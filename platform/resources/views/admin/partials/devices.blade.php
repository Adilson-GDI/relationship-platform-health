<div id="devices" class="card shadow mb-4">
    <div class="card-header py-3"><h6 class="m-0 font-weight-bold text-primary">Dispositivos</h6></div>
    <div class="card-body table-responsive">
        <table class="table table-bordered table-sm">
            <thead><tr><th>Dispositivo</th><th>Profissional</th><th>App</th><th>Status</th><th>Acoes</th></tr></thead>
            <tbody>
                @forelse ($devices as $device)
                    <tr>
                        <td><code>{{ $device->device_id }}</code><div class="small text-muted">{{ $device->platform }} {{ $device->app_version }} - {{ $device->last_seen_at?->diffForHumans() ?: 'sem acesso' }}</div></td>
                        <td>{{ $device->professional?->name }}</td>
                        <td>{{ $device->healthApplication?->name }}</td>
                        <td>@include('admin.partials.status', ['active' => ! $device->is_blocked, 'on' => 'Liberado', 'off' => 'Bloqueado'])</td>
                        <td><form method="POST" action="{{ route('admin.devices.toggle', $device) }}">@csrf @method('PATCH')<button class="btn btn-sm btn-outline-primary">{{ $device->is_blocked ? 'Liberar' : 'Bloquear' }}</button></form></td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="text-center text-muted">Nenhum dispositivo encontrado.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
