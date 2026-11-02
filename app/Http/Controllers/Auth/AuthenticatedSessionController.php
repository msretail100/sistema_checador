<?php

namespace App\Http\Controllers\Auth;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    public function create(): View|RedirectResponse
    {
        if (Auth::check()) {
            return $this->redirectForRole(Auth::user()->role);
        }

        return view('auth.login');
    }

    public function store(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'username' => ['required', 'string', 'max:80'],
            'password' => ['required', 'string'],
        ]);

        if (! Auth::attempt([
            ...$credentials,
            'is_active' => true,
        ], $request->boolean('remember'))) {
            return back()
                ->withErrors(['username' => 'Usuario o contraseña incorrectos.'])
                ->onlyInput('username');
        }

        $request->session()->regenerate();

        return $this->redirectForRole($request->user()->role);
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    private function redirectForRole(string $role): RedirectResponse
    {
        return match ($role) {
            UserRole::Root->value => redirect()->route('root.dashboard'),
            UserRole::Admin->value => redirect()->route('admin.dashboard'),
            UserRole::Worker->value => redirect()->route('worker.dashboard'),
            default => abort(403),
        };
    }
}
