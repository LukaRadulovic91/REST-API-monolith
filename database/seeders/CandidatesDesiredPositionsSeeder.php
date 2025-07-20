<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Candidate;

/**
 * Class CandidatesDesiredPositionsSeeder
 *
 * @package Database\Seeders
 */
class CandidatesDesiredPositionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $candidates = Candidate::get();

        foreach ($candidates as $candidate) {
            DB::table('candidates_desired_position')->insert([
                'candidate_id' => $candidate->id,
                'desired_position_id' => fake()->numberBetween(1,2),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ]);
        }
    }
}
