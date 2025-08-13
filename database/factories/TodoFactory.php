<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

use App\Models\User;
use App\Models\Todos;

use Illuminate\Support\Arr;

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
        $activities = ['READ',
                    'WRITE',
                    'BUY',
                    'SELL',
                    'CALL',
                    'EMAIL',
                    'MEET',
                    'VISIT',
                    'PAY',
                    'WATCH',
                    'LEARN',
                    'FIX',
                    'BUILD',
                    'PLAN',
                    'CLEAN',
                    'ORGANIZE',
                    'PICKUP',
                    'DROP',
                    'RESEARCH',
                    'PLAY'];

        
        return [
            // 'user_id' => U,
            'content' => "I want to " . Arr::random($activities). " a " . fake()->word(),    // Pass FALSE to force the sentence to only contain three words
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
