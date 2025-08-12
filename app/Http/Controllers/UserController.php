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
use Illuminate/Support/Facades/Response;

use Intervention\Image\Laravel\Facades\Image;
use Carbon\Carbon;
use Barryvdh\Debugbar\Facades\Debugbar;

use function PHPUnit\Framework\assertNotEquals;

// use Illuminate\Support\MessageBag;

class UserController extends Controller
{
    
    public function authenticate(Request $request): JSON
    {
        if (!$request->hasHeader('')
        $userEmail = $request->input('email');
        $user = User::where('email', '=', $userEmail)
        ->first();

        $credentials = $request->validate([
        'email' => ['required','email'],
        'password' => ['required',
            Password::min(12)
                ->max(64)
                ->mixedCase()
                ->numbers()],
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            $id = Auth::id();
            return redirect()->intended('/');
        }

        return back()->json([
            'error' => ''
        ]);
    }
    public function logout(Request $request): RedirectResponse 
    {
        Auth::logout();
        
        $request->session()->invalidate();
        $request->session()->regenerateToken(); // Regenerate CSRF TOKEN
        
        return redirect()->route('login');
    }
    assert
    public function register(Request $request) 
    {
        $validator = Validator::make($request->all(), [
            'name'=>'required',
            'email'=>'required|email|unique:users',
            'password'=>'sometimes|regex:/(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[a-zA-Z\d]{11,}/m' 
        ]);

        if ($validator->fails()) {
            return redirect()
            ->back()
            ->withErrors($validator)
            ->withInput()
            ->with('form','register');
        }
        
        $validated = $validator->validated();
        
        $user = User::create($validated);
        Auth::login($user);
        return redirect()->json([
            
        ]);
    }

    // function ganti password
    
    // function ganti nama

    /**
     * PRIORITAS
     * function ganti profile picture
     * fokus, manipulasi file
     */


    /**
     *  Kirim data untuk Home-Page
     */
    public function homePage(Request $request): View
    {        
        // Debugbar::startMeasure("fetch", "UserController::homePage()");
        // 
        // $user = Auth::user();
        // $mode = $request->is_done;
        // $avatarUrl = $user->avatar_url;
// 
        // if ($avatarUrl) {
            // $signedUrl = Storage::disk('s3')->temporaryUrl(
                // $user->avatar_url, now()->addMinutes(2)
            // );
        // } 
        // else {
            // $avatarUrl = 'storage/avatar-placeholder.jpg';
        // }
        
        return response()->json([
            'user' => $user,
            'avatar_url' => $signedUrl ?? null,
            'todos' => $user->todos
                ->where('is_done', '=', $mode),
            'is_done'=>$mode,
        ]);

        // Debugbar::stopMeasure();
    }

    /**
     *  Kirim data user buat nampilin Profile Page
     */
    public function profilePage(): View 
    {    
        $user = Auth::user();
        $avatarUrl = $user->avatar_url;

        if ($avatarUrl) {
            $signedUrl = Storage::disk('s3')->temporaryUrl(
                $user->avatar_url, now()->addMinutes(2)
            );
        }

        return view('profile', [
            'name'=>$user->name,
            'email'=>$user->email,
            'avatar_url'=>$signedUrl ?? null
        ]);
    }

    /**
     *  Merubah data user di profile page
     */
    public function changeProfile(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'nullable|string|max:256',
            'password'=> 'nullable|regex:/(?=.*[a-z])(?=.*[A-Z])(?=.*[\d])[a-zA-Z\d]/m',
            'email' => 'nullable|email',  
            'avatar_picture' => 'sometimes|mimes:jpg,jpeg,png|extensions:jpg,jpeg,png|max:1000|dimensions:ratio=1/1'
        ]);

        $user = Auth::user();

        /**
         *  Menyimpan foto ke s3
         */
        if ($request->avatar_picture){
            $avatarUploaded = $request->file('avatar_picture');
            $imageName = $user->id . '.' . $avatarUploaded->extension();
            $avatarUrl = $request->file('avatar_picture')->storeAs('/avatars', $imageName, 's3');  // To specify disk, add a third argument for storeAs() method
        } else {$avatarUrl = null;}
          
        /**
         *  Menyimpan data baru to DB
         */
        $user = Auth::user();
        $user->name = $request->name ?? $user->name;
        $user->email = $request->email ?? $user->email;        
        $user->password = $request->password ?? $user->password;
        $user->avatar_url = $avatarUrl ?? $user->avatar_url;
        $user->save();

        return redirect()->back();
    }
}