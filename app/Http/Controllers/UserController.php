<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\Laravel\Facades\Image;

class UserController extends Controller
{
    public function loginForm(): View 
    {
        return view('login');
    }
    
    public function authenticate(Request $request): RedirectResponse 
    {
        $userEmail = $request->input('email');
        $user = User::where('email', '=', $userEmail)
        ->first();

        if ($user && $user->password === NULL) {
            Auth::login($user);
            return redirect()->intended('/');
        }

        $credentials = $request->validate([
        'email' => 'required|email',
        'password' => 'required',
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            $id = Auth::id();
            return redirect()->intended('/');
        
        }

        return back()->withErrors([
            "errors"=>"The credentials are incorrect"
        ]);
    }

    public function logout(Request $request): RedirectResponse 
    {
        Auth::logout();
        
        $request->session()->invalidate();
        $request->session()->regenerateToken(); // Regenerate CSRF TOKEN
        
        return redirect()->route('login');
    }

    public function register(Request $request) 
    {
        $validator = Validator::make($request->all(), [
            'name'=>'required',
            'email'=>'required|email|unique:users',
            'password'=>['sometimes','regex:/(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[a-zA-Z\d]{11,}/m']  // {4,} not yet test 
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
        return redirect()->intended('/');
    }

    // function ganti password
    
    // function ganti nama

    /**
     * PRIORITAS
     * function ganti profile picture
     * fokus, manipulasi file
     */

    public function homePage(Request $request): View
    {        
        $user = Auth::user();
        $mode = $request->is_done;

        return view('home', [
            'user' => $user,
            'avatar_url' => $user->avatar_url,
            'todos' => $user->todos->where('is_done', '=', $mode),
            'is_done'=>$mode,
        ]);
    }

    public function storeAvatar(Request $request) {
        
        $userId = $request->user()->id;

        if ($id !== Auth::id()) {
            abort('422', 'Unauthorized');
        };

        // $avatar = $request->file('avatar');
        // if ($avatar->getSize() > (int) 10^6) {
        //     return redirect()->back()->withError(
        //         ['error'=>'Image is too big']
        //     );
        // }

        $avatar = read();
        
        $imageSize = $avatar->size();
        $imageRatio = $imageSize->aspectRatio();

        /**
         *  Save uploaded picture
         */
        $avatarUrl = $request->file('avatar')->storeAs('avatars', $userId);  // To specify disk,     add a third argument for storeAs() method
        // $path = Storage::putFileAs('avatars', $request->file('avatar'), $request->user()->id)
        
        $user = User::where('id', $id)->first();
        $user->avatar_url = $avatarUrl;
        
        return redirect()->back()
        ->with('profile_edit', TRUE)
        ->with('avatar_url', $user->avatar_url);
    }




}
