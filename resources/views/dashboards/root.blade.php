@extends('layouts.app')

@section('title', 'Panel Root')

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-4">
    <div>
        <p class="text-primary fw-semibold mb-1">SUPERADMINISTRADOR</p>
        <h1 class="h2 fw-bold mb-1">Panel Root</h1>
        <p class="muted mb-0">Administración global y monitoreo de asistencia.</p>
    </div>
    <a class="btn btn-primary" href="{{ route('users.index') }}">Gestionar usuarios</a>
</div>
<div class="row g-3 mb-4">
    <div class="col-12 col-md-4"><div class="card p-4"><div class="muted">Usuarios totales</div><div class="metric">{{ $usersCount }}</div></div></div>
    <div class="col-12 col-md-4"><div class="card p-4"><div class="muted">Trabajadores activos</div><div class="metric">{{ $activeWorkersCount }}</div></div></div>
    <div class="col-12 col-md-4"><div class="card p-4"><div class="muted">Checadas de hoy</div><div class="metric">{{ $todayRecordsCount }}</div></div></div>
</div>
<section class="card p-3 p-md-4 mb-4">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
        <h2 class="h5 fw-bold mb-0">Reportes</h2>
        <div class="d-flex gap-2 flex-wrap">
            <a class="btn btn-outline-primary btn-sm" href="{{ route('root.reports', 'daily') }}">Diario</a>
            <a class="btn btn-outline-primary btn-sm" href="{{ route('root.reports', 'monthly') }}">Mensual</a>
            <a class="btn btn-outline-primary btn-sm" href="{{ route('root.reports', 'yearly') }}">Anual</a>
        </div>
    </div>
    @include('partials.records-table', ['records' => $records])
</section>
@endsection

@push('head')
    <meta http-equiv="refresh" content="15">
@endpush
