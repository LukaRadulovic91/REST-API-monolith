<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Class SoftwareSeeder
 *
 * @package Database\Seeders
 */
class SoftwareSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach ($this->getSoftwares() as $software) {
            DB::table('softwares')->insert([
                'title' => $software,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ]);
        }
    }

    private function getSoftwares()
    {
        return [
            'ABEL Dent', 'Dentrix', 'Tracker', 'Logitec Paradigm', 'Autopia', 'Opus', 'Curve', 'Exan', 'Other'
        ];
    }
}
