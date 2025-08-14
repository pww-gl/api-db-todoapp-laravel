<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\User;

use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Response;

use Intervention\Image\Laravel\Facades\Image;
use Carbon\Carbon;
use Barryvdh\Debugbar\Facades\Debugbar;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\Rules\Password;

use function PHPUnit\Framework\assertNotEquals;

// use Illuminate\Support\MessageBag;

class UserController extends Controller
{
    
    public function register(Request $request): JsonResponse 
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users'],
            'password'=>['required', 
                Password::min(12)
                    ->max(64)
                    ->mixedCase()
                    ->numbers()
            ] 
        ]);
        
        $user =  User::create($validated);
        
        $token = $user->createToken( $request->header('User-Agent') . "-" . (string) $user->name );
        
        return response()->json([
            'message' => 'New user has been created',
            'data' => [
                'name' => $user->name,
                'email' => $user->email,
                'token' => $token->plainTextToken
            ]
        ], 201, [
            // 'Location' =>
        ]);
    }
    
    public function login(Request $request, User $user): JsonResponse
    {
        $credentials = $request->validate([
            'email' => ['required','email'],
            'password' => ['required',
                Password::min(12)
                    ->max(64)
                    ->mixedCase()
                    ->numbers()
                ],
        ]);

        if (Auth::attempt($credentials) !== true) {
            return response()->json([
                'error' => 'The credentials are incorrect' 
            ], 401);
        }

        $tokenName = ($request->header('User-Agent') . "-" . (string) Auth::user()->name);
        
        if ($token = Auth::user()->tokens()->where('name', $tokenName)->first()) {
            // User::where('id', 1)->first()->tokens->filter(fn($token)=>$token->name===$tokenName);    // TICKET: Check if for each API, there is already token associated
            // $token->name
            // token
            // created_at
            // updated_at;
            $user->tokens()->where('name', $tokenName)->delete();
        }

        $token = Auth::user()->createToken($tokenName);
        return response()->json([
            'message' => 'Access token for the user has been generated',
            'data' => ['token' => $token->plainTextToken]
        ], 200);
    }

    public function logout(Request $request, User $user): JsonResponse 
    {
        $request->user()->currentAccessToken()->delete();
        
        return response()->json([
            'message' => 'Access token for the user has been deleted'
        ]);
    }

    /**
     *  Kirim data user buat nampilin Profile Page
     */
    public function showUser(): JsonResponse
    {    
        $user = Auth::user();
        $signedUrl = $user->avatar_url;


        // if ($avatarUrl) {
            // $signedUrl = Storage::disk('s3')->temporaryUrl(
                // $user->avatar_url, now()->addMinutes(2)
            // );
        // }

        return response()->json([
            'name'=>$user->name,
            'email'=>$user->email,
            'avatar_url'=>$signedUrl ?? null
        ]);
    }

    /**
     *  Merubah data user di profile page
     */
    public function updateProfile(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['sometimes', 'string', 'max:256'],
            'email' => ['sometimes', 'email'],
            'password'=> [
                'sometimes',
                Password::min(12)->max(64)
                    ->mixedCase()
                    ->numbers()
                ],
            'avatar_picture' => [
                'sometimes', 
                'mimes:jpg,jpeg,png',
                'extensions:jpg,jpeg,png',
                'max:1000',
                'dimensions:ratio=1/1'
                ]
        ]);

        $user = Auth::user();

        /**
         *  Menyimpan foto ke s3
         */
        if (array_key_exists('avatar_picture', $validated)){
            $avatarUploaded = $request->file('avatar_picture');
            $imageName = $user->id . '.' . $avatarUploaded->extension();
            $avatarUrl = $request->file('avatar_picture')->storeAs('/avatars', $imageName, 's3');  // To specify disk, add a third argument for storeAs() method
        } else {$avatarUrl = null;}
          
        /**
         *  Menyimpan data baru to DB
         */
        $user->name = $validated['name'] ?? $user->name;
        $user->email = $validated['email'] ?? $user->email;        
        $user->password = $validated['password'] ?? $user->password;
        $user->avatar_url = $avatarUrl ?? $user->avatar_url;
        $user->save();

        $user = User::where('id', Auth::id())->first();

        return response()->json([
            'message'=>"User's data has been updated",
            'data'=> ['user' => [ 
                'name' => $user->name,
                'email' => $user->email,
                'avatar_url' => $user->avatar_url
                ]
            ]
        ]);
    }
}