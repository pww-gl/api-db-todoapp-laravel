<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

use App\Models\User;
use App\Models\Todo;

use PHPUnit\Framework\Attributes\Groups;
use PHPUnit\Framework\Attributes\Test;

#[Group ('TodoCRUD')]
class TodoCrudTest extends TestCase    
{
    
    use RefreshDatabase;
    
    /**
     * A basic feature test example.
     */
    #[Test]
    public function creating_a_new_todo(): void
    {
        $user = User::factory()->create();
        
        $newTodo =  [
                'user_id' => $user->id,
                'content' => fake()->sentence(3),
                'deadline' => fake()->dateTimeBetween('now', '+4 months')->format('Y-m-d H:i:s'),
                'visibility' => 'public'
        ];

        $response = $this->actingAs($user)
            ->followingRedirects()
            ->post('/users/{user}/todos', $newTodo);
        // dd($response);

        $this->assertDatabaseHas('todos', [
            'user_id' => $user->id
        ]);

        $response->assertStatus(200);
        
        $response->assertViewHas('todos', $user->todos);
    }

    #[Test]
    public function duplicate_content() 
    {
        //
    }
}
