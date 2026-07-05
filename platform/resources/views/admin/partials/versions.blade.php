<div id="versions" class="card shadow mb-4">
    <div class="card-header py-3"><h6 class="m-0 font-weight-bold text-primary">Versoes dos aplicativos</h6></div>
    <div class="card-body table-responsive">
        <table class="table table-bordered table-sm">
            <thead><tr><th>App</th><th>Plataforma</th><th>Versao</th><th>Notas</th><th>Regras</th><th>Acoes</th></tr></thead>
            <tbody>
                <tr class="bg-light">
                    <form method="POST" action="{{ route('admin.versions.store') }}">@csrf
                        <td>@include('admin.partials.app-select', ['name' => 'health_application_id', 'value' => null, 'applications' => $applications, 'required' => true])</td>
                        <td><input name="platform" required class="form-control form-control-sm" placeholder="android"></td>
                        <td><input name="version" required class="form-control form-control-sm" placeholder="1.0.1"></td>
                        <td><textarea name="release_notes" rows="2" class="form-control form-control-sm"></textarea></td>
                        <td class="small"><label><input type="checkbox" name="is_active" value="1" checked> ativa</label><br><label><input type="checkbox" name="is_required" value="1"> obrigatoria</label></td>
                        <td><button class="btn btn-success btn-sm"><i class="fas fa-plus"></i></button></td>
                    </form>
                </tr>
                @foreach ($versions as $version)
                    <tr>
                        <form method="POST" action="{{ route('admin.versions.update', $version) }}">@csrf @method('PUT')
                            <td>@include('admin.partials.app-select', ['name' => 'health_application_id', 'value' => $version->health_application_id, 'applications' => $applications, 'required' => true])</td>
                            <td><input name="platform" value="{{ $version->platform }}" required class="form-control form-control-sm"></td>
                            <td><input name="version" value="{{ $version->version }}" required class="form-control form-control-sm"></td>
                            <td><textarea name="release_notes" rows="2" class="form-control form-control-sm">{{ $version->release_notes }}</textarea></td>
                            <td class="small"><label><input type="checkbox" name="is_active" value="1" @checked($version->is_active)> ativa</label><br><label><input type="checkbox" name="is_required" value="1" @checked($version->is_required)> obrigatoria</label></td>
                            <td class="text-nowrap"><button class="btn btn-primary btn-sm"><i class="fas fa-save"></i></button>
                        </form>
                                <form method="POST" action="{{ route('admin.versions.destroy', $version) }}" class="d-inline" onsubmit="return confirm('Excluir versao?')">@csrf @method('DELETE')<button class="btn btn-danger btn-sm"><i class="fas fa-trash"></i></button></form>
                            </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
