<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

use PHPUnit\Framework\Attributes\Groups;
use PHPUnit\Framework\Attributes\Test;

use App\Models\User;
use App\Models\Todo;

use Illuminate\Support\Facades\Hash;

#[Group ('Timeline')]
class BaseTimelineTest extends TestCase
{
    
    use RefreshDatabase;

    /**
     *  Test to access timenline
     */
    #[Test]
    public function accessing_to_timeline(): void   
    {     
        /**
         *  Temporarily create User objects
         */
        $user = User::factory()->create([
            'password' => Hash::make('Abcd12341234')
        ]);

        /**
         *  Temporarily create Todos
         */
        foreach ($users as $user) {  
            $user->todos = Todo::factory()
                ->create([
                    'user_id' => $user->id,
                    'content' => fake()->sentence(3),
                    'deadline' => fake()->dateTime('now', '+5 months')->format('Y-m-d H:i:s'),
                    'visibility' => 'public',
                    'who_liked' => []  //New Column
                    // 'who_dislikes' => [],   //New Column
                ]);    
        }

        /**
         *  What happend if GET /public/timeline endpoint is fired?
         *  Below is to simulate that triggering
         */
        $response = $this->get('/public/timeline');
        
        /**
         *  !!! TONS of assertions bellow !!!
         */

        // CHECK Retured Status Code
        $response->assertStatus(200);
        
        // CHECK Returned blade.php view
        $response->assertViewIs('timeline');
        

        // SUMMON all todos with public visibility
        $publicTodos = Todo::where('visibility','public')
                ->orderBy('created_at')
                ->get();

        $superTodos = []
        
        // CHECK Returned Todo entries/rows/objects, only those publicly visible and sorted by created_at 
        $response->assertViewHasAll([
            'user' => Auth::user() ?? null,
            'todos' => $publicTodos
        ]);

        // Assert that avatar URL for for each todo, User that own it has their avatar_url. How to check if the picture there??
        foreach ($superTodos as $todo) {
            $response->assertViewHas($todo['user_avatar'], User::where('id', $todo->user_id)  
                ->first()
                ->avatar_url) ?? null;
        }

        // BE12-TDD-1 Assert if like button is shown approriately, depending if user has liked or not
        if (in_array(Auth::id(), $todos->who_liked)) {
            $response->assertSee('Liked');  // Assert that if user liked the todo, the todo will show 'Liked'
        } else {
            $response->assertSee('Like');   // Assertion when user has NOT liked the todo, the todo will show 'Like'
        }

        // Assert that 'likes count' exist: If server-driven, count should be included in $superTodos element; that is for each todo
        foreach ($superTodos as $todo) {
            $response->assertViewHas($todo['likes_count'], count($todo->who_liked));   // Assert that view has likes counter for each todo;
        }
        
    }

    /**
     *  TEST- reacting to todos (currently only like and dislike)
     */
    #[Test]
    public function liking_todos(): void 
    {
        $users = User::factory()
            ->count(3)
            ->create([
                'password' => Hash::make('Abcd12341234')
            ]);
        
        $sampleTodos = Todo::where('visibility', 'public')->get();

        /**
         *  For all 
         */
        foreach ($sampleTodos as $todo) {
            foreach ($users as $user) {
                
                /**
                 *  Create Response object
                 */
                $response = $this->actingAs($user)
                    ->post('public/{$todo->id}/like', [
                        'like'=>TRUE
                    ]);

                /**
                 *  ALL assertions to test are below
                 */
                $response->assertRedirectBack();    // Assert redirection
        
                }

            }
        }


        // response = $this->post('/public/timeline/react', [
        //    'like'
        // );



    }

    #[Test]
    public function edit_todos(): void
    {
        //
    }
    
    
}
