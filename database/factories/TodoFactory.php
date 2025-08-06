<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

use App\Models\User;
use App\Models\Todos;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Todo>
 */
class TodoFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::all()->random()->id,
            'content' => fake()->sentence(3, false),    // Pass FALSE to force the sentence to only contain three words
            'deadline' => fake()->dateTimeBetween('now', '+5 months')->format('Y-m-d H:i:s'),
            'visibility' => 'public',
            // 'is_done' => ,
            // 'is_edited' => ,
            // 'who_liked => ,

        ];
    }

    // /**
    //  *  Make a state for user_id column, based on number of available user
    //  */
    // public function userId(): Factory
    // {
        // $this->state(fn(array ))
    // }
}
