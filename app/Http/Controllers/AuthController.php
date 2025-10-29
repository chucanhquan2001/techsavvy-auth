<?php

namespace App\Http\Controllers;

use App\Application\Services\AuthService;
use Illuminate\Http\Request;
use Exception;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('login');
    }

    public function login(Request $request, AuthService $authService)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        try {
            $data = $authService->login($request->email, $request->password);
            return response()->json($data);
        } catch (Exception $e) {
            return back()->withErrors(['message' => $e->getMessage()]);
        }
    }
}