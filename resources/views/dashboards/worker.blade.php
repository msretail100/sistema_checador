@extends('layouts.app')

@section('title', 'Mi asistencia')

@section('content')
<div class="row justify-content-center">
    <div class="col-12 col-lg-8">
        <section class="card p-4 p-md-5 text-center mb-4">
            <p class="text-primary fw-semibold mb-2">MI ASISTENCIA</p>
            <h1 class="h2 fw-bold">Hola, {{ auth()->user()->name }}</h1>
            <p class="muted mb-4">La fecha y hora se registran desde el servidor.</p>

            @if ($nextType)
                <form id="attendance-form" method="POST" action="{{ route('attendance.store') }}">
                    @csrf
                    <input type="hidden" name="latitud" id="latitud">
                    <input type="hidden" name="longitud" id="longitud">
                    <button class="btn btn-{{ $nextType === 'entrada' ? 'success' : 'primary' }} check-button px-5" id="attendance-button" type="submit">
                        Registrar {{ $nextType }}
                    </button>
                    <p class="small muted mt-3 mb-0">Se solicitará tu ubicación para completar la checada.</p>
                </form>
            @else
                <div class="alert alert-success mb-0">Tu entrada y salida de hoy ya están registradas.</div>
            @endif
        </section>
        <section class="card p-3 p-md-4">
            <h2 class="h5 fw-bold mb-3">Mis checadas de hoy</h2>
            @forelse ($records as $record)
                <div class="d-flex justify-content-between align-items-center border-bottom py-3">
                    <span class="badge text-bg-{{ $record->tipo === 'entrada' ? 'success' : 'secondary' }}">{{ ucfirst($record->tipo) }}</span>
                    <span class="fw-semibold">{{ $record->hora_servidor->format('H:i:s') }}</span>
                    <span class="small muted">{{ $record->hora_servidor->format('d/m/Y') }}</span>
                </div>
            @empty
                <p class="muted mb-0">Aún no tienes registros el día de hoy.</p>
            @endforelse
        </section>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const form = document.getElementById('attendance-form');
    if (form) {
        form.addEventListener('submit', (event) => {
            event.preventDefault();
            const button = document.getElementById('attendance-button');
            button.disabled = true;
            button.textContent = 'Obteniendo ubicación…';

            if (!navigator.geolocation) {
                alert('Este navegador no permite obtener la ubicación. Usa un navegador compatible y una conexión segura (HTTPS).');
                button.disabled = false;
                button.textContent = 'Registrar {{ $nextType }}';
                return;
            }

            navigator.geolocation.getCurrentPosition((position) => {
                document.getElementById('latitud').value = position.coords.latitude;
                document.getElementById('longitud').value = position.coords.longitude;
                form.submit();
            }, () => {
                alert('No se pudo obtener tu ubicación. Permite el acceso a GPS e intenta nuevamente.');
                button.disabled = false;
                button.textContent = 'Registrar {{ $nextType }}';
            }, { enableHighAccuracy: true, timeout: 15000, maximumAge: 0 });
        });
    }
</script>
@endpush
