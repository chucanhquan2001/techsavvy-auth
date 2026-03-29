<?php

namespace App\Http\Controllers;

use App\Application\Services\AuthService;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function showLogin(): View
    {
        return view('login');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (! Auth::guard('web')->attempt($credentials, $request->boolean('remember'))) {
            return back()->withErrors(['email' => __('auth.failed')])->onlyInput('email');
        }

        $request->session()->regenerate();

        return redirect()->intended('/');
    }

    public function showRegister(): View
    {
        return view('register');
    }

    public function register(Request $request, AuthService $authService): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email'],
            'password' => ['required', 'min:6'],
        ]);

        try {
            $authService->register(
                $validated['name'],
                $validated['email'],
                $validated['password']
            );
        } catch (Exception $e) {
            return back()->withErrors(['email' => $e->getMessage()])->withInput();
        }

        Auth::guard('web')->attempt([
            'email' => $validated['email'],
            'password' => $validated['password'],
        ]);

        $request->session()->regenerate();

        return redirect()->intended('/');
    }
}
