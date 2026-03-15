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
        // Create initial permissions
        $permissions = [
            ['name' => 'manage-assets', 'display_name' => 'Manage Printers & Locations'],
            ['name' => 'manage-inventory', 'display_name' => 'Manage Inventory & Stock'],
            ['name' => 'process-requests', 'display_name' => 'Approve & Fulfill Requests'],
            ['name' => 'view-dashboard', 'display_name' => 'View Analytics Dashboard'],
            ['name' => 'generate-reports', 'display_name' => 'Generate Reports'],
        ];

        foreach ($permissions as $p) {
            \App\Models\Permission::create($p);
        }

        // Create the Super User
        \App\Models\User::factory()->create([
            'name' => 'Full Administrator',
            'email' => 'admin@flyafricaworld.com',
            'password' => bcrypt('AdminPassword123!'),
            'is_superuser' => true,
        ]);
    }
}
