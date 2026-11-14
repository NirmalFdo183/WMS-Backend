<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // SQLite stores enum columns as text. Fresh SQLite databases already
        // receive the expanded definition from the original users migration.
        if (DB::getDriverName() === 'sqlite') {
            return;
        }

        DB::statement("ALTER TABLE users MODIFY role ENUM('super_admin', 'admin', 'data_entry', 'cashier', 'user', 'staff', 'rep') NOT NULL DEFAULT 'cashier'");

        // Preserve a usable owner account for installations created before
        // this role model existed. Other existing administrators stay admins.
        DB::table('users')
            ->where('username', 'admin')
            ->where('role', 'admin')
            ->update(['role' => 'super_admin']);
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'sqlite') {
            return;
        }

        DB::statement("UPDATE users SET role = 'admin' WHERE role IN ('super_admin', 'data_entry')");
        DB::statement("ALTER TABLE users MODIFY role ENUM('admin', 'user', 'staff', 'rep', 'cashier') NOT NULL DEFAULT 'user'");
    }
};
