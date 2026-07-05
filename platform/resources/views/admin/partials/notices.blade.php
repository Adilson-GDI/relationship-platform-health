<div id="notices" class="card shadow mb-4">
    <div class="card-header py-3"><h6 class="m-0 font-weight-bold text-primary">Avisos internos</h6></div>
    <div class="card-body table-responsive">
        <table class="table table-bordered table-sm">
            <thead><tr><th>Escopo</th><th>Conteudo</th><th>Severidade</th><th>Janela</th><th>Status</th><th>Acoes</th></tr></thead>
            <tbody>
                <tr class="bg-light">
                    <form method="POST" action="{{ route('admin.notices.store') }}">@csrf
                        <td>@include('admin.partials.app-select', ['name' => 'health_application_id', 'value' => null, 'applications' => $applications, 'required' => false])</td>
                        <td><input name="title" required class="form-control form-control-sm mb-2" placeholder="Titulo"><textarea name="body" required rows="2" class="form-control form-control-sm" placeholder="Mensagem"></textarea></td>
                        <td>@include('admin.partials.severity-select', ['value' => 'info'])</td>
                        <td><input type="datetime-local" name="starts_at" class="form-control form-control-sm mb-2"><input type="datetime-local" name="ends_at" class="form-control form-control-sm"></td>
                        <td><label><input type="checkbox" name="is_active" value="1" checked> Ativo</label></td>
                        <td><button class="btn btn-success btn-sm"><i class="fas fa-plus"></i></button></td>
                    </form>
                </tr>
                @foreach ($notices as $notice)
                    <tr>
                        <form method="POST" action="{{ route('admin.notices.update', $notice) }}">@csrf @method('PUT')
                            <td>@include('admin.partials.app-select', ['name' => 'health_application_id', 'value' => $notice->health_application_id, 'applications' => $applications, 'required' => false])</td>
                            <td><input name="title" value="{{ $notice->title }}" required class="form-control form-control-sm mb-2"><textarea name="body" required rows="2" class="form-control form-control-sm">{{ $notice->body }}</textarea></td>
                            <td>@include('admin.partials.severity-select', ['value' => $notice->severity])</td>
                            <td><input type="datetime-local" name="starts_at" value="{{ $notice->starts_at?->format('Y-m-d\\TH:i') }}" class="form-control form-control-sm mb-2"><input type="datetime-local" name="ends_at" value="{{ $notice->ends_at?->format('Y-m-d\\TH:i') }}" class="form-control form-control-sm"></td>
                            <td><label><input type="checkbox" name="is_active" value="1" @checked($notice->is_active)> Ativo</label></td>
                            <td class="text-nowrap"><button class="btn btn-primary btn-sm"><i class="fas fa-save"></i></button>
                        </form>
                                <form method="POST" action="{{ route('admin.notices.destroy', $notice) }}" class="d-inline" onsubmit="return confirm('Excluir aviso?')">@csrf @method('DELETE')<button class="btn btn-danger btn-sm"><i class="fas fa-trash"></i></button></form>
                            </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
