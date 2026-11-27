<?php

namespace Database\Seeders;

use App\Models\Faq;
use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     *
     * Local development data only; both accounts use the factory password "password".
     */
    public function run(): void
    {
        if (app()->isProduction()) {
            $this->command?->error('DatabaseSeeder creates demo accounts with the password "password" and must not run in production. Use PageSeeder only.');

            return;
        }

        User::factory()->admin()->create([
            'name' => 'Admin Magang',
            'email' => 'admin@magang.test',
        ]);

        User::factory()->create([
            'name' => 'Editor Konten',
            'email' => 'editor@magang.test',
        ]);

        $this->call([
            PageSeeder::class,
            ProgramSeeder::class,
        ]);

        Faq::factory()->count(6)->create();
        Post::factory()->count(4)->create();
    }
}
