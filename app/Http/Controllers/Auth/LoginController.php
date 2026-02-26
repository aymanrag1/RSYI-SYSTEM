<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\WpAuthService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class LoginController extends Controller
{
    public function showLogin()
    {
        if (Session::has('user_id')) {
            return redirect()->route('dashboard');
        }
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'login'    => ['required', 'string'],
            'password' => ['required', 'string'],
        ], [
            'login.required'    => 'يرجى إدخال اسم المستخدم أو البريد الإلكتروني.',
            'password.required' => 'يرجى إدخال كلمة المرور.',
        ]);

        $user = WpAuthService::attempt(
            $request->input('login'),
            $request->input('password')
        );

        if (! $user) {
            return back()->withErrors([
                'login' => 'بيانات الدخول غير صحيحة.',
            ])->withInput(['login' => $request->login]);
        }

        // حفظ بيانات المستخدم في الـ session
        Session::put('user_id',    $user->ID);
        Session::put('user_login', $user->user_login);
        Session::put('user_name',  $user->display_name);
        Session::put('user_email', $user->user_email);
        Session::put('user_roles', $user->wpRoles());

        $request->session()->regenerate();

        return redirect()->intended(route('dashboard'));
    }

    public function logout(Request $request)
    {
        Session::flush();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
