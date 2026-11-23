<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

class LoginController extends Controller
{
    

    use AuthenticatesUsers;

    
    

    
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
        $this->middleware('auth')->only('logout');
    }
    
    protected function authenticated(\Illuminate\Http\Request $request, $user)
    {
        if ($user->role === 'admin') {
            return redirect('/admin/dashboard');
        } else if ($user->role === 'kasir') {
            return redirect('/kasir/dashboard');
        }

        return redirect('/login');
    }
}
