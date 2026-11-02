@extends('layouts.app')

@section('title', 'Iniciar sesión')

@section('content')
<div class="row justify-content-center align-items-center" style="min-height: 70vh">
    <div class="col-12 col-sm-9 col-md-6 col-lg-5">
        <section class="card p-4 p-md-5">
            <div class="mb-4">
                <p class="text-primary fw-semibold mb-2">CONTROL DE ASISTENCIA</p>
                <h1 class="h3 fw-bold">Bienvenido</h1>
                <p class="muted mb-0">Inicia sesión con tu usuario y contraseña.</p>
            </div>
            <form method="POST" action="{{ route('login.store') }}">
                @csrf
                <div class="mb-3">
                    <label class="form-label" for="username">Usuario</label>
                    <input class="form-control form-control-lg" id="username" name="username" value="{{ old('username') }}" autocomplete="username" required autofocus>
                </div>
                <div class="mb-3">
                    <label class="form-label" for="password">Contraseña</label>
                    <input class="form-control form-control-lg" id="password" name="password" type="password" autocomplete="current-password" required>
                </div>
                <div class="form-check mb-4">
                    <input class="form-check-input" id="remember" name="remember" type="checkbox" value="1">
                    <label class="form-check-label" for="remember">Mantener sesión iniciada</label>
                </div>
                <button class="btn btn-primary btn-lg w-100" type="submit">Entrar</button>
            </form>
        </section>
    </div>
</div>
@endsection
