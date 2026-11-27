<?php

namespace Database\Seeders;

use App\Models\Program;
use Illuminate\Database\Seeder;

/**
 * Sample programs for local development only; real programs are entered in the admin.
 */
class ProgramSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Program::factory()->count(3)->create();
        Program::factory()->closed()->create();
        Program::factory()->draft()->create();
    }
}
