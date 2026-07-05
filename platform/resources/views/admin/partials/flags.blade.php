<div id="flags" class="card shadow mb-4">
    <div class="card-header py-3"><h6 class="m-0 font-weight-bold text-primary">Feature flags</h6></div>
    <div class="card-body table-responsive">
        <table class="table table-bordered table-sm">
            <thead><tr><th>Escopo</th><th>Chave</th><th>Nome</th><th>Rules JSON</th><th>Status</th><th>Acoes</th></tr></thead>
            <tbody>
                <tr class="bg-light">
                    <form method="POST" action="{{ route('admin.flags.store') }}">@csrf
                        <td>@include('admin.partials.app-select', ['name' => 'health_application_id', 'value' => null, 'applications' => $applications, 'required' => false])</td>
                        <td><input name="key" required class="form-control form-control-sm" placeholder="offline_first_sync"></td>
                        <td><input name="name" required class="form-control form-control-sm" placeholder="Nome publico"></td>
                        <td><textarea name="rules" rows="2" class="form-control form-control-sm"></textarea></td>
                        <td><label><input type="checkbox" name="enabled" value="1" checked> Ativa</label></td>
                        <td><button class="btn btn-success btn-sm"><i class="fas fa-plus"></i></button></td>
                    </form>
                </tr>
                @foreach ($flags as $flag)
                    <tr>
                        <form method="POST" action="{{ route('admin.flags.update', $flag) }}">@csrf @method('PUT')
                            <td>@include('admin.partials.app-select', ['name' => 'health_application_id', 'value' => $flag->health_application_id, 'applications' => $applications, 'required' => false])</td>
                            <td><input name="key" value="{{ $flag->key }}" required class="form-control form-control-sm"></td>
                            <td><input name="name" value="{{ $flag->name }}" required class="form-control form-control-sm"></td>
                            <td><textarea name="rules" rows="2" class="form-control form-control-sm">{{ $flag->rules ? json_encode($flag->rules, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) : '' }}</textarea></td>
                            <td><label><input type="checkbox" name="enabled" value="1" @checked($flag->enabled)> Ativa</label></td>
                            <td class="text-nowrap"><button class="btn btn-primary btn-sm"><i class="fas fa-save"></i></button>
                        </form>
                                <form method="POST" action="{{ route('admin.flags.destroy', $flag) }}" class="d-inline" onsubmit="return confirm('Excluir flag?')">@csrf @method('DELETE')<button class="btn btn-danger btn-sm"><i class="fas fa-trash"></i></button></form>
                            </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
