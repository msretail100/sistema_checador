<?php

namespace App\Http\Controllers;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class UserManagementController extends Controller
{
    public function index(): View
    {
        $query = User::query()->orderBy('name');

        if (auth()->user()->isAdmin()) {
            $query->where('role', UserRole::Worker->value);
        }

        return view('users.index', ['users' => $query->paginate(15)]);
    }

    public function create(): View
    {
        return view('users.form', [
            'user' => new User,
            'roles' => auth()->user()->isRoot() ? UserRole::cases() : [UserRole::Worker],
            'editing' => false,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $isRoot = $request->user()->isRoot();
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'username' => ['required', 'string', 'alpha_dash', 'max:80', 'unique:users,username'],
            'role' => [$isRoot ? 'required' : 'prohibited', Rule::in(UserRole::values())],
            'password' => ['required', 'string', 'min:12', 'confirmed'],
        ]);

        User::query()->create([
            'name' => $validated['name'],
            'username' => $validated['username'],
            'role' => $isRoot ? $validated['role'] : UserRole::Worker->value,
            'is_active' => true,
            'password' => $validated['password'],
        ]);

        return redirect()->route('users.index')->with('status', 'Usuario creado correctamente.');
    }

    public function edit(User $user): View
    {
        abort_unless(auth()->user()->isRoot(), 403);

        return view('users.form', [
            'user' => $user,
            'roles' => UserRole::cases(),
            'editing' => true,
        ]);
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        abort_unless($request->user()->isRoot(), 403);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'username' => ['required', 'string', 'alpha_dash', 'max:80', Rule::unique('users', 'username')->ignore($user->id)],
            'role' => ['required', Rule::in(UserRole::values())],
            'password' => ['nullable', 'string', 'min:12', 'confirmed'],
        ]);

        if ($user->is_active && $user->isRoot() && $validated['role'] !== UserRole::Root->value) {
            $activeRoots = User::query()
                ->where('role', UserRole::Root->value)
                ->where('is_active', true)
                ->count();

            if ($activeRoots <= 1) {
                return back()->withErrors(['user' => 'Debe existir al menos un Root activo.']);
            }
        }

        $user->fill([
            'name' => $validated['name'],
            'username' => $validated['username'],
            'role' => $validated['role'],
        ]);

        if ($request->filled('password')) {
            $user->password = $validated['password'];
        }

        $user->save();

        return redirect()->route('users.index')->with('status', 'Usuario actualizado correctamente.');
    }

    public function toggle(User $user): RedirectResponse
    {
        $actor = auth()->user();
        abort_unless($actor->isRoot() || ($actor->isAdmin() && $user->isWorker()), 403);

        if ($user->is_active && $user->id === $actor->id) {
            return back()->withErrors(['user' => 'No puedes deshabilitar tu propia cuenta.']);
        }

        if ($user->is_active && $user->isRoot()) {
            $activeRoots = User::query()
                ->where('role', UserRole::Root->value)
                ->where('is_active', true)
                ->count();

            if ($activeRoots <= 1) {
                return back()->withErrors(['user' => 'Debe existir al menos un Root activo.']);
            }
        }

        $user->update(['is_active' => ! $user->is_active]);

        return back()->with('status', $user->is_active ? 'Cuenta habilitada.' : 'Cuenta deshabilitada.');
    }
}
