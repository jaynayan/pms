<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Insert Default Roles
        \Illuminate\Support\Facades\DB::table('roles')->insertOrIgnore([
            ['id' => 1, 'name' => 'Admin'],
            ['id' => 2, 'name' => 'PM'],
            ['id' => 3, 'name' => 'Team Member'],
            ['id' => 4, 'name' => 'Viewer'],
        ]);
    }
}
