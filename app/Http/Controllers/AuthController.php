<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function proseslogin(Request $request)
    {
        if (Auth::guard('pengajar')->attempt(['nik' => $request->nik, 'password' => $request->password])) {
            return redirect('/dashboard');
        } else {
            return redirect('/')->with(['warning' => 'NIK / Password Salah']);
        }
    }

    public function proseslogout()
    {
        if (auth::guard('pengajar')->check()) {
            auth::guard('pengajar')->logout();
            return redirect('/');
        }
    }
    
        public function proseslogoutadmin()
    {
        if (auth::guard('user')->check()) {
            auth::guard('user')->logout();
            return redirect('/panel');
        }
    }
    
    public function prosesloginadmin(Request $request)
    {
        if (Auth::guard('user')->attempt(['username' => $request->username, 'password' => $request->password])) {
            return redirect('/panel/dashboardadmin');
        } else {
            return redirect('/panel')->with(['warning' => 'Username / Password Salah']);
        }
    }
}