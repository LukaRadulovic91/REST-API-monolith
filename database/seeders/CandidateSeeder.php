<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Candidate;

/**
 * Class CandidateSeeder
 *
 * @package Database\Seeders
 */
class CandidateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Candidate::factory(10)->create();
    }
}
