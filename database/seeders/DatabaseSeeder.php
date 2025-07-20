<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

/**
 * Class DatabaseSeeder
 *
 * @package Database\Seeders
 */
class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            AdministrationSeeder::class,
            SoftwareSeeder::class,
            PositionsSeeder::class,
            LanguagesSeeder::class,
            UserSeeder::class,
//            ClientSeeder::class,
//            CandidateSeeder::class,
//            CandidatesDesiredPositionsSeeder::class,
//            JobAdSeeder::class,
            ProfileStatusesSeeder::class
        ]);

    }
}
