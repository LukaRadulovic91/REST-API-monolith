<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Class LanguagesSeeder
 *
 * @package Database\Seeders
 */
class LanguagesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach ($this->getLanguages() as $language) {
            DB::table('languages')->insert([
                'title' => $language,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ]);
        }
    }

    private function getLanguages()
    {
        return [
            'English', 'French', 'Greek', 'Italian', 'Polish', 'Portuguese', 'Hindi', 'Tamil', 'Urdu',
            'Chinese', 'Spanish', 'Arabic', 'Persian', 'German', 'Russian', 'Malay', 'Turkish', 'Korean',
            'Bengali', 'Japanese', 'Vietnamese', 'Other'
        ];
    }
}
