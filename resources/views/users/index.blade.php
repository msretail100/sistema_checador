@extends('layouts.app')

@section('title', 'Usuarios')

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
    <div>
        <p class="text-primary fw-semibold mb-1">{{ auth()->user()->isRoot() ? 'ADMINISTRACIÓN GLOBAL' : 'ADMINISTRACIÓN' }}</p>
        <h1 class="h2 fw-bold mb-0">{{ auth()->user()->isRoot() ? 'Usuarios' : 'Trabajadores' }}</h1>
    </div>
    <a class="btn btn-primary" href="{{ route('users.create') }}">Agregar usuario</a>
</div>
<section class="card p-3 p-md-4">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead><tr><th>Nombre</th><th>Usuario</th><th>Rol</th><th>Estado</th><th class="text-end">Acciones</th></tr></thead>
            <tbody>
            @forelse ($users as $user)
                <tr>
                    <td class="fw-semibold">{{ $user->name }}</td>
                    <td>{{ '@'.$user->username }}</td>
                    <td>{{ \App\Enums\UserRole::tryFrom($user->role)?->label() ?? $user->role }}</td>
                    <td><span class="badge text-bg-{{ $user->is_active ? 'success' : 'secondary' }}">{{ $user->is_active ? 'Activo' : 'Deshabilitado' }}</span></td>
                    <td class="text-end">
                        @if (auth()->user()->isRoot())
                            <a class="btn btn-sm btn-outline-primary" href="{{ route('users.edit', $user) }}">Editar</a>
                        @endif
                        <form class="d-inline" method="POST" action="{{ route('users.toggle', $user) }}">
                            @csrf
                            @method('PATCH')
                            <button class="btn btn-sm btn-outline-{{ $user->is_active ? 'danger' : 'success' }}" type="submit">{{ $user->is_active ? 'Deshabilitar' : 'Habilitar' }}</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td class="text-center muted py-4" colspan="5">No hay usuarios registrados.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-3">{{ $users->links() }}</div>
</section>
@endsection
