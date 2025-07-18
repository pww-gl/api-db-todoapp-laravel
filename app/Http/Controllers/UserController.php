<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

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

    
}
