<div class="table-responsive">
    <table class="table table-hover mb-0">
        <thead><tr><th>Trabajador</th><th>Tipo</th><th>Hora del servidor</th><th>IP</th><th>Coordenadas</th></tr></thead>
        <tbody>
        @forelse ($records as $record)
            <tr>
                <td><div class="fw-semibold">{{ $record->user->name }}</div><small class="muted">{{ '@'.$record->user->username }}</small></td>
                <td><span class="badge text-bg-{{ $record->tipo === 'entrada' ? 'success' : 'secondary' }}">{{ ucfirst($record->tipo) }}</span></td>
                <td>{{ $record->hora_servidor->format('d/m/Y H:i:s') }}</td>
                <td><code>{{ $record->ip_address }}</code></td>
                <td><a href="https://www.openstreetmap.org/?mlat={{ $record->latitud }}&mlon={{ $record->longitud }}#map=16/{{ $record->latitud }}/{{ $record->longitud }}" target="_blank" rel="noopener noreferrer">{{ $record->latitud }}, {{ $record->longitud }}</a></td>
            </tr>
        @empty
            <tr><td class="text-center muted py-4" colspan="5">Todavía no hay checadas registradas.</td></tr>
        @endforelse
        </tbody>
    </table>
</div>
