<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

use App\Models\User;
use App\Models\Subscription;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // \App\Models\User::factory(10)->create();
        $users = User::factory(10)->create();

        $subscriptions = Subscription::factory(20)->create();

        // attach Users 2 Subscriptions
        foreach ($subscriptions as $subscription) {
            $subscription->users()->attach(
                $users->random(rand(1, 5))->pluck('id')->toArray()
            );
        }
    }
}
