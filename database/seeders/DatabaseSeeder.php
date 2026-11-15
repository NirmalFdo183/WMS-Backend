<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        // ─── Disable FK checks & truncate all tables ───────────────────────
        DB::statement('SET FOREIGN_KEY_CHECKS=0');

        $tables = [
            'personal_access_tokens',
            'sale_items',
            'sales',
            'loading_returns',
            'load_list_items',
            'loadings',
            'shops',
            'sales_reps',
            'employees',
            'trucks',
            'routes',
            'batch__stocks',
            'supplier_invoices',
            'products',
            'suppliers',
            'users',
        ];

        foreach ($tables as $table) {
            DB::table($table)->truncate();
        }

        // ══════════════════════════════════════════════════════════════════
        // 1. USERS (1 super_admin, 1 admin, 2 cashiers)
        // ══════════════════════════════════════════════════════════════════
        DB::table('users')->insert([
            [
                'name'       => 'System Owner',
                'username'   => 'superadmin',
                'email'      => 'superadmin@wms.com',
                'password'   => Hash::make('super123'),
                'phone'      => '0770000001',
                'address'    => 'Head Office, Colombo',
                'role'       => 'super_admin',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name'       => 'Warehouse Admin',
                'username'   => 'admin',
                'email'      => 'fnirmal269@gmail.com',
                'password'   => Hash::make('admin123'),
                'phone'      => '0770000000',
                'address'    => 'Warehouse HQ, Peliyagoda',
                'role'       => 'admin',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name'       => 'Kasun Perera',
                'username'   => 'cashier1',
                'email'      => 'cashier1@wms.com',
                'password'   => Hash::make('cashier123'),
                'phone'      => '0771000001',
                'address'    => 'Colombo 03',
                'role'       => 'cashier',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name'       => 'Nadeeka Silva',
                'username'   => 'cashier2',
                'email'      => 'cashier2@wms.com',
                'password'   => Hash::make('cashier123'),
                'phone'      => '0771000002',
                'address'    => 'Dehiwala',
                'role'       => 'cashier',
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);

        // ══════════════════════════════════════════════════════════════════
        // 2. SUPPLIERS (5 suppliers)
        // ══════════════════════════════════════════════════════════════════
        DB::table('suppliers')->insert([
            ['id' => 1, 'name' => 'Hemas Holdings PLC',  'contactno' => '0112345678', 'address' => '161 Vinayalankara Mw, Colombo 10',         'created_at' => $now, 'updated_at' => $now],
            ['id' => 2, 'name' => 'Ceylon Biscuits Ltd',  'contactno' => '0112456789', 'address' => '103 Sirimavo Bandaranaike Mw, Colombo 14', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 3, 'name' => 'Maliban Biscuit Mfg',  'contactno' => '0112567890', 'address' => 'Maliban Building, Hendala, Wattala',        'created_at' => $now, 'updated_at' => $now],
            ['id' => 4, 'name' => 'Nestle Lanka PLC',     'contactno' => '0112678901', 'address' => '440 Nawala Rd, Rajagiriya',                 'created_at' => $now, 'updated_at' => $now],
            ['id' => 5, 'name' => 'Unilever Lanka Ltd',   'contactno' => '0112789012', 'address' => '258 Madampitiya Rd, Colombo 15',            'created_at' => $now, 'updated_at' => $now],
        ]);

        // ══════════════════════════════════════════════════════════════════
        // 3. PRODUCTS (83, 55, 65, 78, 99 from 5 suppliers = 380 products)
        // ══════════════════════════════════════════════════════════════════
        $supplierSpecs = [
            1 => [
                'prefix' => 'HEM',
                'count'  => 83,
                'brands' => ['Baby Cheramy', 'Clogard', 'Kumarika', 'Velvet', 'Fems', 'Diva', 'Goya', 'Protex'],
                'items'  => ['Soap', 'Shampoo', 'Cologne', 'Moisturizing Cream', 'Baby Powder', 'Hair Oil', 'Toothpaste', 'Mouthwash', 'Body Wash', 'Beauty Bar', 'Detergent Powder', 'Conditioner'],
                'sizes'  => ['70g', '100g', '125g', '150g', '200ml', '250ml', '400ml', '500ml'],
                'base_p' => 90.0,
            ],
            2 => [
                'prefix' => 'CBL',
                'count'  => 55,
                'brands' => ['Munchee', 'Tiara', 'Ritzbury', 'Cecil', 'Samaposha', 'Lanka Soy'],
                'items'  => ['Super Cream Cracker', 'Lemon Puff', 'Chocolate Cream', 'Hawaiian Cookies', 'Ginger Biscuit', 'Tikiri Marie', 'Milk Short Cake', 'Karapincha Bites', 'Swiss Roll', 'Fruit Juice', 'Pebbles Chocolate', 'Soya Meat Curry'],
                'sizes'  => ['80g', '100g', '150g', '200g', '350g', '400g', '500g', '1L'],
                'base_p' => 110.0,
            ],
            3 => [
                'prefix' => 'MAL',
                'count'  => 65,
                'brands' => ['Maliban', 'Little Lion', 'Tea Time'],
                'items'  => ['Real Chocolate Biscuit', 'Cream Cracker Classic', 'Cheese Bits', 'Custard Cream', 'Smarties Treat', 'Gold Marie', 'Bran Cracker Health', 'Lemon Puff Crispy', 'Ginger Biscuit Spicy', 'Orange Cream Drop', 'Wafer Vanilla Delight', 'Krisco Snack'],
                'sizes'  => ['60g', '100g', '120g', '170g', '200g', '300g', '400g', '500g'],
                'base_p' => 105.0,
            ],
            4 => [
                'prefix' => 'NES',
                'count'  => 78,
                'brands' => ['Nestle Milo', 'Nespray', 'Nescafe', 'Maggi', 'Nestomalt', 'Cerelac', 'KitKat', 'Milkmaid'],
                'items'  => ['Milo Energy Tin', 'Milo RTD Pack', 'Everyday Milk Powder', 'Classic Coffee Jar', '3-in-1 Coffee Mix', '2-Minute Noodles Chicken', '2-Minute Noodles Curry', 'Coconut Milk Powder', 'Actigen Malt Tin', 'Sweet Condensed Milk', 'Chocolate 4-Finger'],
                'sizes'  => ['75g', '100g', '150g', '200g', '300g', '400g', '1kg', '180ml'],
                'base_p' => 220.0,
            ],
            5 => [
                'prefix' => 'UNI',
                'count'  => 99,
                'brands' => ['Sunlight', 'Lifebuoy', 'Signal', 'Sunsilk', 'Lux', 'Astra', 'Knorr', 'Vim', 'Surf Excel', 'Comfort', 'Vaseline', 'Pears'],
                'items'  => ['Washing Soap Bar', 'Detergent Powder Clean', 'Germ Protection Soap', 'Total Handwash Bottle', 'Strong Teeth Toothpaste', 'Black Shine Shampoo', 'Rose Radiance Soap', 'Table Margarine Tub', 'Chicken Broth Cubes', 'Dishwash Bar Lemon', 'Fabric Conditioner'],
                'sizes'  => ['75g', '100g', '110g', '120g', '160g', '200g', '250g', '400g', '500g', '1kg'],
                'base_p' => 135.0,
            ],
        ];

        $productRows = [];
        $productIndex = 1;
        $supplierProducts = []; // supplier_id => array of product_id
        $productPriceMap  = []; // product_id => retail_price

        foreach ($supplierSpecs as $supplierId => $spec) {
            $supplierProducts[$supplierId] = [];
            $prefix = $spec['prefix'];
            $count  = $spec['count'];
            $brands = $spec['brands'];
            $items  = $spec['items'];
            $sizes  = $spec['sizes'];
            $baseP  = $spec['base_p'];

            for ($i = 1; $i <= $count; $i++) {
                $code = $prefix . '-' . str_pad($i, 3, '0', STR_PAD_LEFT);
                $barcode = '890' . str_pad($supplierId, 2, '0', STR_PAD_LEFT) . str_pad($i, 7, '0', STR_PAD_LEFT);
                $brand = $brands[($i - 1) % count($brands)];
                $item  = $items[(int)(($i - 1) / count($brands)) % count($items)];
                $size  = $sizes[($i * 3 + $supplierId) % count($sizes)];
                $name  = "{$brand} {$item} {$size}";

                $retail = round($baseP + (($i * 13 + $supplierId * 17) % 350), 2);
                $productPriceMap[$productIndex] = $retail;
                $supplierProducts[$supplierId][] = $productIndex;

                $productRows[] = [
                    'id'            => $productIndex,
                    'material_code' => $code,
                    'barcode'       => $barcode,
                    'name'          => $name,
                    'supplier_id'   => $supplierId,
                    'created_at'    => $now,
                    'updated_at'    => $now,
                ];

                $productIndex++;
            }
        }

        foreach (array_chunk($productRows, 100) as $chunk) {
            DB::table('products')->insert($chunk);
        }

        // ══════════════════════════════════════════════════════════════════
        // 4. SUPPLIER INVOICES & BATCH STOCKS
        //    Supplies from October 15 to November 16
        //    All 380 products receive batch stock in Week 1 (Oct 15).
        //    Subsequent weekly replenishment dates replenish active supplies.
        // ══════════════════════════════════════════════════════════════════
        $supplyDates = [
            '2026-10-15', '2026-10-19', '2026-10-26',
            '2026-11-02', '2026-11-09', '2026-11-16',
        ];
        $packSize    = 12;
        $defaultExpiryDate = '2027-08-31';

        // 2 designated critically empty products (0 units)
        $criticallyEmptyProductIds = [20, 95];

        // 13 designated low-stock products (< 50 units) across the 5 suppliers
        $lowStockMap = [
            5   => 14, // Hemas Baby Soap 125g
            18  => 22, // Hemas Clogard Toothpaste 100g
            32  => 35, // Hemas Velvet Beauty Bar 150g
            88  => 18, // CBL Munchee Super Cream Cracker 150g
            102 => 28, // CBL Tiara Swiss Roll 200g
            115 => 42, // CBL Ritzbury Chocolate 100g
            145 => 15, // Maliban Real Chocolate 120g
            160 => 25, // Maliban Cream Cracker 200g
            175 => 38, // Maliban Cheese Bits 100g
            215 => 12, // Nestle Milo Tin 400g
            240 => 30, // Nestle Maggi Noodles Chicken 75g
            295 => 20, // Unilever Sunlight Soap Bar 110g
            330 => 45, // Unilever Lifebuoy Handwash 200ml
        ];

        // Exactly 13 batches expiring soon (within 90 days of today, expiry <= 2027-02-13)
        // Products: 1, 2, 3, 4, 6, 7, 8, 9, 10, 11, 12, 13, 14 (13 items, none empty)
        $expiringProductIds = [1, 2, 3, 4, 6, 7, 8, 9, 10, 11, 12, 13, 14];
        $expiringDates = [
            '2026-11-22',
            '2026-11-28',
            '2026-12-05',
            '2026-12-12',
            '2026-12-19',
            '2026-12-28',
            '2027-01-04',
            '2027-01-11',
            '2027-01-18',
            '2027-01-25',
            '2027-02-02',
            '2027-02-07',
            '2027-02-11',
        ];
        $productExpiryMap = array_combine($expiringProductIds, $expiringDates);

        $invoiceRows    = [];
        $batchStockRows = [];
        $invoiceIdCounter = 1;
        $latestBatchPerProduct = []; // product_id => batch_id
        $batchIdCounter = 1;

        foreach ($supplyDates as $weekIdx => $supplyDate) {
            $weekNum = $weekIdx + 1;
            $dateObj = Carbon::parse($supplyDate);
            $yyyy    = $dateObj->format('Y');
            $mm      = $dateObj->format('m');
            $dd      = $dateObj->format('d');

            for ($supplierId = 1; $supplierId <= 5; $supplierId++) {
                $prods = $supplierProducts[$supplierId];
                // In week 1, supply all products for this supplier.
                // In weeks 2-11, supply a rotating half of the supplier's products.
                if ($weekNum === 1) {
                    $selectedProds = $prods;
                } else {
                    $halfCount = (int)ceil(count($prods) / 2);
                    $offset = (($weekNum - 2) * 15) % count($prods);
                    $selectedProds = [];
                    for ($k = 0; $k < $halfCount; $k++) {
                        $selectedProds[] = $prods[($offset + $k) % count($prods)];
                    }
                }

                $invoiceTotal = 0;
                $currentInvoiceBatches = [];

                foreach ($selectedProds as $pId) {
                    $isCriticallyEmpty = in_array($pId, $criticallyEmptyProductIds, true);
                    $isLowStock         = isset($lowStockMap[$pId]);

                    // If it is critically empty or low-stock, do not restock in subsequent weeks
                    if (($isCriticallyEmpty || $isLowStock) && $weekNum > 1) {
                        continue;
                    }

                    if ($isCriticallyEmpty) {
                        $noCases   = 0;
                        $remainQty = 0;
                    } elseif ($isLowStock) {
                        $targetUnits = $lowStockMap[$pId];
                        $noCases     = (int) ceil($targetUnits / $packSize);
                        $remainQty   = $targetUnits;
                    } else {
                        $noCases   = 15 + (($pId * 7 + $weekNum * 11) % 35); // 15 to 49 cases
                        $remainQty = $noCases * $packSize;
                    }

                    $retail    = $productPriceMap[$pId];
                    $netPrice  = round($retail * 0.75, 2);
                    $lineTotal = $remainQty * $netPrice;
                    $invoiceTotal += $lineTotal;

                    // Set expiry date: only week 1 batches for the 13 chosen products get expiring soon dates
                    $batchExpiry = ($weekNum === 1 && isset($productExpiryMap[$pId]))
                        ? $productExpiryMap[$pId]
                        : $defaultExpiryDate;

                    $currentInvoiceBatches[] = [
                        'id'                  => $batchIdCounter,
                        'product_id'          => $pId,
                        'supplier_invoice_id' => $invoiceIdCounter,
                        'no_cases'            => $noCases,
                        'pack_size'           => $packSize,
                        'extra_units'         => 0,
                        'remain_qty'          => $remainQty,
                        'free_qty'            => 0,
                        'retail_price'        => $retail,
                        'netprice'            => $netPrice,
                        'expiry_date'         => $batchExpiry,
                        'returned_qty'        => 0,
                        'created_at'          => $now,
                        'updated_at'          => $now,
                    ];

                    $latestBatchPerProduct[$pId] = $batchIdCounter;
                    $batchIdCounter++;
                }

                $invoiceRows[] = [
                    'id'                => $invoiceIdCounter,
                    'supplier_id'       => $supplierId,
                    'invoice_number'    => "INV-{$yyyy}{$mm}{$dd}-S{$supplierId}",
                    'invoice_date'      => $supplyDate,
                    'total_bill_amount' => round($invoiceTotal, 2),
                    'created_at'        => $now,
                    'updated_at'        => $now,
                ];

                foreach ($currentInvoiceBatches as $b) {
                    $batchStockRows[] = $b;
                }

                $invoiceIdCounter++;
            }
        }

        DB::table('supplier_invoices')->insert($invoiceRows);

        foreach (array_chunk($batchStockRows, 200) as $chunk) {
            DB::table('batch__stocks')->insert($chunk);
        }

        // ══════════════════════════════════════════════════════════════════
        // 5. ROUTES (6 routes)
        // ══════════════════════════════════════════════════════════════════
        DB::table('routes')->insert([
            ['id' => 1, 'route_code' => 'RT-001', 'route_description' => 'Colombo North Route',   'created_at' => $now, 'updated_at' => $now],
            ['id' => 2, 'route_code' => 'RT-002', 'route_description' => 'Colombo South Route',   'created_at' => $now, 'updated_at' => $now],
            ['id' => 3, 'route_code' => 'RT-003', 'route_description' => 'Gampaha East Route',    'created_at' => $now, 'updated_at' => $now],
            ['id' => 4, 'route_code' => 'RT-004', 'route_description' => 'Gampaha West Route',    'created_at' => $now, 'updated_at' => $now],
            ['id' => 5, 'route_code' => 'RT-005', 'route_description' => 'Kalutara North Route',  'created_at' => $now, 'updated_at' => $now],
            ['id' => 6, 'route_code' => 'RT-006', 'route_description' => 'Negombo Coastal Route', 'created_at' => $now, 'updated_at' => $now],
        ]);

        // ══════════════════════════════════════════════════════════════════
        // 6. TRUCKS (6 trucks)
        // ══════════════════════════════════════════════════════════════════
        $truckPlates = ['CAB-1234', 'CAB-2345', 'CAB-3456', 'CAB-4567', 'CAB-5678', 'CAB-6789'];
        $truckRows   = [];
        foreach ($truckPlates as $idx => $plate) {
            $truckRows[] = [
                'id'               => $idx + 1,
                'licence_plate_no' => $plate,
                'description'      => 'Delivery Truck',
                'created_at'       => $now,
                'updated_at'       => $now,
            ];
        }
        DB::table('trucks')->insert($truckRows);

        // ══════════════════════════════════════════════════════════════════
        // 7. EMPLOYEES (22 dispatch team: 6 drivers, 6 helpers, 6 cash collectors, 4 warehouse)
        // ══════════════════════════════════════════════════════════════════
        DB::table('employees')->insert([
            // Drivers (IDs 1–6)
            ['id' => 1,  'name' => 'Suresh Perera',       'nic' => '198512345678V', 'phoneno' => '0712001001', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 2,  'name' => 'Chaminda Silva',      'nic' => '198612345679V', 'phoneno' => '0712001002', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 3,  'name' => 'Ranjith Fernando',    'nic' => '198712345680V', 'phoneno' => '0712001003', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 4,  'name' => 'Lasitha Jayaweera',   'nic' => '198812345681V', 'phoneno' => '0712001004', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 5,  'name' => 'Pradeep Wijesinghe',  'nic' => '198912345682V', 'phoneno' => '0712001005', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 6,  'name' => 'Mahesh Bandara',      'nic' => '199012345683V', 'phoneno' => '0712001006', 'created_at' => $now, 'updated_at' => $now],
            // Helpers (IDs 7–12)
            ['id' => 7,  'name' => 'Nuwan Rathnayake',    'nic' => '199112345684V', 'phoneno' => '0712002001', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 8,  'name' => 'Kasun Dissanayake',   'nic' => '199212345685V', 'phoneno' => '0712002002', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 9,  'name' => 'Dilan Gunasekara',    'nic' => '199312345686V', 'phoneno' => '0712002003', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 10, 'name' => 'Chathura Liyanage',   'nic' => '199412345687V', 'phoneno' => '0712002004', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 11, 'name' => 'Isuru Madushanka',    'nic' => '199512345688V', 'phoneno' => '0712002005', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 12, 'name' => 'Hirantha Senanayake', 'nic' => '199612345689V', 'phoneno' => '0712002006', 'created_at' => $now, 'updated_at' => $now],
            // Cash Collectors (IDs 13–18)
            ['id' => 13, 'name' => 'Anjali Kumari',           'nic' => '199212345690V', 'phoneno' => '0712003001', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 14, 'name' => 'Sanduni Wickramasinghe',  'nic' => '199312345691V', 'phoneno' => '0712003002', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 15, 'name' => 'Sachini Herath',          'nic' => '199412345692V', 'phoneno' => '0712003003', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 16, 'name' => 'Dilhani Rajapaksha',      'nic' => '199512345693V', 'phoneno' => '0712003004', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 17, 'name' => 'Chamari Seneviratne',     'nic' => '199612345694V', 'phoneno' => '0712003005', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 18, 'name' => 'Piyumi Tennakoon',        'nic' => '199712345695V', 'phoneno' => '0712003006', 'created_at' => $now, 'updated_at' => $now],
            // Warehouse Helpers (IDs 19–22)
            ['id' => 19, 'name' => 'Ruwan Kumara',   'nic' => '199312345696V', 'phoneno' => '0712004001', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 20, 'name' => 'Thilina Prasad', 'nic' => '199412345697V', 'phoneno' => '0712004002', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 21, 'name' => 'Gayan Sampath',  'nic' => '199512345698V', 'phoneno' => '0712004003', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 22, 'name' => 'Maduka Lakmal',  'nic' => '199612345699V', 'phoneno' => '0712004004', 'created_at' => $now, 'updated_at' => $now],
        ]);

        // ══════════════════════════════════════════════════════════════════
        // 8. SALES REPS (25 reps: 5 per route group + 2 managing route 6 = 27 rows)
        // ══════════════════════════════════════════════════════════════════
        $joinDate = '2026-01-15';
        $repGroups = [
            ['CN', 1, [
                ['Nuwan Perera',      '0761100101'],
                ['Sachith Kumara',    '0761100102'],
                ['Ruwan Perera',      '0761100103'],
                ['Dinesh Fernando',   '0761100104'],
                ['Lasitha Silva',     '0761100105'],
            ]],
            ['CS', 2, [
                ['Chathuri Perera',   '0761100201'],
                ['Sanduni Kumari',    '0761100202'],
                ['Malith Wijesinghe', '0761100203'],
                ['Iresha Jayaweera',  '0761100204'],
                ['Dhanushka Rathna',  '0761100205'],
            ]],
            ['GE', 3, [
                ['Hiruni Madushanka', '0761100301'],
                ['Thilina Herath',    '0761100302'],
                ['Prabath Dissan',    '0761100303'],
                ['Saman Gunasekara',  '0761100304'],
                ['Ruwanthika De S',   '0761100305'],
            ]],
            ['GW', 4, [
                ['Kalani Liyanage',   '0761100401'],
                ['Asanka Bandara',    '0761100402'],
                ['Chamara Saman',     '0761100403'],
                ['Nipun Rajapaksha',  '0761100404'],
                ['Dulmini Senarathne','0761100405'],
            ]],
            ['KN', 5, [
                ['Lahiru Tennakoon',  '0761100501'],
                ['Pavithra Perera',   '0761100502'],
                ['Vimukthi Silva',    '0761100503'],
                ['Eranda Fernando',   '0761100504'],
                ['Nishani Kumara',    '0761100505'],
            ]],
        ];

        $salesRepRows = [];
        $globalRepIdx = 0;

        foreach ($repGroups as [$prefix, $routeId, $reps]) {
            foreach ($reps as $idx => [$repName, $contact]) {
                $nn = str_pad($idx + 1, 2, '0', STR_PAD_LEFT);
                $salesRepRows[] = [
                    'rep_id'      => "REP-{$prefix}-{$nn}",
                    'supplier_id' => ($globalRepIdx % 5) + 1,
                    'name'        => $repName,
                    'contact'     => $contact,
                    'join_date'   => $joinDate,
                    'route_id'    => $routeId,
                    'created_at'  => $now,
                    'updated_at'  => $now,
                ];
                $globalRepIdx++;
            }
        }

        // Additional route assignments for 2 reps managing Route 6
        $salesRepRows[] = [
            'rep_id'      => 'REP-NG-01',
            'supplier_id' => 3,
            'name'        => 'Ruwan Perera',
            'contact'     => '0761100103',
            'join_date'   => $joinDate,
            'route_id'    => 6,
            'created_at'  => $now,
            'updated_at'  => $now,
        ];
        $salesRepRows[] = [
            'rep_id'      => 'REP-NG-02',
            'supplier_id' => 2,
            'name'        => 'Sanduni Kumari',
            'contact'     => '0761100202',
            'join_date'   => $joinDate,
            'route_id'    => 6,
            'created_at'  => $now,
            'updated_at'  => $now,
        ];

        DB::table('sales_reps')->insert($salesRepRows);

        // ══════════════════════════════════════════════════════════════════
        // 9. SHOPS (437 shops across 6 routes)
        // ══════════════════════════════════════════════════════════════════
        $routeCodes = ['RT-001', 'RT-002', 'RT-003', 'RT-004', 'RT-005', 'RT-006'];
        $shopCounts = [73, 72, 74, 73, 73, 72];

        $nameTemplates = [
            'Perera Groceries',    'Silva Supermart',      'Lanka Traders',
            'Sunil Stores',        'Nimal Cash & Carry',   'Kumara Provisions',
            'Devi Retail',         'Ravi Mini Mart',        'Saman Boutique',
            'Priya General Store', 'Sandya Enterprises',   'Ruwan Merchants',
            'Chaminda Stores',     'Thilak Traders',        'Pradeep Supermart',
            'Anura Provisions',    'Lasantha Retail',       'Sumudu Stores',
            'Piyumi Boutique',     'Nadeeka Cash & Carry',
        ];

        $shopRows = [];
        foreach ($routeCodes as $rIdx => $routeCode) {
            $count    = $shopCounts[$rIdx];
            $routeNum = $rIdx + 1;
            $codeTag  = str_replace('-', '', $routeCode);

            for ($s = 1; $s <= $count; $s++) {
                $nn       = str_pad($s, 3, '0', STR_PAD_LEFT);
                $template = $nameTemplates[($s - 1) % count($nameTemplates)];
                $phone    = substr('071' . $routeNum . str_pad($s, 4, '0', STR_PAD_LEFT), 0, 10);

                $shopRows[] = [
                    'shop_code'  => "SHP-{$codeTag}-{$nn}",
                    'shop_name'  => "{$template} {$s}",
                    'address'    => 'No. ' . ($s * 2) . ', Route ' . $routeNum . ' Area, Sri Lanka',
                    'phoneno'    => $phone,
                    'route_code' => $routeCode,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
        }

        foreach (array_chunk($shopRows, 100) as $chunk) {
            DB::table('shops')->insert($chunk);
        }

        // ══════════════════════════════════════════════════════════════════
        // 10. LOADINGS + LOAD LIST ITEMS
        //     From 2026-09-01 through 2026-11-15 (76 days)
        //     Each day has a realistic varied number of loadings (1 to 4)
        //     Includes delivered, not_delivered (cancelled/failed), and pending
        // ══════════════════════════════════════════════════════════════════
        $primaryRepId = [1 => 1, 2 => 6, 3 => 11, 4 => 16, 5 => 21, 6 => 26];

        $loadingRows  = [];
        $loadItemRows = [];
        $loadingId    = 1;
        $totalProductsCount = 380;
        $firstLoadingIdByDate = []; // Y-m-d => loading_id

        // Specific loading statuses (key: "YYYY-MM-DD:loading_index_of_day")
        // - 4 pending loadings on recent days (waiting longest first)
        // - cancelled / not_delivered loadings scattered realistically
        $specialStatusMap = [
            '2026-10-25:0' => 'not_delivered',
            '2026-11-03:0' => 'not_delivered',
            '2026-11-08:0' => 'not_delivered',
            '2026-11-13:0' => 'pending',
            '2026-11-14:0' => 'pending',
            '2026-11-15:0' => 'pending',
            '2026-11-16:0' => 'pending',
        ];

        // 33 days loop: from 2026-10-15 to 2026-11-16
        $startDate   = Carbon::create(2026, 10, 15);
        $endDate     = Carbon::create(2026, 11, 16);
        $routeOffset = 0;

        for ($curr = $startDate->copy(); $curr->lte($endDate); $curr->addDay()) {
            $dayFull  = $curr->format('Y-m-d');
            $dayStr   = $curr->format('Ymd');
            $prepFull = $curr->copy()->subDay()->format('Y-m-d');

            // Varied number of loadings per day between 1 and 4
            $numLoadings = ((int) abs(crc32($dayFull)) % 4) + 1; // 1 to 4

            $routesForDay = [];
            for ($k = 0; $k < $numLoadings; $k++) {
                $routesForDay[] = (($routeOffset + $k) % 6) + 1;
            }
            $routeOffset = ($routeOffset + $numLoadings) % 6;

            foreach ($routesForDay as $rIdx => $routeIdx) {
                if ($rIdx === 0) {
                    $firstLoadingIdByDate[$dayFull] = $loadingId;
                }

                $routeCode = $routeCodes[$routeIdx - 1];
                $routeTag  = str_replace('-', '', $routeCode);

                $statusKey = "{$dayFull}:{$rIdx}";
                $status    = $specialStatusMap[$statusKey] ?? 'delivered';

                $loadingRows[] = [
                    'id'                => $loadingId,
                    'load_number'       => "LD-{$dayStr}-{$routeTag}",
                    'truck_id'          => $routeIdx,
                    'route_id'          => $routeIdx,
                    'prepared_date'     => $prepFull,
                    'loading_date'      => $dayFull,
                    'status'            => $status,
                    'driver_id'         => $routeIdx,
                    'helper_id'         => $routeIdx + 6,
                    'cash_collector_id' => $routeIdx + 12,
                    'sales_rep_id'      => $primaryRepId[$routeIdx],
                    'created_at'        => $now,
                    'updated_at'        => $now,
                ];

                // 15 products loaded per loading
                for ($itemIdx = 0; $itemIdx < 15; $itemIdx++) {
                    $pId     = (($loadingId * 13 + $itemIdx * 23) % $totalProductsCount) + 1;
                    $batchId = $latestBatchPerProduct[$pId] ?? 1;
                    $retailP = $productPriceMap[$pId] ?? 100.0;
                    $netP    = round($retailP * 0.75, 2);
                    $qty     = (($loadingId * 7 + $itemIdx * 11) % 35) + 15;

                    $loadItemRows[] = [
                        'loading_id' => $loadingId,
                        'batch_id'   => $batchId,
                        'qty'        => $qty,
                        'free_qty'   => 0,
                        'wh_price'   => $netP,
                        'net_price'  => $netP,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];
                }

                $loadingId++;
            }
        }

        foreach (array_chunk($loadingRows, 60) as $chunk) {
            DB::table('loadings')->insert($chunk);
        }

        foreach (array_chunk($loadItemRows, 200) as $chunk) {
            DB::table('load_list_items')->insert($chunk);
        }

        // ══════════════════════════════════════════════════════════════════
        // 11. LOADING RETURNS (6 returns on random dates between Oct 15 and Nov 16)
        // ══════════════════════════════════════════════════════════════════
        $returnSpec = [
            ['2026-10-18', 15,  6,  'Damaged goods'],
            ['2026-10-23', 45,  8,  'Excess stock'],
            ['2026-10-28', 78,  5,  'Quality issue'],
            ['2026-11-02', 120, 10, 'Customer refusal'],
            ['2026-11-07', 210, 7,  'Temperature concern'],
            ['2026-11-12', 305, 12, 'Wrong delivery'],
        ];

        $returnRows = [];
        foreach ($returnSpec as [$returnDate, $productId, $qty, $reason]) {
            $ldId       = $firstLoadingIdByDate[$returnDate] ?? 1;
            $batchId    = $latestBatchPerProduct[$productId] ?? 1;

            $returnRows[] = [
                'loading_id'  => $ldId,
                'batch_id'    => $batchId,
                'qty'         => $qty,
                'return_date' => $returnDate,
                'reason'      => $reason,
                'created_at'  => $now,
                'updated_at'  => $now,
            ];
        }

        DB::table('loading_returns')->insert($returnRows);

        // ══════════════════════════════════════════════════════════════════
        // 12. POS SALES & SALE ITEMS
        //     From 2026-10-15 to 2026-11-16 (33 days)
        //     Alternating 1-by-1 between 2 cashiers:
        //     - cashier1 (user_id 3) on even day-indices
        //     - cashier2 (user_id 4) on odd day-indices
        // ══════════════════════════════════════════════════════════════════
        $payTypes     = ['cash', 'card'];
        $discountPool = [0, 10, 25, 50];

        $salesRows    = [];
        $saleItemRows = [];
        $saleId       = 1;
        $dayIdx       = 0;

        for ($curr = $startDate->copy(); $curr->lte($endDate); $curr->addDay()) {
            $userId   = ($dayIdx % 2 === 0) ? 3 : 4; // alternating 1-by-1
            $dateStr  = $curr->format('Y-m-d');
            $numSales = (($dayIdx + 7) % 8) + 8; // 8 to 15 sales per day

            for ($sIdx = 0; $sIdx < $numSales; $sIdx++) {
                $hour       = str_pad(9 + ($sIdx % 10), 2, '0', STR_PAD_LEFT);
                $minute     = str_pad(($sIdx * 7) % 60, 2, '0', STR_PAD_LEFT);
                $dateTime   = "{$dateStr} {$hour}:{$minute}:00";
                $discount   = $discountPool[($dayIdx + $sIdx) % count($discountPool)];
                $payType    = $payTypes[$sIdx % 2];
                $numItems   = ($sIdx % 4) + 1; // 1 to 4 items per sale

                $saleTotal = 0;

                for ($iIdx = 0; $iIdx < $numItems; $iIdx++) {
                    $productId = (($dayIdx * 17 + $sIdx * 23 + $iIdx * 29) % $totalProductsCount) + 1;
                    $batchId   = $latestBatchPerProduct[$productId] ?? 1;
                    $qty       = ($iIdx % 3) + 1;
                    $unitPrice = $productPriceMap[$productId] ?? 120.0;
                    $itemTotal = $qty * $unitPrice;
                    $saleTotal += $itemTotal;

                    $saleItemRows[] = [
                        'sale_id'      => $saleId,
                        'batch_id'     => $batchId,
                        'product_id'   => $productId,
                        'qty'          => $qty,
                        'retail_price' => $unitPrice,
                        'unit_price'   => $unitPrice,
                        'total'        => $itemTotal,
                        'discount'     => 0,
                        'created_at'   => $now,
                        'updated_at'   => $now,
                    ];
                }

                $salesRows[] = [
                    'id'           => $saleId,
                    'date_time'    => $dateTime,
                    'user_id'      => $userId,
                    'total'        => max(0, $saleTotal - $discount),
                    'discount'     => $discount,
                    'payment_type' => $payType,
                    'created_at'   => $now,
                    'updated_at'   => $now,
                ];

                $saleId++;
            }

            $dayIdx++;
        }

        foreach (array_chunk($salesRows, 100) as $chunk) {
            DB::table('sales')->insert($chunk);
        }

        foreach (array_chunk($saleItemRows, 200) as $chunk) {
            DB::table('sale_items')->insert($chunk);
        }

        // ─── Re-enable FK checks ───────────────────────────────────────────
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }
}
