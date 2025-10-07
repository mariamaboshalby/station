<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * عرض فورم تسجيل الدخول
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * تنفيذ تسجيل الدخول
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();
        $request->session()->regenerate();

        // ✅ التوجيه حسب الرول
        if ($request->user()->hasRole('admin')) {
            return redirect()->intended('/dashboard');
        }

        if ($request->user()->hasRole('user')) {
            return redirect()->intended('/shifts/create');
        }

        // لو ملوش رول خالص
        return redirect()->intended('/');
    }

    /**
     * تسجيل الخروج
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
