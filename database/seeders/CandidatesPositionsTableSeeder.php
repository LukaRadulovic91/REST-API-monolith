<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Class CandidatesPositionsTableSeeder
 *
 * @package Database\Seeders
 */
class CandidatesPositionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $candidates = DB::table('candidates')->select('id', 'position_id')->get();

        foreach ($candidates as $candidate) {
            DB::table('candidates_positions')->insert([
                'candidate_id' => $candidate->id,
                'position_id' => $candidate->position_id,
            ]);
        }
    }
}
