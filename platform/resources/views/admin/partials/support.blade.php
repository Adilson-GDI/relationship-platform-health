<div id="support" class="card shadow mb-4">
    <div class="card-header py-3"><h6 class="m-0 font-weight-bold text-primary">Mensagens de suporte</h6></div>
    <div class="card-body table-responsive">
        <table class="table table-bordered table-sm">
            <thead>
                <tr>
                    <th>Profissional</th>
                    <th>Contato</th>
                    <th>Mensagem</th>
                    <th>App</th>
                    <th>Origem</th>
                    <th>Status</th>
                    <th>Recebida</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($supportMessages as $message)
                    <tr>
                        <td>
                            <strong>{{ $message->professional?->name ?? $message->name ?? 'Nao identificado' }}</strong>
                            <div class="small text-muted">Local: {{ $message->local_user_id ?: '-' }}</div>
                        </td>
                        <td class="small">{{ $message->email ?: '-' }}<br>{{ $message->phone ?: '-' }}</td>
                        <td style="min-width: 260px;">{{ $message->message }}</td>
                        <td>{{ $message->healthApplication?->name ?? '-' }}</td>
                        <td class="small">{{ $message->platform ?: '-' }} {{ $message->app_version ?: '' }}</td>
                        <td><span class="badge badge-info">{{ $message->status }}</span></td>
                        <td class="small">{{ $message->created_at?->format('d/m/Y H:i') }}</td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="text-center text-muted">Nenhuma mensagem de suporte recebida.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
