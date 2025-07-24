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
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

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

        // $signedUrl = Storage::disk('s3')->temporaryUrl(
        //     $user->avatar_url, Carbon::now()->addMinutes(2)
        // );


        return view('home', [
            'user' => $user,
            // 'avatar_url' => $signedUrl,
            'todos' => $user->todos->where('is_done', '=', $mode),
            'is_done'=>$mode,
        ]);
    }

    /**
     *  Get user profile page
     */
    public function profilePage(): View 
    {    
        $user = Auth::user();
        
        $signedUrl = 
        // Storage::disk('s3')->temporaryUrl(
            // $user->avatar_url, Carbon::now()->addMinutes(2)
        // ) 
        Storage::url('avatar-placeholder.jpg');

        return view('profile', [
            'name'=>$user->name,
            'email'=>$user->email,
            'avatar_url'=>$signedUrl
        ]);
    }

    /**
     *  Change user data
     */
    public function changeProfile(Request $request): RedirectResponse
    {
        /**
         *  Logic buat klo gaada user profile data yg diganti gimana?????
         */

        $request->validate([
            'name' => 'nullable|string|max:256',
            'password'=> 'nullable|regex:/(?=.*[a-z])(?=.*[A-Z])(?=.*[\d])[a-zA-Z\d]/m',
            'email' => 'nullable|email',  
            'avatar_picture' => 'sometimes|mimes:jpg,jpeg,png|extensions:jpg,jpeg,png|max:1000|dimensions:ratio=1/1'
        ]);

        $user = Auth::user();

        /**
         *  Save uploaded picture
         */
        if ($request->avatar_picture){
            $avatarUploaded = $request->file('avatar_picture');
            $imageName = $user->id . '.' . $avatarUploaded->extension();
            $avatarUrl = $request->file('avatar_picture')->storeAs('/avatars', $imageName, 's3');  // To specify disk, add a third argument for storeAs() method
        } else {$avatarUrl = null;}
          

        /**
         *  Save the new values to Table
         */
        $user = Auth::user();
        $user->name = $request->name ?: $user->name;
        $user->email = $request->email ?: $user->email;        
        $user->password = $request->password ?: $user->password;
        $user->avatar_url = $avatarUrl ?: $user->avatar_url;
        $user->save();

        // $signedUrl = Storage::disk('s3')->temporaryUrl(
        //     $avatarUrl, Carbon::now()->addMinutes(2)
        // );

        return redirect()->back();
    }
}