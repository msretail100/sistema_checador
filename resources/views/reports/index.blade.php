@extends('layouts.app')

@section('title', 'Reporte de asistencia')

@section('content')
@php
    $dashboardRoute = auth()->user()->isRoot() ? 'root.dashboard' : 'admin.dashboard';
    $reportRoute = auth()->user()->isRoot() ? 'root.reports' : 'admin.reports';
    $labels = ['daily' => 'Diario', 'monthly' => 'Mensual', 'yearly' => 'Anual'];
@endphp
<div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
    <div>
        <p class="text-primary fw-semibold mb-1">REPORTES</p>
        <h1 class="h2 fw-bold mb-0">Reporte {{ $labels[$period] }}</h1>
    </div>
    <a class="btn btn-outline-secondary" href="{{ route($dashboardRoute) }}">Volver al panel</a>
</div>
<section class="card p-3 p-md-4">
    <form class="row g-3 align-items-end mb-4" method="GET" action="{{ route($reportRoute, $period) }}">
        <div class="col-12 col-md-5">
            <label class="form-label" for="date">Fecha de referencia</label>
            <input class="form-control" type="date" id="date" name="date" value="{{ $date }}" required>
            <div class="form-text">El reporte {{ strtolower($labels[$period]) }} usa el día, mes o año de esta fecha.</div>
        </div>
        <div class="col-auto"><button class="btn btn-primary" type="submit">Consultar</button></div>
    </form>
    <p class="muted">Registros encontrados: <strong>{{ $total }}</strong></p>
    @include('partials.records-table', ['records' => $records])
    <div class="mt-3">{{ $records->links() }}</div>
</section>
@endsection
