<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
// use PHPUnit\Framework\TestCase;
use App\Models\User;
use App\Models\Todo;
use Illuminate\Support\Facades\Hash;

#[Group ('AppAccess')]
class AppAccessTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function access_homepage_without_authentication(): void
    {   
        $response = $this->followingRedirects()->get('/');

        $response->assertStatus(200);
        $response->assertViewIs('login');
        $response->assertSee('login');

    }

    #[Test]
    public function user_logging_in(): void
    {   
        $user = User::factory()->create([
            'password' => Hash::make('Abcd12341234')
        ]);

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'Abcd12341234'
        ]);

        // $response->assertStatus(200);
        $response->assertRedirectToRoute('home-page');
        // $response->assertSee('Welcome, ' . $user->name);
        $this->assertAuthenticatedAs($user);
    }

    #[Test]
    public function authenticated_access_to_homepage()
    {
        $user = User::factory()->create([
            'avatar_url'=>'image-test.com'
        ]);

        for ($x = 0; $x <= 4; $x++) {
            $todo = Todo::factory()->create([
                'user_id' => $user->id,
                'content' => fake()->sentence(3),
                'deadline' => fake()->dateTimeBetween('now', '+5 months')->format('Y-m-d H:i:s')
            ]);
            $todos[] = $todo;
        }

        // dd($user->todos);
        // dd(count($todos));

        $response = $this->actingAs($user)
            // ->withSession()
            ->get('/');
        

        $response->assertStatus(200);
        $response->assertViewIs('home');
        $response->assertSee('Welcome, ' . $user->name);
        $response->assertViewHasAll([
            'todos'=>$user->todos,
            'avatar_url'=>
                \Illuminate\Support\Facades\Storage::disk('s3')->temporaryUrl(
                $user->avatar_url, now()->addMinutes(2)
            )
        ]);
    }

    #[Test]
    public function user_registering() 
    {
        //
    }

    #[Test]
    public function user_logging_out() 
    {
        //
    }

    #[Test]
    public function user_accessing_profile() 
    {
        //
    }
    
}   