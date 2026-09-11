<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Supplier;
use App\Models\Product;
use App\Models\SupplierInvoice;
use App\Models\Batch_Stock;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Admin user
        User::updateOrCreate(
            ['username' => 'admin'],
            [
                'name'     => 'System Admin',
                'email'    => 'admin@ims.com',
                'phone'    => '0770000000',
                'password' => Hash::make('password123'),
                'role'     => 'admin',
            ]
        );

        // Cashier user (username: cashier, pw: password123)
        User::updateOrCreate(
            ['username' => 'cashier'],
            [
                'name'     => 'Cashier User',
                'email'    => 'cashier@ims.com',
                'phone'    => '0771234568',
                'password' => Hash::make('password123'),
                'role'     => 'cashier',
            ]
        );

        // Cashier1 user (username: cashier1, pw: password123)
        User::updateOrCreate(
            ['username' => 'cashier1'],
            [
                'name'     => 'Cashier User 1',
                'email'    => 'cashier1@ims.com',
                'phone'    => '0771234567',
                'password' => Hash::make('password123'),
                'role'     => 'cashier',
            ]
        );

        // Sample Supplier, Products & Stock Batches for POS testing
        $supplier = Supplier::firstOrCreate(
            ['name' => 'Lanka Distributors'],
            ['contactno' => '0112345678', 'address' => 'Colombo']
        );

        $invoice = SupplierInvoice::firstOrCreate(
            ['invoice_number' => 'INV-2026-001'],
            ['invoice_date' => now()->toDateString(), 'total_bill_amount' => 5000.00, 'supplier_id' => $supplier->id]
        );

        $product1 = Product::firstOrCreate(
            ['material_code' => 'MAT-101'],
            ['barcode' => '8901234567890', 'name' => 'Anchor Milk Powder 400g', 'supplier_id' => $supplier->id]
        );

        Batch_Stock::firstOrCreate(
            ['product_id' => $product1->id, 'supplier_invoice_id' => $invoice->id],
            [
                'no_cases' => 10,
                'pack_size' => 5,
                'remain_qty' => 50,
                'retail_price' => 1250.00,
                'netprice' => 1100.00,
                'expiry_date' => now()->addMonths(6)->toDateString(),
            ]
        );

        $product2 = Product::firstOrCreate(
            ['material_code' => 'MAT-102'],
            ['barcode' => '8901234567891', 'name' => 'Maliban Cream Cracker 500g', 'supplier_id' => $supplier->id]
        );

        Batch_Stock::firstOrCreate(
            ['product_id' => $product2->id, 'supplier_invoice_id' => $invoice->id],
            [
                'no_cases' => 15,
                'pack_size' => 10,
                'remain_qty' => 150,
                'retail_price' => 380.00,
                'netprice' => 320.00,
                'expiry_date' => now()->addYear()->toDateString(),
            ]
        );
    }
}
