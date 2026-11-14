<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class TestRoleAccountsSeeder extends Seeder
{
    public function run(): void
    {
        $accounts = [
            ['username' => 'test_owner', 'name' => 'Test Owner', 'email' => 'test.owner@ims.local', 'phone' => '0779000001', 'role' => 'super_admin', 'password' => 'OwnerTest@2026!'],
            ['username' => 'test_admin', 'name' => 'Test Admin', 'email' => 'test.admin@ims.local', 'phone' => '0779000002', 'role' => 'admin', 'password' => 'AdminTest@2026!'],
            ['username' => 'test_entry', 'name' => 'Test Data Entry', 'email' => 'test.entry@ims.local', 'phone' => '0779000003', 'role' => 'data_entry', 'password' => 'EntryTest@2026!'],
            ['username' => 'test_cashier', 'name' => 'Test Cashier', 'email' => 'test.cashier@ims.local', 'phone' => '0779000004', 'role' => 'cashier', 'password' => 'CashierTest@2026!'],
        ];

        foreach ($accounts as $account) {
            User::updateOrCreate(
                ['username' => $account['username']],
                [
                    ...$account,
                    'password' => Hash::make($account['password']),
                ],
            );
        }
    }
}
