<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\View\View;

class AdminAuthController extends Controller
{
    /**
     * Show the cinematic admin login screen.
     */
    public function showLoginForm(): View|RedirectResponse
    {
        if (Auth::check() && Auth::user()->isAdmin()) {
            return redirect()->route('admin.settings');
        }

        return view('admin.auth.login');
    }

    /**
     * Handle an encrypted secure admin login attempt.
     */
    public function login(Request $request): RedirectResponse
    {
        $request->validate([
            'login'    => 'required|string',
            'password' => 'required|string',
        ]);

        $login = trim($request->input('login'));
        $password = $request->input('password');
        $remember = $request->boolean('remember');

        // Rate limiting key: 5 attempts per 60 seconds per IP + username
        $throttleKey = Str::transliterate(Str::lower($login) . '|' . $request->ip());

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            return back()->withErrors([
                'login' => "Security rate limit exceeded. Please try again in {$seconds} seconds.",
            ])->withInput($request->only('login', 'remember'));
        }

        // Support authentication via either username or email
        $isEmail = filter_var($login, FILTER_VALIDATE_EMAIL);
        $credentials = [
            $isEmail ? 'email' : 'username' => $login,
            'password' => $password,
        ];

        if (Auth::attempt($credentials, $remember)) {
            $user = Auth::user();

            if (!$user->isAdmin()) {
                Auth::logout();
                RateLimiter::hit($throttleKey, 60);
                return back()->withErrors([
                    'login' => 'Access denied. Administrator privileges required.',
                ])->withInput($request->only('login'));
            }

            RateLimiter::clear($throttleKey);
            $request->session()->regenerate();

            return redirect()->intended(route('admin.settings'))
                ->with('success', "Welcome back, {$user->name}!");
        }

        RateLimiter::hit($throttleKey, 60);

        return back()->withErrors([
            'login' => 'Invalid username or password. Please verify your credentials.',
        ])->withInput($request->only('login', 'remember'));
    }

    /**
     * Log out the administrator and invalidate session.
     */
    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login')
            ->with('success', 'You have been securely logged out.');
    }

    /**
     * Update administrator username and encrypted password.
     */
    public function updateCredentials(Request $request): JsonResponse|RedirectResponse
    {
        /** @var User $user */
        $user = Auth::user();

        $request->validate([
            'name'             => 'nullable|string|max:255',
            'username'         => 'required|string|max:255|alpha_dash|unique:users,username,' . $user->id,
            'email'            => 'required|email|max:255|unique:users,email,' . $user->id,
            'current_password' => 'required|string',
            'new_password'     => 'nullable|string|min:8|confirmed',
        ]);

        if (!Hash::check($request->input('current_password'), $user->password)) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'The current password provided is incorrect.',
                ], 422);
            }

            return back()->withErrors(['current_password' => 'The current password provided is incorrect.']);
        }

        if ($request->filled('name')) {
            $user->name = trim($request->input('name'));
        }
        $user->username = trim($request->input('username'));
        $user->email = trim($request->input('email'));

        if ($request->filled('new_password')) {
            $user->password = Hash::make($request->input('new_password'));
        }

        $user->save();

        if ($request->expectsJson()) {
            return response()->json([
                'success'  => true,
                'message'  => 'Admin credentials updated and securely encrypted.',
                'username' => $user->username,
                'email'    => $user->email,
            ]);
        }

        return back()->with('success', 'Admin credentials updated and securely encrypted.');
    }
}
