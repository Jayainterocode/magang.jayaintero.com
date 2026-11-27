<?php

namespace Database\Seeders;

use App\Models\Page;
use Illuminate\Database\Seeder;

/**
 * Starter copy for the editable pages. Safe to run in production: existing pages are left as they are.
 */
class PageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Page::query()->firstOrCreate(['key' => 'home'], [
            'title' => 'Magang di proyek nyata',
            'intro' => 'Untuk siswa SMK dan mahasiswa yang ingin belajar langsung di dunia renovasi, interior, dan material bangunan bersama tim Jaya Intero.',
            'body' => "Peserta magang ikut bekerja bersama tim Jaya Intero di Medan.\n\nUbah teks ini dari menu **Halaman** di admin.",
        ]);

        Page::query()->firstOrCreate(['key' => 'about'], [
            'title' => 'Tentang Magang',
            'intro' => 'Kenapa magang di Jaya Intero, bidang kerja, dan aturan umum selama magang.',
            'body' => "## Bidang kerja\n\nTulis bidang kerja peserta magang di sini.\n\n## Aturan umum\n\nTulis aturan umum selama magang di sini.\n\nUbah teks ini dari menu **Halaman** di admin.",
        ]);
    }
}
