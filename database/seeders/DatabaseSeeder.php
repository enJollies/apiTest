<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Section;
use App\Models\User;

class DatabaseSeeder extends Seeder
{

    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {

        $sections = Section::factory()->count(10)->create();
        User::factory()
        ->hasAttached($sections->random(4))
        ->create(['email' => 'admin@admin.com']);

        User::factory()
        ->count(5)
        ->hasAttached($sections->random(3))
        ->create();
    }
}
