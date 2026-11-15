<?php

namespace Tests\Feature;

use App\Models\Batch_Stock;
use App\Models\Loading;
use App\Models\LoadListItem;
use App\Models\Product;
use App\Models\Route;
use App\Models\Supplier;
use App\Models\SupplierInvoice;
use App\Models\Truck;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class DashboardOverviewTest extends TestCase
{
    use RefreshDatabase;

    private function seedData(): void
    {
        $supplier = Supplier::create(['name' => 'S', 'contactno' => '011', 'address' => 'Colombo']);
        $invoice = SupplierInvoice::create([
            'invoice_number' => 'INV-1',
            'invoice_date' => now()->toDateString(),
            'total_bill_amount' => 1000,
            'supplier_id' => $supplier->id,
        ]);
        $truck = Truck::create(['licence_plate_no' => 'AB-1234']);
        $route = Route::create(['route_code' => 'R1', 'route_description' => 'Route one']);

        // Stock levels: none, low, exactly at limit, above limit.
        $levels = [0, 10, 50, 51, 400];
        // Expiry dates: expired, soon, at window edge, far away, none.
        $expiries = [-5, 10, 90, 200, null];
        $batches = [];
        foreach ($levels as $i => $qty) {
            $product = Product::create([
                'material_code' => "MAT-$i",
                'barcode' => "BC-$i",
                'name' => "Product $i",
                'supplier_id' => $supplier->id,
            ]);
            if ($qty === 0) {
                continue;
            }
            $batches[] = Batch_Stock::create([
                'product_id' => $product->id,
                'supplier_invoice_id' => $invoice->id,
                'no_cases' => 1,
                'pack_size' => 1,
                'remain_qty' => $qty,
                'retail_price' => 10,
                'netprice' => 8,
                'expiry_date' => $expiries[$i] === null ? null : now()->addDays($expiries[$i])->toDateString(),
            ]);
        }

        foreach (['pending', 'delivered', 'not_delivered', 'pending'] as $i => $status) {
            $loading = Loading::create([
                'load_number' => "L-$i",
                'truck_id' => $truck->id,
                'route_id' => $route->id,
                'loading_date' => now()->subDays($i * 3)->toDateString(),
                'status' => $status,
            ]);
            LoadListItem::create(['loading_id' => $loading->id, 'batch_id' => $batches[0]->id, 'qty' => 3 + $i]);
            LoadListItem::create(['loading_id' => $loading->id, 'batch_id' => $batches[1]->id, 'qty' => 2]);
        }
    }

    public function test_overview_requires_management_role(): void
    {
        Sanctum::actingAs(User::factory()->create(['role' => 'cashier']));
        $this->getJson('/api/dashboard/overview')->assertStatus(403);
    }

    public function test_overview_matches_what_the_dashboard_used_from_the_full_lists(): void
    {
        Sanctum::actingAs(User::factory()->create(['role' => 'admin']));
        $this->seedData();

        $overview = $this->getJson('/api/dashboard/overview?limit=500')->assertOk()->json();

        // Low stock: same products the page filtered out of /products.
        $oldLow = collect($this->getJson('/api/products')->json())
            ->filter(fn ($p) => $p['total_units'] <= 50)
            ->map(fn ($p) => [$p['id'], $p['total_units']])->values()->all();
        $newLow = collect($overview['products'])
            ->map(fn ($p) => [$p['id'], $p['total_units']])->values()->all();
        $this->assertSame($oldLow, $newLow);
        $this->assertCount(3, $newLow);

        // Expiring: every batch the page showed before is still sent.
        $limit = now()->startOfDay()->addDays(90);
        $oldExpiring = collect($this->getJson('/api/batch-stocks')->json())
            ->filter(fn ($b) => $b['expiry_date'] && $b['remain_qty'] > 0 && now()->parse($b['expiry_date']) <= $limit)
            ->pluck('id')->sort()->values()->all();
        $newIds = collect($overview['batches'])->pluck('id')->all();
        foreach ($oldExpiring as $id) {
            $this->assertContains($id, $newIds);
        }
        $this->assertArrayHasKey('name', $overview['batches'][0]['product']);

        // Dispatches: same records, same order, same item quantities.
        $old = collect($this->getJson('/api/loadings?limit=500')->json());
        $new = collect($overview['loadings']);
        $shape = fn ($l) => [
            $l['id'], $l['load_number'], $l['status'], $l['loading_date'],
            $l['truck']['licence_plate_no'], $l['route']['route_code'],
            count($l['loading_items']), collect($l['loading_items'])->sum('qty'),
        ];
        $this->assertSame($old->map($shape)->all(), $new->map($shape)->all());
        $this->assertCount(4, $new);
    }
}
