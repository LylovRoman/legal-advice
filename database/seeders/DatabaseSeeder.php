<?php

namespace Database\Seeders;

use App\Models\Issue;
use Database\Factories\IssueFactory;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        \App\Models\User::factory(4)->create();
        \App\Models\User::factory(2)->create([
            'role' => 'lawyer'
        ]);
        Issue::factory(16)->create();
    }
}
