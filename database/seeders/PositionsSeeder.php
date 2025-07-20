<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Class PositionsSeeder
 *
 * @package Database\Seeders
 */
class PositionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach ($this->getPositions() as $position) {
            DB::table('positions')->insert([
                'title' => $position,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ]);
        }
    }

    private function getPositions()
    {
        return [
            'Hygienist', 'DA', 'CDA (Have NDAEB certificate)', 'Receptionist', 'Treatment Coordinator',
            'Manager', 'Associate Dentist', 'Nurse'
        ];
    }
}
