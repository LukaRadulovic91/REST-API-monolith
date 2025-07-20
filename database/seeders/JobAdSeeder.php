<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\JobAd;

/**
 * Class JobAdSeeder
 *
 * @package Database\Seeders
 */
class JobAdSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        JobAd::factory(20)->create();
    }
}
