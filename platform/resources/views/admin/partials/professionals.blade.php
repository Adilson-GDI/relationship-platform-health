<div id="professionals" class="card shadow mb-4">
    <div class="card-header py-3"><h6 class="m-0 font-weight-bold text-primary">Profissionais</h6></div>
    <div class="card-body table-responsive">
        <table class="table table-bordered table-sm">
            <thead><tr><th>Nome</th><th>App</th><th>Contato</th><th>Status</th><th>Acoes</th></tr></thead>
            <tbody>
                @forelse ($professionals as $professional)
                    <tr>
                        <td><strong>{{ $professional->name }}</strong><div class="small text-muted">{{ $professional->profession ?: 'Sem profissao' }} - {{ $professional->city ?: 'Cidade nao informada' }}</div></td>
                        <td>{{ $professional->healthApplication?->name }}</td>
                        <td class="small">{{ $professional->email ?: '-' }}<br>{{ $professional->phone ?: '-' }}</td>
                        <td>@include('admin.partials.status', ['active' => ! $professional->is_blocked, 'on' => 'Liberado', 'off' => 'Bloqueado'])</td>
                        <td><form method="POST" action="{{ route('admin.professionals.toggle', $professional) }}">@csrf @method('PATCH')<button class="btn btn-sm btn-outline-primary">{{ $professional->is_blocked ? 'Liberar' : 'Bloquear' }}</button></form></td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="text-center text-muted">Nenhum profissional encontrado.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
