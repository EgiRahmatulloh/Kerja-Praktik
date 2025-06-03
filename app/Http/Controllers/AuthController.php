<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function index()
    {
        if (Auth::check()) {
            if (Auth::user()->role === 'admin') {
                return redirect()->route('admin.dashboard');
            } else {
                return redirect()->route('user.dashboard');
            }
        }
        
        return view('login.index', [
            'title' => 'Login',
        ]);
    }
    
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'login' => 'required',
            'password' => 'required'
        ]);
        
        $login = $credentials['login'];
        $password = $credentials['password'];
        
        // Cek apakah login menggunakan username atau NIM
        $user = User::where('username', $login)
                    ->orWhere('nim', $login)
                    ->first();
        
        if ($user && Auth::attempt(['username' => $user->username, 'password' => $password])) {
            $request->session()->regenerate();
            
            if (Auth::user()->role === 'admin') {
                return redirect()->route('admin.dashboard');
            } else {
                return redirect()->route('user.dashboard');
            }
        }
        
        return back()->with('loginError', 'Login gagal! Username/NIM atau password salah.');
    }
    
    public function logout(Request $request)
    {
        Auth::logout();
        
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect('/');
    }
}
