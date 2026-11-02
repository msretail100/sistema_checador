@extends('layouts.app')

@section('title', 'Panel Administrador')

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-4">
    <div>
        <p class="text-primary fw-semibold mb-1">ADMINISTRADOR</p>
        <h1 class="h2 fw-bold mb-1">Panel de asistencia</h1>
        <p class="muted mb-0">Gestión de trabajadores y checadas recientes.</p>
    </div>
    <a class="btn btn-primary" href="{{ route('users.index') }}">Gestionar trabajadores</a>
</div>
<div class="row g-3 mb-4">
    <div class="col-12 col-md-6"><div class="card p-4"><div class="muted">Trabajadores activos</div><div class="metric">{{ $activeWorkersCount }}</div></div></div>
    <div class="col-12 col-md-6"><div class="card p-4"><div class="muted">Checadas de hoy</div><div class="metric">{{ $todayRecordsCount }}</div></div></div>
</div>
<section class="card p-3 p-md-4">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
        <h2 class="h5 fw-bold mb-0">Monitoreo reciente</h2>
        <div class="d-flex gap-2">
            <a class="btn btn-outline-primary btn-sm" href="{{ route('admin.reports', 'daily') }}">Diario</a>
            <a class="btn btn-outline-primary btn-sm" href="{{ route('admin.reports', 'monthly') }}">Mensual</a>
        </div>
    </div>
    @include('partials.records-table', ['records' => $records])
</section>
@endsection

@push('head')
    <meta http-equiv="refresh" content="15">
@endpush
