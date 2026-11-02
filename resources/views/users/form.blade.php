@extends('layouts.app')

@section('title', $editing ? 'Editar usuario' : 'Crear usuario')

@section('content')
<div class="row justify-content-center">
    <div class="col-12 col-lg-8">
        <section class="card p-4 p-md-5">
            <h1 class="h3 fw-bold mb-4">{{ $editing ? 'Editar usuario' : 'Crear usuario' }}</h1>
            <form method="POST" action="{{ $editing ? route('users.update', $user) : route('users.store') }}">
                @csrf
                @if ($editing) @method('PUT') @endif
                <div class="mb-3">
                    <label class="form-label" for="name">Nombre completo</label>
                    <input class="form-control" id="name" name="name" value="{{ old('name', $user->name) }}" maxlength="120" required>
                </div>
                <div class="mb-3">
                    <label class="form-label" for="username">Usuario</label>
                    <input class="form-control" id="username" name="username" value="{{ old('username', $user->username) }}" maxlength="80" autocomplete="username" required>
                </div>
                @if (auth()->user()->isRoot())
                    <div class="mb-3">
                        <label class="form-label" for="role">Rol</label>
                        <select class="form-select" id="role" name="role" required>
                            @foreach ($roles as $role)
                                <option value="{{ $role->value }}" @selected(old('role', $user->role) === $role->value)>{{ $role->label() }}</option>
                            @endforeach
                        </select>
                    </div>
                @endif
                <div class="mb-3">
                    <label class="form-label" for="password">Contraseña {{ $editing ? '(dejar en blanco para conservarla)' : '' }}</label>
                    <input class="form-control" id="password" name="password" type="password" minlength="12" autocomplete="new-password" {{ $editing ? '' : 'required' }}>
                    <div class="form-text">Mínimo 12 caracteres.</div>
                </div>
                <div class="mb-4">
                    <label class="form-label" for="password_confirmation">Confirmar contraseña</label>
                    <input class="form-control" id="password_confirmation" name="password_confirmation" type="password" minlength="12" autocomplete="new-password" {{ $editing ? '' : 'required' }}>
                </div>
                <div class="d-flex gap-2">
                    <button class="btn btn-primary" type="submit">{{ $editing ? 'Guardar cambios' : 'Crear cuenta' }}</button>
                    <a class="btn btn-outline-secondary" href="{{ route('users.index') }}">Cancelar</a>
                </div>
            </form>
        </section>
    </div>
</div>
@endsection
