<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\User;

/**
 * Class ProfileStatusesSeeder
 *
 * @package Database\Seeders
 */
class ProfileStatusesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();
        $statuses = $this->getStatuses();
        foreach ($users as $user) {
            DB::table('profile_statuses')->insert([
                'user_id' => $user->id,
                'status' => $user->profile_status_id,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ]);
        }
    }

    private function getStatuses()
    {
        return [ 1, 2, 3 ];
    }
}
