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
     *
     * Local development accounts only; both use the factory password "password".
     */
    public function run(): void
    {
        User::factory()->admin()->create([
            'name' => 'Admin Magang',
            'email' => 'admin@magang.test',
        ]);

        User::factory()->create([
            'name' => 'Editor Konten',
            'email' => 'editor@magang.test',
        ]);
    }
}
