<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\SupplierInvoice;
use App\Models\Batch_Stock;
use App\Models\Sale;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Laravel\Sanctum\Sanctum;

class PosCheckoutTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
    }

    public function test_unauthenticated_request_is_rejected(): void
    {
        $response = $this->getJson('/api/sales');
        $response->assertStatus(401);
    }

    public function test_user_can_be_created_with_cashier_role(): void
    {
        $response = $this->postJson('/api/register', [
            'name' => 'Cashier User',
            'username' => 'cashier123',
            'email' => 'cashier@example.com',
            'phone' => '0771234567',
            'password' => 'password123',
            'role' => 'cashier',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure(['access_token', 'token_type', 'user'])
            ->assertJsonPath('user.role', 'cashier');

        $this->assertDatabaseHas('users', [
            'username' => 'cashier123',
            'role' => 'cashier',
        ]);
    }

    public function test_successful_pos_checkout_updates_inventory(): void
    {
        $cashier = User::factory()->create(['role' => 'cashier']);
        Sanctum::actingAs($cashier);

        $supplier = Supplier::create([
            'name' => 'Test Supplier',
            'contactno' => '0112345678',
            'address' => 'Colombo',
        ]);

        $product = Product::create([
            'material_code' => 'MAT-101',
            'barcode' => '8901234567890',
            'name' => 'Test Product',
            'supplier_id' => $supplier->id,
        ]);

        $invoice = SupplierInvoice::create([
            'invoice_number' => 'INV-2026-01',
            'invoice_date' => now()->toDateString(),
            'total_bill_amount' => 1000.00,
            'supplier_id' => $supplier->id,
        ]);

        $batch = Batch_Stock::create([
            'product_id' => $product->id,
            'supplier_invoice_id' => $invoice->id,
            'no_cases' => 10,
            'pack_size' => 5,
            'remain_qty' => 50,
            'retail_price' => 150.00,
            'netprice' => 100.00,
            'expiry_date' => now()->addYear()->toDateString(),
        ]);

        $saleData = [
            'date_time' => now()->toDateTimeString(),
            'user_id' => $cashier->id,
            'total' => 750.00,
            'discount' => 0,
            'payment_type' => 'cash',
            'items' => [
                [
                    'product_id' => $product->id,
                    'batch_id' => $batch->id,
                    'qty' => 5,
                    'retail_price' => 150.00,
                    'unit_price' => 150.00,
                    'total' => 750.00,
                    'discount' => 0,
                ],
            ],
        ];

        $response = $this->postJson('/api/sales', $saleData);

        $response->assertStatus(201)
            ->assertJsonPath('success', true);

        $this->assertDatabaseHas('sales', [
            'user_id' => $cashier->id,
            'total' => 750.00,
            'payment_type' => 'cash',
        ]);

        $this->assertDatabaseHas('sale_items', [
            'product_id' => $product->id,
            'batch_id' => $batch->id,
            'qty' => 5,
        ]);

        $batch->refresh();
        $this->assertEquals(45, $batch->remain_qty);
    }

    public function test_insufficient_stock_causes_transaction_rollback(): void
    {
        $cashier = User::factory()->create(['role' => 'cashier']);
        Sanctum::actingAs($cashier);

        $supplier = Supplier::create(['name' => 'Supplier B']);
        $product = Product::create([
            'material_code' => 'MAT-102',
            'barcode' => '8901234567891',
            'name' => 'Limited Stock Item',
            'supplier_id' => $supplier->id,
        ]);
        $invoice = SupplierInvoice::create([
            'invoice_number' => 'INV-2026-02',
            'invoice_date' => now()->toDateString(),
            'total_bill_amount' => 500.00,
            'supplier_id' => $supplier->id,
        ]);
        $batch = Batch_Stock::create([
            'product_id' => $product->id,
            'supplier_invoice_id' => $invoice->id,
            'no_cases' => 2,
            'pack_size' => 5,
            'remain_qty' => 10,
            'retail_price' => 50.00,
            'netprice' => 30.00,
        ]);

        $saleData = [
            'date_time' => now()->toDateTimeString(),
            'user_id' => $cashier->id,
            'total' => 1000.00,
            'payment_type' => 'cash',
            'items' => [
                [
                    'product_id' => $product->id,
                    'batch_id' => $batch->id,
                    'qty' => 20, // Requesting more than available 10
                    'retail_price' => 50.00,
                    'unit_price' => 50.00,
                    'total' => 1000.00,
                ],
            ],
        ];

        $response = $this->postJson('/api/sales', $saleData);

        $response->assertStatus(500)
            ->assertJsonPath('success', false);

        $this->assertDatabaseCount('sales', 0);
        $this->assertDatabaseCount('sale_items', 0);

        $batch->refresh();
        $this->assertEquals(10, $batch->remain_qty);
    }

    public function test_returned_stock_priority_deduction(): void
    {
        $cashier = User::factory()->create(['role' => 'cashier']);
        Sanctum::actingAs($cashier);

        $supplier = Supplier::create(['name' => 'Supplier C']);
        $product = Product::create([
            'material_code' => 'MAT-103',
            'barcode' => '8901234567892',
            'name' => 'Returned Stock Item',
            'supplier_id' => $supplier->id,
        ]);
        $invoice = SupplierInvoice::create([
            'invoice_number' => 'INV-2026-03',
            'invoice_date' => now()->toDateString(),
            'total_bill_amount' => 500.00,
            'supplier_id' => $supplier->id,
        ]);
        $batch = Batch_Stock::create([
            'product_id' => $product->id,
            'supplier_invoice_id' => $invoice->id,
            'no_cases' => 5,
            'pack_size' => 10,
            'remain_qty' => 50,
            'returned_qty' => 10, // Returned stock available
            'retail_price' => 100.00,
            'netprice' => 70.00,
        ]);

        $saleData = [
            'date_time' => now()->toDateTimeString(),
            'user_id' => $cashier->id,
            'total' => 600.00,
            'payment_type' => 'card',
            'items' => [
                [
                    'product_id' => $product->id,
                    'batch_id' => $batch->id,
                    'qty' => 6, // Should deduct 6 from returned_qty (leaving 4), and 6 from remain_qty (leaving 44)
                    'retail_price' => 100.00,
                    'unit_price' => 100.00,
                    'total' => 600.00,
                ],
            ],
        ];

        $response = $this->postJson('/api/sales', $saleData);
        $response->assertStatus(201);

        $batch->refresh();
        $this->assertEquals(4, $batch->returned_qty);
        $this->assertEquals(44, $batch->remain_qty);
    }

    public function test_deleting_sale_restores_stock_to_wms(): void
    {
        $cashier = User::factory()->create(['role' => 'cashier']);
        Sanctum::actingAs($cashier);

        $supplier = Supplier::create(['name' => 'Supplier D']);
        $product = Product::create([
            'material_code' => 'MAT-104',
            'barcode' => '8901234567893',
            'name' => 'Revert Stock Item',
            'supplier_id' => $supplier->id,
        ]);
        $invoice = SupplierInvoice::create([
            'invoice_number' => 'INV-2026-04',
            'invoice_date' => now()->toDateString(),
            'total_bill_amount' => 500.00,
            'supplier_id' => $supplier->id,
        ]);
        $batch = Batch_Stock::create([
            'product_id' => $product->id,
            'supplier_invoice_id' => $invoice->id,
            'no_cases' => 5,
            'pack_size' => 10,
            'remain_qty' => 50,
            'retail_price' => 200.00,
            'netprice' => 150.00,
        ]);

        $sale = Sale::create([
            'date_time' => now()->toDateTimeString(),
            'user_id' => $cashier->id,
            'total' => 600.00,
            'discount' => 0,
            'payment_type' => 'cash',
        ]);

        $sale->items()->create([
            'batch_id' => $batch->id,
            'product_id' => $product->id,
            'qty' => 3,
            'retail_price' => 200.00,
            'unit_price' => 200.00,
            'total' => 600.00,
        ]);

        // Stock was 50, sale took 3 (making it 47)
        $batch->decrement('remain_qty', 3);
        $this->assertEquals(47, $batch->fresh()->remain_qty);

        // Delete/Void sale via API
        $response = $this->deleteJson("/api/sales/{$sale->id}");
        $response->assertStatus(200)
            ->assertJsonPath('success', true);

        // Assert stock is restored to 50
        $this->assertEquals(50, $batch->fresh()->remain_qty);
        $this->assertDatabaseMissing('sales', ['id' => $sale->id]);
    }

    public function test_product_search_by_barcode_and_name(): void
    {
        $cashier = User::factory()->create(['role' => 'cashier']);
        Sanctum::actingAs($cashier);

        $supplier = Supplier::create(['name' => 'Supplier E']);
        $product = Product::create([
            'material_code' => 'MAT-200',
            'barcode' => '9998887776665',
            'name' => 'Barcode Searchable Item',
            'supplier_id' => $supplier->id,
        ]);
        $invoice = SupplierInvoice::create([
            'invoice_number' => 'INV-2026-05',
            'invoice_date' => now()->toDateString(),
            'total_bill_amount' => 500.00,
            'supplier_id' => $supplier->id,
        ]);
        Batch_Stock::create([
            'product_id' => $product->id,
            'supplier_invoice_id' => $invoice->id,
            'no_cases' => 2,
            'pack_size' => 10,
            'remain_qty' => 20,
            'retail_price' => 80.00,
            'netprice' => 50.00,
        ]);

        // Search by barcode
        $response = $this->getJson('/api/products/search?query=9998887776665');
        $response->assertStatus(200)
            ->assertJsonCount(1)
            ->assertJsonPath('0.material_code', 'MAT-200');

        // Search by product name keyword
        $response2 = $this->getJson('/api/products/search?query=Searchable');
        $response2->assertStatus(200)
            ->assertJsonCount(1)
            ->assertJsonPath('0.barcode', '9998887776665');
    }
}
