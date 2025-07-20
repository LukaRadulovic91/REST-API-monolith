<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Client;

/**
 * Class ClientSeeder
 *
 * @package Database\Seeders
 */
class ClientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Client::factory(10)->create();
    }
}
