<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Supplier;
use App\Models\Product;
use App\Models\SupplierInvoice;
use App\Models\Batch_Stock;
use App\Models\Route;
use App\Models\Truck;
use App\Models\Employee;
use App\Models\Shop;
use App\Models\SalesRep;
use App\Models\Loading;
use App\Models\LoadListItem;
use App\Models\LoadingReturn;
use App\Models\Sale;
use App\Models\SaleItem;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database with comprehensive dummy data.
     */
    public function run(): void
    {
        // -------------------------------------------------------------
        // 1. USERS (11 users: 3 admin, 8 cashier/staff)
        // -------------------------------------------------------------
        $usersData = [
            ['username' => 'admin', 'name' => 'System Admin', 'email' => 'admin@ims.com', 'phone' => '0770000000', 'role' => 'admin'],
            ['username' => 'manager', 'name' => 'Warehouse Manager', 'email' => 'manager@ims.com', 'phone' => '0771112233', 'role' => 'admin'],
            ['username' => 'supervisor', 'name' => 'Stock Supervisor', 'email' => 'supervisor@ims.com', 'phone' => '0772223344', 'role' => 'admin'],
            ['username' => 'cashier', 'name' => 'Cashier User', 'email' => 'cashier@ims.com', 'phone' => '0771234568', 'role' => 'cashier'],
            ['username' => 'cashier1', 'name' => 'Cashier User 1', 'email' => 'cashier1@ims.com', 'phone' => '0771234567', 'role' => 'cashier'],
            ['username' => 'cashier2', 'name' => 'Kasun Perera', 'email' => 'cashier2@ims.com', 'phone' => '0773334455', 'role' => 'cashier'],
            ['username' => 'cashier3', 'name' => 'Nadeeka Silva', 'email' => 'cashier3@ims.com', 'phone' => '0774445566', 'role' => 'cashier'],
            ['username' => 'cashier4', 'name' => 'Dilan Fernando', 'email' => 'cashier4@ims.com', 'phone' => '0775556677', 'role' => 'cashier'],
            ['username' => 'inventory1', 'name' => 'Ruwan Jayasinghe', 'email' => 'inventory1@ims.com', 'phone' => '0776667788', 'role' => 'admin'],
            ['username' => 'inventory2', 'name' => 'Chamara Wickramasinghe', 'email' => 'inventory2@ims.com', 'phone' => '0777778899', 'role' => 'admin'],
            ['username' => 'auditor', 'name' => 'Anura Bandara', 'email' => 'auditor@ims.com', 'phone' => '0778889900', 'role' => 'admin'],
        ];

        $users = [];
        foreach ($usersData as $ud) {
            $users[$ud['username']] = User::updateOrCreate(
                ['username' => $ud['username']],
                [
                    'name'     => $ud['name'],
                    'email'    => $ud['email'],
                    'phone'    => $ud['phone'],
                    'password' => Hash::make('password123'),
                    'role'     => $ud['role'],
                ]
            );
        }

        // -------------------------------------------------------------
        // 2. SUPPLIERS (12 suppliers)
        // -------------------------------------------------------------
        $suppliersData = [
            ['name' => 'Lanka Distributors', 'contactno' => '0112345678', 'address' => '12 Bankshall Street, Colombo 11'],
            ['name' => 'Hemas Consumer Brands', 'contactno' => '+94 78 985 1619', 'address' => '34/4, Negombo Road, Halawatha'],
            ['name' => 'Ceylon Biscuits Limited (Munchee)', 'contactno' => '0112855555', 'address' => 'High Level Road, Makumbura, Pannipitiya'],
            ['name' => 'Maliban Biscuit Manufactories', 'contactno' => '0115555555', 'address' => '389 Galle Road, Ratmalana'],
            ['name' => 'Nestlé Lanka PLC', 'contactno' => '0112699999', 'address' => '440 T.B. Jayah Mawatha, Colombo 10'],
            ['name' => 'Unilever Sri Lanka', 'contactno' => '0117888888', 'address' => '258 M. Vincent Perera Mawatha, Colombo 14'],
            ['name' => 'Fonterra Brands Lanka', 'contactno' => '0114828282', 'address' => '100 Delgoda Road, Biyagama'],
            ['name' => 'Ceylon Cold Stores (Elephant House)', 'contactno' => '0112441441', 'address' => '148 Vauxhall Street, Colombo 02'],
            ['name' => 'Renuka Foods PLC', 'contactno' => '0112314750', 'address' => '69 Sri Jinarathana Road, Colombo 02'],
            ['name' => 'Raigam Marketing Services', 'contactno' => '0112753340', 'address' => '277 Koswatta, Kiriwattuduwa'],
            ['name' => 'Harischandra Mills PLC', 'contactno' => '0412222421', 'address' => '11 Rahula Road, Matara'],
            ['name' => 'Wijeya Products Ltd', 'contactno' => '0362231200', 'address' => 'Kalutara Road, Dodangoda'],
        ];

        $suppliers = [];
        foreach ($suppliersData as $sd) {
            $suppliers[$sd['name']] = Supplier::updateOrCreate(
                ['name' => $sd['name']],
                ['contactno' => $sd['contactno'], 'address' => $sd['address']]
            );
        }

        // -------------------------------------------------------------
        // 3. ROUTES (10 routes)
        // -------------------------------------------------------------
        $routesData = [
            ['route_code' => 'RT-COL-01', 'route_description' => 'Colombo Central & Fort Metro'],
            ['route_code' => 'RT-NEG-02', 'route_description' => 'Negombo Coastal & Kochchikade'],
            ['route_code' => 'RT-KAN-03', 'route_description' => 'Kandy City & Peradeniya Urban'],
            ['route_code' => 'RT-GAL-04', 'route_description' => 'Galle Southern Highway Corridor'],
            ['route_code' => 'RT-GAM-05', 'route_description' => 'Gampaha Urban & Mirigama'],
            ['route_code' => 'RT-KUR-06', 'route_description' => 'Kurunegala Express North-Western'],
            ['route_code' => 'RT-KAL-07', 'route_description' => 'Kalutara & Wadduwa South'],
            ['route_code' => 'RT-RAT-08', 'route_description' => 'Ratnapura Sabaragamuwa Arterial'],
            ['route_code' => 'RT-MAT-09', 'route_description' => 'Matara & Weligama Bay Coastal'],
            ['route_code' => 'RT-ANR-10', 'route_description' => 'Anuradhapura Sacred City North'],
        ];

        $routes = [];
        foreach ($routesData as $rd) {
            $routes[$rd['route_code']] = Route::updateOrCreate(
                ['route_code' => $rd['route_code']],
                ['route_description' => $rd['route_description']]
            );
        }

        // -------------------------------------------------------------
        // 4. TRUCKS (10 trucks)
        // -------------------------------------------------------------
        $trucksData = [
            ['licence_plate_no' => 'WP-CAB-1234', 'description' => 'Isuzu NPR 4.5T Box Truck'],
            ['licence_plate_no' => 'WP-CAB-5678', 'description' => 'Mitsubishi Fuso Canter 5T'],
            ['licence_plate_no' => 'WP-DAE-2345', 'description' => 'Hino Dutro 4.2T Delivery Van'],
            ['licence_plate_no' => 'CP-NA-6789', 'description' => 'Tata LPT 709 Refrigerated Truck'],
            ['licence_plate_no' => 'SP-LB-3456', 'description' => 'Ashok Leyland Boss 6T Carrier'],
            ['licence_plate_no' => 'WP-LY-7890', 'description' => 'Isuzu Forward 8T Heavy Duty'],
            ['licence_plate_no' => 'NW-NC-1122', 'description' => 'Mahindra Bolero Maxi Truck Plus'],
            ['licence_plate_no' => 'WP-KV-3344', 'description' => 'Toyota Dyna 3.5T Closed Body'],
            ['licence_plate_no' => 'SG-ND-5566', 'description' => 'Eicher Pro 1059 Closed Container'],
            ['licence_plate_no' => 'NC-NE-7788', 'description' => 'Tata 407 Delivery Van'],
        ];

        $trucks = [];
        foreach ($trucksData as $td) {
            $trucks[$td['licence_plate_no']] = Truck::updateOrCreate(
                ['licence_plate_no' => $td['licence_plate_no']],
                ['description' => $td['description']]
            );
        }

        // -------------------------------------------------------------
        // 5. EMPLOYEES (12 employees)
        // -------------------------------------------------------------
        $employeesData = [
            ['name' => 'Kamal Gunaratne', 'nic' => '198512345678', 'phoneno' => '0771234561'],
            ['name' => 'Nimal Jayawardena', 'nic' => '198723456789', 'phoneno' => '0772345672'],
            ['name' => 'Sunil Shantha', 'nic' => '198934567890', 'phoneno' => '0773456783'],
            ['name' => 'Jagath Kumara', 'nic' => '199245678901', 'phoneno' => '0774567894'],
            ['name' => 'Nuwan Pradeep', 'nic' => '199456789012', 'phoneno' => '0775678905'],
            ['name' => 'Dinesh Priyankara', 'nic' => '199667890123', 'phoneno' => '0776789016'],
            ['name' => 'Ajith Rohana', 'nic' => '199178901234', 'phoneno' => '0777890127'],
            ['name' => 'Bandula Warnakula', 'nic' => '198889012345', 'phoneno' => '0778901238'],
            ['name' => 'Sanath Kaluwitharana', 'nic' => '198690123456', 'phoneno' => '0779012349'],
            ['name' => 'Upul Chandana', 'nic' => '199001234567', 'phoneno' => '0770123450'],
            ['name' => 'Mahinda Rajapaksha', 'nic' => '199312345671', 'phoneno' => '0771122331'],
            ['name' => 'Ranjan Ramanayake', 'nic' => '199523456782', 'phoneno' => '0772233442'],
        ];

        $employees = [];
        foreach ($employeesData as $ed) {
            $employees[$ed['nic']] = Employee::updateOrCreate(
                ['nic' => $ed['nic']],
                ['name' => $ed['name'], 'phoneno' => $ed['phoneno']]
            );
        }

        // -------------------------------------------------------------
        // 6. SHOPS (15 customer shops)
        // -------------------------------------------------------------
        $shopsData = [
            ['shop_code' => 'SHP-001', 'shop_name' => 'Perera Super City', 'address' => '45 Main Street, Colombo 11', 'phoneno' => '0112341111', 'route_code' => 'RT-COL-01'],
            ['shop_code' => 'SHP-002', 'shop_name' => 'City Mart Supermarket', 'address' => '112 High Level Road, Colombo 06', 'phoneno' => '0112342222', 'route_code' => 'RT-COL-01'],
            ['shop_code' => 'SHP-003', 'shop_name' => 'Negombo Mini Super', 'address' => '88 Sea Street, Negombo', 'phoneno' => '0312231111', 'route_code' => 'RT-NEG-02'],
            ['shop_code' => 'SHP-004', 'shop_name' => 'Lagoon View Grocery', 'address' => '12 Porutota Road, Kochchikade', 'phoneno' => '0312232222', 'route_code' => 'RT-NEG-02'],
            ['shop_code' => 'SHP-005', 'shop_name' => 'Central Highlands Stores', 'address' => '15 Dalada Veediya, Kandy', 'phoneno' => '0812233333', 'route_code' => 'RT-KAN-03'],
            ['shop_code' => 'SHP-006', 'shop_name' => 'Peradeniya Grocery & Bakers', 'address' => '78 Kandy Road, Peradeniya', 'phoneno' => '0812234444', 'route_code' => 'RT-KAN-03'],
            ['shop_code' => 'SHP-007', 'shop_name' => 'Southern Coast Super', 'address' => '230 Matara Road, Galle', 'phoneno' => '0912235555', 'route_code' => 'RT-GAL-04'],
            ['shop_code' => 'SHP-008', 'shop_name' => 'Fort Mart Family Shop', 'address' => '41 Church Street, Galle Fort', 'phoneno' => '0912236666', 'route_code' => 'RT-GAL-04'],
            ['shop_code' => 'SHP-009', 'shop_name' => 'Gampaha Central Stores', 'address' => '67 Colombo Road, Gampaha', 'phoneno' => '0332237777', 'route_code' => 'RT-GAM-05'],
            ['shop_code' => 'SHP-010', 'shop_name' => 'Royal Retailers Mirigama', 'address' => '19 Railway Road, Mirigama', 'phoneno' => '0332238888', 'route_code' => 'RT-GAM-05'],
            ['shop_code' => 'SHP-011', 'shop_name' => 'Wayamba Trade Center', 'address' => '104 Colombo Road, Kurunegala', 'phoneno' => '0372239999', 'route_code' => 'RT-KUR-06'],
            ['shop_code' => 'SHP-012', 'shop_name' => 'Kalutara Grand Mart', 'address' => '82 Galle Road, Kalutara South', 'phoneno' => '0342241111', 'route_code' => 'RT-KAL-07'],
            ['shop_code' => 'SHP-013', 'shop_name' => 'Sabaragamuwa Gem Stores', 'address' => '33 Main Street, Ratnapura', 'phoneno' => '0452242222', 'route_code' => 'RT-RAT-08'],
            ['shop_code' => 'SHP-014', 'shop_name' => 'Nilwala Supermarket', 'address' => '54 Hakmana Road, Matara', 'phoneno' => '0412243333', 'route_code' => 'RT-MAT-09'],
            ['shop_code' => 'SHP-015', 'shop_name' => 'Nuwara Wewa Retailers', 'address' => '12 Market Place, Anuradhapura', 'phoneno' => '0252244444', 'route_code' => 'RT-ANR-10'],
        ];

        foreach ($shopsData as $sh) {
            Shop::updateOrCreate(
                ['shop_code' => $sh['shop_code']],
                [
                    'shop_name'  => $sh['shop_name'],
                    'Address'    => $sh['address'],
                    'phoneno'    => $sh['phoneno'],
                    'route_code' => $sh['route_code'],
                ]
            );
        }

        // -------------------------------------------------------------
        // 7. SALES REPS (10 sales representatives)
        // -------------------------------------------------------------
        $salesRepsData = [
            ['rep_id' => 'REP-001', 'supplier' => 'Lanka Distributors', 'name' => 'Chaminda Vaas', 'contact' => '0771000001', 'join_date' => '2024-01-15', 'route' => 'RT-COL-01'],
            ['rep_id' => 'REP-002', 'supplier' => 'Hemas Consumer Brands', 'name' => 'Marvan Atapattu', 'contact' => '0771000002', 'join_date' => '2024-02-01', 'route' => 'RT-NEG-02'],
            ['rep_id' => 'REP-003', 'supplier' => 'Ceylon Biscuits Limited (Munchee)', 'name' => 'Romesh Kaluwitharana', 'contact' => '0771000003', 'join_date' => '2024-03-10', 'route' => 'RT-KAN-03'],
            ['rep_id' => 'REP-004', 'supplier' => 'Maliban Biscuit Manufactories', 'name' => 'Aravinda de Silva', 'contact' => '0771000004', 'join_date' => '2024-04-05', 'route' => 'RT-GAL-04'],
            ['rep_id' => 'REP-005', 'supplier' => 'Nestlé Lanka PLC', 'name' => 'Arjuna Ranatunga', 'contact' => '0771000005', 'join_date' => '2024-05-12', 'route' => 'RT-GAM-05'],
            ['rep_id' => 'REP-006', 'supplier' => 'Unilever Sri Lanka', 'name' => 'Roshan Mahanama', 'contact' => '0771000006', 'join_date' => '2024-06-18', 'route' => 'RT-KUR-06'],
            ['rep_id' => 'REP-007', 'supplier' => 'Fonterra Brands Lanka', 'name' => 'Asanka Gurusinha', 'contact' => '0771000007', 'join_date' => '2024-07-22', 'route' => 'RT-KAL-07'],
            ['rep_id' => 'REP-008', 'supplier' => 'Ceylon Cold Stores (Elephant House)', 'name' => 'Pramodya Wickramasinghe', 'contact' => '0771000008', 'join_date' => '2024-08-14', 'route' => 'RT-RAT-08'],
            ['rep_id' => 'REP-009', 'supplier' => 'Renuka Foods PLC', 'name' => 'Hashan Tillakaratne', 'contact' => '0771000009', 'join_date' => '2024-09-01', 'route' => 'RT-MAT-09'],
            ['rep_id' => 'REP-010', 'supplier' => 'Wijeya Products Ltd', 'name' => 'Kumar Dharmasena', 'contact' => '0771000010', 'join_date' => '2024-10-05', 'route' => 'RT-ANR-10'],
        ];

        $salesReps = [];
        foreach ($salesRepsData as $srd) {
            $suppObj = $suppliers[$srd['supplier']];
            $routeObj = $routes[$srd['route']];
            $salesReps[$srd['rep_id']] = SalesRep::updateOrCreate(
                ['rep_id' => $srd['rep_id']],
                [
                    'supplier_id' => $suppObj->id,
                    'name'        => $srd['name'],
                    'contact'     => $srd['contact'],
                    'join_date'   => $srd['join_date'],
                    'route_id'    => $routeObj->id,
                ]
            );
        }

        // -------------------------------------------------------------
        // 8. SUPPLIER INVOICES (12 invoices)
        // -------------------------------------------------------------
        $invoicesData = [
            ['invoice_number' => 'INV-2026-001', 'supplier' => 'Fonterra Brands Lanka', 'amount' => 145000.00, 'days_ago' => 45],
            ['invoice_number' => 'INV-2026-002', 'supplier' => 'Maliban Biscuit Manufactories', 'amount' => 88500.00, 'days_ago' => 40],
            ['invoice_number' => 'INV-2026-003', 'supplier' => 'Ceylon Biscuits Limited (Munchee)', 'amount' => 96000.00, 'days_ago' => 35],
            ['invoice_number' => 'INV-2026-004', 'supplier' => 'Nestlé Lanka PLC', 'amount' => 175000.00, 'days_ago' => 30],
            ['invoice_number' => 'INV-2026-005', 'supplier' => 'Unilever Sri Lanka', 'amount' => 210000.00, 'days_ago' => 25],
            ['invoice_number' => 'INV-2026-006', 'supplier' => 'Hemas Consumer Brands', 'amount' => 112000.00, 'days_ago' => 20],
            ['invoice_number' => 'INV-2026-007', 'supplier' => 'Ceylon Cold Stores (Elephant House)', 'amount' => 134000.00, 'days_ago' => 18],
            ['invoice_number' => 'INV-2026-008', 'supplier' => 'Raigam Marketing Services', 'amount' => 64000.00, 'days_ago' => 15],
            ['invoice_number' => 'INV-2026-009', 'supplier' => 'Renuka Foods PLC', 'amount' => 78000.00, 'days_ago' => 12],
            ['invoice_number' => 'INV-2026-010', 'supplier' => 'Harischandra Mills PLC', 'amount' => 52000.00, 'days_ago' => 10],
            ['invoice_number' => 'INV-2026-011', 'supplier' => 'Wijeya Products Ltd', 'amount' => 69000.00, 'days_ago' => 8],
            ['invoice_number' => 'INV-2026-012', 'supplier' => 'Lanka Distributors', 'amount' => 85000.00, 'days_ago' => 5],
        ];

        $invoices = [];
        foreach ($invoicesData as $inv) {
            $supp = $suppliers[$inv['supplier']];
            $invoices[$inv['invoice_number']] = SupplierInvoice::updateOrCreate(
                ['invoice_number' => $inv['invoice_number']],
                [
                    'invoice_date'      => Carbon::now()->subDays($inv['days_ago'])->toDateString(),
                    'total_bill_amount' => $inv['amount'],
                    'supplier_id'       => $supp->id,
                ]
            );
        }

        // -------------------------------------------------------------
        // 9. PRODUCTS (EXACTLY 50 DUMMY PRODUCTS)
        // -------------------------------------------------------------
        $productsCatalog = [
            // Fonterra (1-4)
            ['code' => 'MAT-001', 'barcode' => '890100000001', 'name' => 'Anchor Full Cream Milk Powder 400g', 'supplier' => 'Fonterra Brands Lanka', 'retail' => 1250.00, 'net' => 1100.00, 'pack' => 24, 'cases' => 15, 'qty' => 360, 'exp_months' => 8, 'inv' => 'INV-2026-001'],
            ['code' => 'MAT-002', 'barcode' => '890100000002', 'name' => 'Anchor Full Cream Milk Powder 1kg', 'supplier' => 'Fonterra Brands Lanka', 'retail' => 2950.00, 'net' => 2600.00, 'pack' => 12, 'cases' => 10, 'qty' => 120, 'exp_months' => 9, 'inv' => 'INV-2026-001'],
            ['code' => 'MAT-003', 'barcode' => '890100000003', 'name' => 'Anchor Salted Butter 227g', 'supplier' => 'Fonterra Brands Lanka', 'retail' => 820.00, 'net' => 710.00, 'pack' => 20, 'cases' => 8, 'qty' => 160, 'exp_months' => 4, 'inv' => 'INV-2026-001'],
            ['code' => 'MAT-004', 'barcode' => '890100000004', 'name' => 'Anchor PediaPro 1-2 Years 400g', 'supplier' => 'Fonterra Brands Lanka', 'retail' => 1480.00, 'net' => 1290.00, 'pack' => 12, 'cases' => 6, 'qty' => 72, 'exp_months' => 7, 'inv' => 'INV-2026-001'],

            // Maliban (5-9)
            ['code' => 'MAT-005', 'barcode' => '890100000005', 'name' => 'Maliban Cream Cracker 500g', 'supplier' => 'Maliban Biscuit Manufactories', 'retail' => 380.00, 'net' => 320.00, 'pack' => 24, 'cases' => 20, 'qty' => 480, 'exp_months' => 10, 'inv' => 'INV-2026-002'],
            ['code' => 'MAT-006', 'barcode' => '890100000006', 'name' => 'Maliban Gold Marie 300g', 'supplier' => 'Maliban Biscuit Manufactories', 'retail' => 260.00, 'net' => 215.00, 'pack' => 30, 'cases' => 15, 'qty' => 450, 'exp_months' => 11, 'inv' => 'INV-2026-002'],
            ['code' => 'MAT-007', 'barcode' => '890100000007', 'name' => 'Maliban Chocolate Biscuit 200g', 'supplier' => 'Maliban Biscuit Manufactories', 'retail' => 240.00, 'net' => 200.00, 'pack' => 36, 'cases' => 12, 'qty' => 432, 'exp_months' => 9, 'inv' => 'INV-2026-002'],
            ['code' => 'MAT-008', 'barcode' => '890100000008', 'name' => 'Maliban Lemon Puff 200g', 'supplier' => 'Maliban Biscuit Manufactories', 'retail' => 220.00, 'net' => 180.00, 'pack' => 36, 'cases' => 10, 'qty' => 360, 'exp_months' => 8, 'inv' => 'INV-2026-002'],
            ['code' => 'MAT-009', 'barcode' => '890100000009', 'name' => 'Maliban Real Temptation Chocolate 100g', 'supplier' => 'Maliban Biscuit Manufactories', 'retail' => 180.00, 'net' => 145.00, 'pack' => 48, 'cases' => 10, 'qty' => 4, 'exp_months' => 1, 'inv' => 'INV-2026-002'], // Low stock & near expiry!

            // CBL Munchee (10-14)
            ['code' => 'MAT-010', 'barcode' => '890100000010', 'name' => 'Munchee Super Cream Cracker 490g', 'supplier' => 'Ceylon Biscuits Limited (Munchee)', 'retail' => 390.00, 'net' => 325.00, 'pack' => 24, 'cases' => 25, 'qty' => 600, 'exp_months' => 12, 'inv' => 'INV-2026-003'],
            ['code' => 'MAT-011', 'barcode' => '890100000011', 'name' => 'Munchee Tiara Sponge Cake 200g', 'supplier' => 'Ceylon Biscuits Limited (Munchee)', 'retail' => 320.00, 'net' => 270.00, 'pack' => 20, 'cases' => 10, 'qty' => 200, 'exp_months' => 5, 'inv' => 'INV-2026-003'],
            ['code' => 'MAT-012', 'barcode' => '890100000012', 'name' => 'Munchee Hawaiian Cookies 200g', 'supplier' => 'Ceylon Biscuits Limited (Munchee)', 'retail' => 250.00, 'net' => 210.00, 'pack' => 30, 'cases' => 8, 'qty' => 240, 'exp_months' => 9, 'inv' => 'INV-2026-003'],
            ['code' => 'MAT-013', 'barcode' => '890100000013', 'name' => 'Munchee Chocolate Puff 200g', 'supplier' => 'Ceylon Biscuits Limited (Munchee)', 'retail' => 230.00, 'net' => 190.00, 'pack' => 36, 'cases' => 15, 'qty' => 540, 'exp_months' => 10, 'inv' => 'INV-2026-003'],
            ['code' => 'MAT-014', 'barcode' => '890100000014', 'name' => 'Munchee Ginger Biscuit 200g', 'supplier' => 'Ceylon Biscuits Limited (Munchee)', 'retail' => 210.00, 'net' => 175.00, 'pack' => 36, 'cases' => 12, 'qty' => 432, 'exp_months' => 11, 'inv' => 'INV-2026-003'],

            // Nestlé (15-19)
            ['code' => 'MAT-015', 'barcode' => '890100000015', 'name' => 'Maggi 2-Minute Chicken Noodles 73g', 'supplier' => 'Nestlé Lanka PLC', 'retail' => 120.00, 'net' => 98.00, 'pack' => 60, 'cases' => 30, 'qty' => 1800, 'exp_months' => 8, 'inv' => 'INV-2026-004'],
            ['code' => 'MAT-016', 'barcode' => '890100000016', 'name' => 'Maggi Curry Noodles Family Pack 300g', 'supplier' => 'Nestlé Lanka PLC', 'retail' => 420.00, 'net' => 355.00, 'pack' => 20, 'cases' => 15, 'qty' => 300, 'exp_months' => 7, 'inv' => 'INV-2026-004'],
            ['code' => 'MAT-017', 'barcode' => '890100000017', 'name' => 'Nestomalt Malted Food Drink 400g', 'supplier' => 'Nestlé Lanka PLC', 'retail' => 690.00, 'net' => 595.00, 'pack' => 24, 'cases' => 18, 'qty' => 432, 'exp_months' => 10, 'inv' => 'INV-2026-004'],
            ['code' => 'MAT-018', 'barcode' => '890100000018', 'name' => 'Milo Chocolate Malt Drink 400g', 'supplier' => 'Nestlé Lanka PLC', 'retail' => 780.00, 'net' => 680.00, 'pack' => 24, 'cases' => 20, 'qty' => 480, 'exp_months' => 9, 'inv' => 'INV-2026-004'],
            ['code' => 'MAT-019', 'barcode' => '890100000019', 'name' => 'Nescafé Classic Instant Coffee 50g', 'supplier' => 'Nestlé Lanka PLC', 'retail' => 650.00, 'net' => 560.00, 'pack' => 36, 'cases' => 8, 'qty' => 6, 'exp_months' => 0.5, 'inv' => 'INV-2026-004'], // Low stock & near expiry!

            // Unilever (20-25)
            ['code' => 'MAT-020', 'barcode' => '890100000020', 'name' => 'Sunlight Clean Detergent Powder 1kg', 'supplier' => 'Unilever Sri Lanka', 'retail' => 420.00, 'net' => 355.00, 'pack' => 15, 'cases' => 20, 'qty' => 300, 'exp_months' => 24, 'inv' => 'INV-2026-005'],
            ['code' => 'MAT-021', 'barcode' => '890100000021', 'name' => 'Sunlight Lemon Soap Bar 115g', 'supplier' => 'Unilever Sri Lanka', 'retail' => 130.00, 'net' => 108.00, 'pack' => 72, 'cases' => 25, 'qty' => 1800, 'exp_months' => 24, 'inv' => 'INV-2026-005'],
            ['code' => 'MAT-022', 'barcode' => '890100000022', 'name' => 'Lifebuoy Total 10 Soap 100g', 'supplier' => 'Unilever Sri Lanka', 'retail' => 160.00, 'net' => 135.00, 'pack' => 72, 'cases' => 20, 'qty' => 1440, 'exp_months' => 24, 'inv' => 'INV-2026-005'],
            ['code' => 'MAT-023', 'barcode' => '890100000023', 'name' => 'Signal Strong Teeth Toothpaste 120g', 'supplier' => 'Unilever Sri Lanka', 'retail' => 230.00, 'net' => 195.00, 'pack' => 48, 'cases' => 15, 'qty' => 720, 'exp_months' => 18, 'inv' => 'INV-2026-005'],
            ['code' => 'MAT-024', 'barcode' => '890100000024', 'name' => 'Astra Margarine Fat Spread 250g', 'supplier' => 'Unilever Sri Lanka', 'retail' => 490.00, 'net' => 420.00, 'pack' => 24, 'cases' => 10, 'qty' => 240, 'exp_months' => 6, 'inv' => 'INV-2026-005'],
            ['code' => 'MAT-025', 'barcode' => '890100000025', 'name' => 'Vim Dishwash Bar 200g', 'supplier' => 'Unilever Sri Lanka', 'retail' => 150.00, 'net' => 125.00, 'pack' => 48, 'cases' => 18, 'qty' => 864, 'exp_months' => 24, 'inv' => 'INV-2026-005'],

            // Hemas (26-30)
            ['code' => 'MAT-026', 'barcode' => '890100000026', 'name' => 'Baby Cheramy Cream Floral 100ml', 'supplier' => 'Hemas Consumer Brands', 'retail' => 450.00, 'net' => 385.00, 'pack' => 24, 'cases' => 12, 'qty' => 288, 'exp_months' => 14, 'inv' => 'INV-2026-006'],
            ['code' => 'MAT-027', 'barcode' => '890100000027', 'name' => 'Baby Cheramy Cologne Floral 100ml', 'supplier' => 'Hemas Consumer Brands', 'retail' => 380.00, 'net' => 320.00, 'pack' => 24, 'cases' => 10, 'qty' => 240, 'exp_months' => 15, 'inv' => 'INV-2026-006'],
            ['code' => 'MAT-028', 'barcode' => '890100000028', 'name' => 'Clogard Fresh Mint Toothpaste 120g', 'supplier' => 'Hemas Consumer Brands', 'retail' => 220.00, 'net' => 185.00, 'pack' => 48, 'cases' => 16, 'qty' => 768, 'exp_months' => 18, 'inv' => 'INV-2026-006'],
            ['code' => 'MAT-029', 'barcode' => '890100000029', 'name' => 'Diva Fresh Lime Detergent Powder 1kg', 'supplier' => 'Hemas Consumer Brands', 'retail' => 390.00, 'net' => 330.00, 'pack' => 15, 'cases' => 15, 'qty' => 225, 'exp_months' => 20, 'inv' => 'INV-2026-006'],
            ['code' => 'MAT-030', 'barcode' => '890100000030', 'name' => 'Velvet Moisturizing Body Wash Rose 250ml', 'supplier' => 'Hemas Consumer Brands', 'retail' => 580.00, 'net' => 495.00, 'pack' => 20, 'cases' => 8, 'qty' => 160, 'exp_months' => 12, 'inv' => 'INV-2026-006'],

            // Elephant House (31-35)
            ['code' => 'MAT-031', 'barcode' => '890100000031', 'name' => 'Elephant House Cream Soda 1.5L', 'supplier' => 'Ceylon Cold Stores (Elephant House)', 'retail' => 420.00, 'net' => 355.00, 'pack' => 12, 'cases' => 30, 'qty' => 360, 'exp_months' => 6, 'inv' => 'INV-2026-007'],
            ['code' => 'MAT-032', 'barcode' => '890100000032', 'name' => 'Elephant House Necto 1.5L', 'supplier' => 'Ceylon Cold Stores (Elephant House)', 'retail' => 420.00, 'net' => 355.00, 'pack' => 12, 'cases' => 25, 'qty' => 300, 'exp_months' => 6, 'inv' => 'INV-2026-007'],
            ['code' => 'MAT-033', 'barcode' => '890100000033', 'name' => 'Elephant House Ginger Beer 1.5L', 'supplier' => 'Ceylon Cold Stores (Elephant House)', 'retail' => 440.00, 'net' => 370.00, 'pack' => 12, 'cases' => 25, 'qty' => 300, 'exp_months' => 6, 'inv' => 'INV-2026-007'],
            ['code' => 'MAT-034', 'barcode' => '890100000034', 'name' => 'Elephant House Vanilla Ice Cream 1L', 'supplier' => 'Ceylon Cold Stores (Elephant House)', 'retail' => 650.00, 'net' => 550.00, 'pack' => 8, 'cases' => 15, 'qty' => 120, 'exp_months' => 4, 'inv' => 'INV-2026-007'],
            ['code' => 'MAT-035', 'barcode' => '890100000035', 'name' => 'Elephant House Chicken Hot Dogs 500g', 'supplier' => 'Ceylon Cold Stores (Elephant House)', 'retail' => 890.00, 'net' => 760.00, 'pack' => 12, 'cases' => 10, 'qty' => 8, 'exp_months' => 0.8, 'inv' => 'INV-2026-007'], // Low stock & near expiry!

            // Raigam (36-39)
            ['code' => 'MAT-036', 'barcode' => '890100000036', 'name' => 'Raigam Deveni Batta Pure Salt 1kg', 'supplier' => 'Raigam Marketing Services', 'retail' => 120.00, 'net' => 95.00, 'pack' => 25, 'cases' => 40, 'qty' => 1000, 'exp_months' => 36, 'inv' => 'INV-2026-008'],
            ['code' => 'MAT-037', 'barcode' => '890100000037', 'name' => 'Raigam Soya Meat Chicken Flavor 90g', 'supplier' => 'Raigam Marketing Services', 'retail' => 130.00, 'net' => 105.00, 'pack' => 50, 'cases' => 20, 'qty' => 1000, 'exp_months' => 12, 'inv' => 'INV-2026-008'],
            ['code' => 'MAT-038', 'barcode' => '890100000038', 'name' => 'Raigam Soya Meat Mutton Flavor 90g', 'supplier' => 'Raigam Marketing Services', 'retail' => 130.00, 'net' => 105.00, 'pack' => 50, 'cases' => 15, 'qty' => 750, 'exp_months' => 12, 'inv' => 'INV-2026-008'],
            ['code' => 'MAT-039', 'barcode' => '890100000039', 'name' => 'Raigam Instant Rice Noodles 400g', 'supplier' => 'Raigam Marketing Services', 'retail' => 280.00, 'net' => 235.00, 'pack' => 20, 'cases' => 12, 'qty' => 240, 'exp_months' => 9, 'inv' => 'INV-2026-008'],

            // Renuka (40-42)
            ['code' => 'MAT-040', 'barcode' => '890100000040', 'name' => 'Renuka Coconut Milk Powder 300g', 'supplier' => 'Renuka Foods PLC', 'retail' => 720.00, 'net' => 610.00, 'pack' => 24, 'cases' => 15, 'qty' => 360, 'exp_months' => 18, 'inv' => 'INV-2026-009'],
            ['code' => 'MAT-041', 'barcode' => '890100000041', 'name' => 'Renuka Premium Coconut Milk Can 400ml', 'supplier' => 'Renuka Foods PLC', 'retail' => 450.00, 'net' => 380.00, 'pack' => 24, 'cases' => 10, 'qty' => 240, 'exp_months' => 15, 'inv' => 'INV-2026-009'],
            ['code' => 'MAT-042', 'barcode' => '890100000042', 'name' => 'Captain Canned Jack Mackerel 425g', 'supplier' => 'Renuka Foods PLC', 'retail' => 620.00, 'net' => 530.00, 'pack' => 24, 'cases' => 12, 'qty' => 288, 'exp_months' => 20, 'inv' => 'INV-2026-009'],

            // Harischandra (43-45)
            ['code' => 'MAT-043', 'barcode' => '890100000043', 'name' => 'Harischandra Pure Coffee Powder 200g', 'supplier' => 'Harischandra Mills PLC', 'retail' => 480.00, 'net' => 410.00, 'pack' => 24, 'cases' => 10, 'qty' => 240, 'exp_months' => 10, 'inv' => 'INV-2026-010'],
            ['code' => 'MAT-044', 'barcode' => '890100000044', 'name' => 'Harischandra Kurakkan Flour 400g', 'supplier' => 'Harischandra Mills PLC', 'retail' => 320.00, 'net' => 270.00, 'pack' => 20, 'cases' => 8, 'qty' => 160, 'exp_months' => 8, 'inv' => 'INV-2026-010'],
            ['code' => 'MAT-045', 'barcode' => '890100000045', 'name' => 'Harischandra Special Noodles 400g', 'supplier' => 'Harischandra Mills PLC', 'retail' => 290.00, 'net' => 245.00, 'pack' => 20, 'cases' => 12, 'qty' => 240, 'exp_months' => 9, 'inv' => 'INV-2026-010'],

            // Wijeya (46-49)
            ['code' => 'MAT-046', 'barcode' => '890100000046', 'name' => 'Wijeya Roasted Curry Powder 250g', 'supplier' => 'Wijeya Products Ltd', 'retail' => 340.00, 'net' => 290.00, 'pack' => 40, 'cases' => 15, 'qty' => 600, 'exp_months' => 14, 'inv' => 'INV-2026-011'],
            ['code' => 'MAT-047', 'barcode' => '890100000047', 'name' => 'Wijeya Pure Chilli Powder 250g', 'supplier' => 'Wijeya Products Ltd', 'retail' => 380.00, 'net' => 325.00, 'pack' => 40, 'cases' => 15, 'qty' => 600, 'exp_months' => 14, 'inv' => 'INV-2026-011'],
            ['code' => 'MAT-048', 'barcode' => '890100000048', 'name' => 'Wijeya Pure Turmeric Powder 100g', 'supplier' => 'Wijeya Products Ltd', 'retail' => 290.00, 'net' => 245.00, 'pack' => 50, 'cases' => 10, 'qty' => 500, 'exp_months' => 16, 'inv' => 'INV-2026-011'],
            ['code' => 'MAT-049', 'barcode' => '890100000049', 'name' => 'Wijeya Black Pepper Powder 100g', 'supplier' => 'Wijeya Products Ltd', 'retail' => 360.00, 'net' => 305.00, 'pack' => 50, 'cases' => 8, 'qty' => 400, 'exp_months' => 16, 'inv' => 'INV-2026-011'],

            // Lanka Distributors (50)
            ['code' => 'MAT-050', 'barcode' => '890100000050', 'name' => 'Fortune Pure White Coconut Oil 1L', 'supplier' => 'Lanka Distributors', 'retail' => 980.00, 'net' => 850.00, 'pack' => 12, 'cases' => 25, 'qty' => 300, 'exp_months' => 18, 'inv' => 'INV-2026-012'],
        ];

        $products = [];
        $batches = [];

        foreach ($productsCatalog as $pItem) {
            $suppObj = $suppliers[$pItem['supplier']];
            $invObj = $invoices[$pItem['inv']];

            $product = Product::updateOrCreate(
                ['material_code' => $pItem['code']],
                [
                    'barcode'     => $pItem['barcode'],
                    'name'        => $pItem['name'],
                    'supplier_id' => $suppObj->id,
                ]
            );
            $products[$pItem['code']] = $product;

            // Create batch stock for each product
            $batch = Batch_Stock::updateOrCreate(
                [
                    'product_id'          => $product->id,
                    'supplier_invoice_id' => $invObj->id,
                ],
                [
                    'no_cases'     => $pItem['cases'],
                    'pack_size'    => $pItem['pack'],
                    'extra_units'  => 0,
                    'remain_qty'   => $pItem['qty'],
                    'returned_qty' => 0,
                    'free_qty'     => 0,
                    'retail_price' => $pItem['retail'],
                    'netprice'     => $pItem['net'],
                    'expiry_date'  => Carbon::now()->addDays((int)($pItem['exp_months'] * 30))->toDateString(),
                ]
            );
            $batches[$pItem['code']] = $batch;
        }

        // -------------------------------------------------------------
        // 10. LOADINGS (10 dispatches)
        // -------------------------------------------------------------
        $loadingsData = [
            ['load_number' => 'LD-2026-001', 'truck' => 'WP-CAB-1234', 'route' => 'RT-COL-01', 'driver' => 'Kamal Gunaratne', 'helper' => 'Jagath Kumara', 'collector' => 'Bandula Warnakula', 'rep' => 'REP-001', 'status' => 'delivered', 'prep_days_ago' => 12, 'load_days_ago' => 11],
            ['load_number' => 'LD-2026-002', 'truck' => 'WP-CAB-5678', 'route' => 'RT-NEG-02', 'driver' => 'Nimal Jayawardena', 'helper' => 'Nuwan Pradeep', 'collector' => 'Sanath Kaluwitharana', 'rep' => 'REP-002', 'status' => 'delivered', 'prep_days_ago' => 10, 'load_days_ago' => 9],
            ['load_number' => 'LD-2026-003', 'truck' => 'WP-DAE-2345', 'route' => 'RT-KAN-03', 'driver' => 'Sunil Shantha', 'helper' => 'Dinesh Priyankara', 'collector' => 'Upul Chandana', 'rep' => 'REP-003', 'status' => 'delivered', 'prep_days_ago' => 8, 'load_days_ago' => 7],
            ['load_number' => 'LD-2026-004', 'truck' => 'CP-NA-6789', 'route' => 'RT-GAL-04', 'driver' => 'Kamal Gunaratne', 'helper' => 'Ajith Rohana', 'collector' => 'Bandula Warnakula', 'rep' => 'REP-004', 'status' => 'delivered', 'prep_days_ago' => 6, 'load_days_ago' => 5],
            ['load_number' => 'LD-2026-005', 'truck' => 'SP-LB-3456', 'route' => 'RT-GAM-05', 'driver' => 'Nimal Jayawardena', 'helper' => 'Jagath Kumara', 'collector' => 'Sanath Kaluwitharana', 'rep' => 'REP-005', 'status' => 'pending', 'prep_days_ago' => 2, 'load_days_ago' => 1],
            ['load_number' => 'LD-2026-006', 'truck' => 'WP-LY-7890', 'route' => 'RT-KUR-06', 'driver' => 'Sunil Shantha', 'helper' => 'Nuwan Pradeep', 'collector' => 'Upul Chandana', 'rep' => 'REP-006', 'status' => 'pending', 'prep_days_ago' => 1, 'load_days_ago' => null],
            ['load_number' => 'LD-2026-007', 'truck' => 'NW-NC-1122', 'route' => 'RT-KAL-07', 'driver' => 'Kamal Gunaratne', 'helper' => 'Dinesh Priyankara', 'collector' => 'Bandula Warnakula', 'rep' => 'REP-007', 'status' => 'pending', 'prep_days_ago' => 1, 'load_days_ago' => null],
            ['load_number' => 'LD-2026-008', 'truck' => 'WP-KV-3344', 'route' => 'RT-RAT-08', 'driver' => 'Nimal Jayawardena', 'helper' => 'Ajith Rohana', 'collector' => 'Sanath Kaluwitharana', 'rep' => 'REP-008', 'status' => 'pending', 'prep_days_ago' => 0, 'load_days_ago' => null],
            ['load_number' => 'LD-2026-009', 'truck' => 'SG-ND-5566', 'route' => 'RT-MAT-09', 'driver' => 'Sunil Shantha', 'helper' => 'Jagath Kumara', 'collector' => 'Upul Chandana', 'rep' => 'REP-009', 'status' => 'not_delivered', 'prep_days_ago' => 4, 'load_days_ago' => 3],
            ['load_number' => 'LD-2026-010', 'truck' => 'NC-NE-7788', 'route' => 'RT-ANR-10', 'driver' => 'Kamal Gunaratne', 'helper' => 'Nuwan Pradeep', 'collector' => 'Bandula Warnakula', 'rep' => 'REP-010', 'status' => 'not_delivered', 'prep_days_ago' => 5, 'load_days_ago' => 4],
        ];

        $loadings = [];
        $employeeByName = [];
        foreach ($employees as $emp) {
            $employeeByName[$emp->name] = $emp;
        }

        foreach ($loadingsData as $ld) {
            $truckObj = $trucks[$ld['truck']];
            $routeObj = $routes[$ld['route']];
            $driverObj = $employeeByName[$ld['driver']] ?? null;
            $helperObj = $employeeByName[$ld['helper']] ?? null;
            $collectorObj = $employeeByName[$ld['collector']] ?? null;
            $repObj = $salesReps[$ld['rep']] ?? null;

            $loading = Loading::updateOrCreate(
                ['load_number' => $ld['load_number']],
                [
                    'truck_id'          => $truckObj->id,
                    'route_id'          => $routeObj->id,
                    'prepared_date'     => Carbon::now()->subDays($ld['prep_days_ago'])->toDateString(),
                    'loading_date'      => $ld['load_days_ago'] !== null ? Carbon::now()->subDays($ld['load_days_ago'])->toDateString() : null,
                    'status'            => $ld['status'],
                    'driver_id'         => $driverObj ? $driverObj->id : null,
                    'helper_id'         => $helperObj ? $helperObj->id : null,
                    'cash_collector_id' => $collectorObj ? $collectorObj->id : null,
                    'sales_rep_id'      => $repObj ? $repObj->id : null,
                ]
            );
            $loadings[$ld['load_number']] = $loading;
        }

        // -------------------------------------------------------------
        // 11. LOAD LIST ITEMS (15 items across loadings)
        // -------------------------------------------------------------
        $loadListItemsData = [
            ['load' => 'LD-2026-001', 'prod' => 'MAT-001', 'qty' => 48, 'free_qty' => 2, 'wh_price' => 1150.00, 'net_price' => 1100.00],
            ['load' => 'LD-2026-001', 'prod' => 'MAT-005', 'qty' => 72, 'free_qty' => 4, 'wh_price' => 350.00, 'net_price' => 320.00],
            ['load' => 'LD-2026-002', 'prod' => 'MAT-010', 'qty' => 48, 'free_qty' => 2, 'wh_price' => 360.00, 'net_price' => 325.00],
            ['load' => 'LD-2026-002', 'prod' => 'MAT-015', 'qty' => 120, 'free_qty' => 10, 'wh_price' => 105.00, 'net_price' => 98.00],
            ['load' => 'LD-2026-003', 'prod' => 'MAT-018', 'qty' => 48, 'free_qty' => 2, 'wh_price' => 730.00, 'net_price' => 680.00],
            ['load' => 'LD-2026-003', 'prod' => 'MAT-020', 'qty' => 30, 'free_qty' => 1, 'wh_price' => 390.00, 'net_price' => 355.00],
            ['load' => 'LD-2026-004', 'prod' => 'MAT-026', 'qty' => 48, 'free_qty' => 2, 'wh_price' => 420.00, 'net_price' => 385.00],
            ['load' => 'LD-2026-004', 'prod' => 'MAT-031', 'qty' => 60, 'free_qty' => 5, 'wh_price' => 380.00, 'net_price' => 355.00],
            ['load' => 'LD-2026-005', 'prod' => 'MAT-002', 'qty' => 24, 'free_qty' => 1, 'wh_price' => 2750.00, 'net_price' => 2600.00],
            ['load' => 'LD-2026-005', 'prod' => 'MAT-006', 'qty' => 60, 'free_qty' => 3, 'wh_price' => 235.00, 'net_price' => 215.00],
            ['load' => 'LD-2026-006', 'prod' => 'MAT-016', 'qty' => 40, 'free_qty' => 2, 'wh_price' => 385.00, 'net_price' => 355.00],
            ['load' => 'LD-2026-007', 'prod' => 'MAT-021', 'qty' => 144, 'free_qty' => 12, 'wh_price' => 118.00, 'net_price' => 108.00],
            ['load' => 'LD-2026-008', 'prod' => 'MAT-036', 'qty' => 100, 'free_qty' => 5, 'wh_price' => 105.00, 'net_price' => 95.00],
            ['load' => 'LD-2026-009', 'prod' => 'MAT-040', 'qty' => 48, 'free_qty' => 2, 'wh_price' => 660.00, 'net_price' => 610.00],
            ['load' => 'LD-2026-010', 'prod' => 'MAT-046', 'qty' => 80, 'free_qty' => 4, 'wh_price' => 315.00, 'net_price' => 290.00],
        ];

        foreach ($loadListItemsData as $lli) {
            $loadObj = $loadings[$lli['load']];
            $batchObj = $batches[$lli['prod']];

            LoadListItem::firstOrCreate(
                [
                    'loading_id' => $loadObj->id,
                    'batch_id'   => $batchObj->id,
                ],
                [
                    'qty'        => $lli['qty'],
                    'free_qty'   => $lli['free_qty'],
                    'wh_price'   => $lli['wh_price'],
                    'net_price'  => $lli['net_price'],
                ]
            );
        }

        // -------------------------------------------------------------
        // 12. LOADING RETURNS (10 returns)
        // -------------------------------------------------------------
        $loadingReturnsData = [
            ['load' => 'LD-2026-001', 'prod' => 'MAT-005', 'qty' => 5, 'days_ago' => 10, 'reason' => 'Outer carton damp in monsoon rain'],
            ['load' => 'LD-2026-002', 'prod' => 'MAT-015', 'qty' => 12, 'days_ago' => 8, 'reason' => 'Shop requested exchange for Curry flavor'],
            ['load' => 'LD-2026-003', 'prod' => 'MAT-018', 'qty' => 3, 'days_ago' => 6, 'reason' => 'Minor dent on tin lid'],
            ['load' => 'LD-2026-004', 'prod' => 'MAT-031', 'qty' => 8, 'days_ago' => 4, 'reason' => 'Customer reduced order quantity at delivery'],
            ['load' => 'LD-2026-009', 'prod' => 'MAT-040', 'qty' => 24, 'days_ago' => 3, 'reason' => 'Delivery truck broke down on route'],
            ['load' => 'LD-2026-010', 'prod' => 'MAT-046', 'qty' => 40, 'days_ago' => 3, 'reason' => 'Road block caused route cancellation'],
            ['load' => 'LD-2026-001', 'prod' => 'MAT-001', 'qty' => 2, 'days_ago' => 9, 'reason' => 'Store shelf space overstocked'],
            ['load' => 'LD-2026-002', 'prod' => 'MAT-010', 'qty' => 6, 'days_ago' => 7, 'reason' => 'Merchant requested credit note'],
            ['load' => 'LD-2026-003', 'prod' => 'MAT-020', 'qty' => 4, 'days_ago' => 5, 'reason' => 'Packet seal defect detected'],
            ['load' => 'LD-2026-004', 'prod' => 'MAT-026', 'qty' => 2, 'days_ago' => 3, 'reason' => 'Incorrect invoice pricing addressed'],
        ];

        foreach ($loadingReturnsData as $lr) {
            $loadObj = $loadings[$lr['load']];
            $batchObj = $batches[$lr['prod']];

            LoadingReturn::firstOrCreate(
                [
                    'loading_id'  => $loadObj->id,
                    'batch_id'    => $batchObj->id,
                    'return_date' => Carbon::now()->subDays($lr['days_ago'])->toDateString(),
                ],
                [
                    'qty'    => $lr['qty'],
                    'reason' => $lr['reason'],
                ]
            );
        }

        // -------------------------------------------------------------
        // 13. DIRECT SALES & SALE ITEMS (12 sales + 24 items)
        // -------------------------------------------------------------
        $cashierUser = $users['cashier'];
        $salesData = [
            ['total' => 3800.00, 'discount' => 0.00, 'pay' => 'cash', 'hours_ago' => 2, 'items' => [
                ['prod' => 'MAT-001', 'qty' => 2, 'retail' => 1250.00, 'unit' => 1250.00, 'tot' => 2500.00],
                ['prod' => 'MAT-005', 'qty' => 2, 'retail' => 380.00, 'unit' => 380.00, 'tot' => 760.00],
                ['prod' => 'MAT-015', 'qty' => 4, 'retail' => 120.00, 'unit' => 120.00, 'tot' => 480.00],
            ]],
            ['total' => 6580.00, 'discount' => 100.00, 'pay' => 'card', 'hours_ago' => 5, 'items' => [
                ['prod' => 'MAT-002', 'qty' => 2, 'retail' => 2950.00, 'unit' => 2950.00, 'tot' => 5900.00],
                ['prod' => 'MAT-017', 'qty' => 1, 'retail' => 690.00, 'unit' => 690.00, 'tot' => 690.00],
            ]],
            ['total' => 2140.00, 'discount' => 0.00, 'pay' => 'cash', 'hours_ago' => 8, 'items' => [
                ['prod' => 'MAT-010', 'qty' => 2, 'retail' => 390.00, 'unit' => 390.00, 'tot' => 780.00],
                ['prod' => 'MAT-020', 'qty' => 1, 'retail' => 420.00, 'unit' => 420.00, 'tot' => 420.00],
                ['prod' => 'MAT-024', 'qty' => 1, 'retail' => 490.00, 'unit' => 490.00, 'tot' => 490.00],
                ['prod' => 'MAT-031', 'qty' => 1, 'retail' => 420.00, 'unit' => 420.00, 'tot' => 420.00],
            ]],
            ['total' => 1560.00, 'discount' => 0.00, 'pay' => 'cash', 'hours_ago' => 14, 'items' => [
                ['prod' => 'MAT-011', 'qty' => 2, 'retail' => 320.00, 'unit' => 320.00, 'tot' => 640.00],
                ['prod' => 'MAT-023', 'qty' => 4, 'retail' => 230.00, 'unit' => 230.00, 'tot' => 920.00],
            ]],
            ['total' => 8450.00, 'discount' => 250.00, 'pay' => 'card', 'hours_ago' => 20, 'items' => [
                ['prod' => 'MAT-003', 'qty' => 3, 'retail' => 820.00, 'unit' => 820.00, 'tot' => 2460.00],
                ['prod' => 'MAT-018', 'qty' => 4, 'retail' => 780.00, 'unit' => 780.00, 'tot' => 3120.00],
                ['prod' => 'MAT-034', 'qty' => 4, 'retail' => 650.00, 'unit' => 650.00, 'tot' => 2600.00],
            ]],
            ['total' => 4120.00, 'discount' => 0.00, 'pay' => 'cash', 'hours_ago' => 26, 'items' => [
                ['prod' => 'MAT-026', 'qty' => 4, 'retail' => 450.00, 'unit' => 450.00, 'tot' => 1800.00],
                ['prod' => 'MAT-028', 'qty' => 4, 'retail' => 220.00, 'unit' => 220.00, 'tot' => 880.00],
                ['prod' => 'MAT-040', 'qty' => 2, 'retail' => 720.00, 'unit' => 720.00, 'tot' => 1440.00],
            ]],
            ['total' => 3240.00, 'discount' => 50.00, 'pay' => 'card', 'hours_ago' => 32, 'items' => [
                ['prod' => 'MAT-006', 'qty' => 4, 'retail' => 260.00, 'unit' => 260.00, 'tot' => 1040.00],
                ['prod' => 'MAT-007', 'qty' => 5, 'retail' => 240.00, 'unit' => 240.00, 'tot' => 1200.00],
                ['prod' => 'MAT-008', 'qty' => 4, 'retail' => 220.00, 'unit' => 220.00, 'tot' => 880.00],
            ]],
            ['total' => 2890.00, 'discount' => 0.00, 'pay' => 'cash', 'hours_ago' => 48, 'items' => [
                ['prod' => 'MAT-043', 'qty' => 3, 'retail' => 480.00, 'unit' => 480.00, 'tot' => 1440.00],
                ['prod' => 'MAT-044', 'qty' => 2, 'retail' => 320.00, 'unit' => 320.00, 'tot' => 640.00],
                ['prod' => 'MAT-045', 'qty' => 2, 'retail' => 290.00, 'unit' => 290.00, 'tot' => 580.00],
            ]],
            ['total' => 1840.00, 'discount' => 0.00, 'pay' => 'cash', 'hours_ago' => 60, 'items' => [
                ['prod' => 'MAT-046', 'qty' => 2, 'retail' => 340.00, 'unit' => 340.00, 'tot' => 680.00],
                ['prod' => 'MAT-047', 'qty' => 2, 'retail' => 380.00, 'unit' => 380.00, 'tot' => 760.00],
                ['prod' => 'MAT-048', 'qty' => 1, 'retail' => 290.00, 'unit' => 290.00, 'tot' => 290.00],
            ]],
            ['total' => 3940.00, 'discount' => 0.00, 'pay' => 'card', 'hours_ago' => 72, 'items' => [
                ['prod' => 'MAT-050', 'qty' => 4, 'retail' => 980.00, 'unit' => 980.00, 'tot' => 3920.00],
            ]],
            ['total' => 5200.00, 'discount' => 100.00, 'pay' => 'cash', 'hours_ago' => 96, 'items' => [
                ['prod' => 'MAT-032', 'qty' => 4, 'retail' => 420.00, 'unit' => 420.00, 'tot' => 1680.00],
                ['prod' => 'MAT-033', 'qty' => 4, 'retail' => 440.00, 'unit' => 440.00, 'tot' => 1760.00],
                ['prod' => 'MAT-035', 'qty' => 2, 'retail' => 890.00, 'unit' => 890.00, 'tot' => 1780.00],
            ]],
            ['total' => 1960.00, 'discount' => 0.00, 'pay' => 'cash', 'hours_ago' => 120, 'items' => [
                ['prod' => 'MAT-037', 'qty' => 6, 'retail' => 130.00, 'unit' => 130.00, 'tot' => 780.00],
                ['prod' => 'MAT-038', 'qty' => 6, 'retail' => 130.00, 'unit' => 130.00, 'tot' => 780.00],
                ['prod' => 'MAT-039', 'qty' => 1, 'retail' => 280.00, 'unit' => 280.00, 'tot' => 280.00],
            ]],
        ];

        foreach ($salesData as $sd) {
            $sale = Sale::create([
                'date_time'    => Carbon::now()->subHours($sd['hours_ago']),
                'user_id'      => $cashierUser->id,
                'total'        => $sd['total'],
                'discount'     => $sd['discount'],
                'payment_type' => $sd['pay'],
            ]);

            foreach ($sd['items'] as $item) {
                $pObj = $products[$item['prod']];
                $bObj = $batches[$item['prod']];

                SaleItem::create([
                    'sale_id'      => $sale->id,
                    'batch_id'     => $bObj->id,
                    'product_id'   => $pObj->id,
                    'qty'          => $item['qty'],
                    'retail_price' => $item['retail'],
                    'unit_price'   => $item['unit'],
                    'total'        => $item['tot'],
                    'discount'     => 0.00,
                ]);
            }
        }
    }
}
