<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Todo;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use Illuminate\Database\Eloquent\Factories\Sequence;

class UserSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();
        // $name = [];
        // for ($x = 0; $x <= 10; $x++) {
            // if ($x <=10) {
                // $name[] = fake()->firstName('female');
            // } else {
                // $name[] = fake()->firstName('male');
            // }
        // }

        User::factory()->count(5)
            // ->state(new Sequence(
                // fn(Sequence $sequence) => ['name' => $name]
            // ))
            ->state(new Sequence (
                fn(Sequence $sequence) => ['avatar_url' => '/avatars/' . $sequence->index + 1 .'.jpg'] 
            ))
            ->has(Todo::factory()->count(5))
            ->create();
    }
}
