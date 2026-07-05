<div id="applications" class="card shadow mb-4">
    <div class="card-header py-3"><h6 class="m-0 font-weight-bold text-primary">Aplicativos</h6></div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-sm align-middle">
                <thead><tr><th>Identidade</th><th>Cores</th><th>Settings JSON</th><th>Status</th><th>Uso</th><th>Acoes</th></tr></thead>
                <tbody>
                    <tr class="bg-light">
                        <form method="POST" action="{{ route('admin.applications.store') }}">@csrf
                            <td><input name="name" required class="form-control form-control-sm mb-2" placeholder="Nome"><input name="code" required class="form-control form-control-sm" placeholder="codigo"></td>
                            <td><input name="primary_color" class="form-control form-control-sm mb-2" placeholder="#16a34a"><input name="secondary_color" class="form-control form-control-sm" placeholder="#0f172a"></td>
                            <td><textarea name="settings" rows="2" class="form-control form-control-sm" placeholder='{"plan":"basic"}'></textarea></td>
                            <td><div class="custom-control custom-switch"><input id="app-new-active" type="checkbox" name="is_active" value="1" checked class="custom-control-input"><label class="custom-control-label" for="app-new-active">Ativo</label></div></td>
                            <td>Novo app</td>
                            <td><button class="btn btn-success btn-sm"><i class="fas fa-plus"></i> Criar</button></td>
                        </form>
                    </tr>
                    @foreach ($applications as $application)
                        <tr>
                            <form method="POST" action="{{ route('admin.applications.update', $application) }}">@csrf @method('PUT')
                                <td><input name="name" value="{{ $application->name }}" required class="form-control form-control-sm mb-2"><input name="code" value="{{ $application->code }}" required class="form-control form-control-sm"></td>
                                <td><div class="d-flex"><input type="color" name="primary_color" value="{{ $application->primary_color ?: '#16a34a' }}" class="form-control form-control-sm mr-2"><input type="color" name="secondary_color" value="{{ $application->secondary_color ?: '#0f172a' }}" class="form-control form-control-sm"></div></td>
                                <td><textarea name="settings" rows="2" class="form-control form-control-sm">{{ $application->settings ? json_encode($application->settings, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) : '' }}</textarea></td>
                                <td><div class="custom-control custom-switch"><input id="app-active-{{ $application->id }}" type="checkbox" name="is_active" value="1" @checked($application->is_active) class="custom-control-input"><label class="custom-control-label" for="app-active-{{ $application->id }}">Ativo</label></div></td>
                                <td class="small">{{ $application->professionals_count }} prof. / {{ $application->devices_count }} disp. / {{ $application->versions_count }} vers.</td>
                                <td class="text-nowrap"><button class="btn btn-primary btn-sm"><i class="fas fa-save"></i></button>
                            </form>
                                    <form method="POST" action="{{ route('admin.applications.destroy', $application) }}" class="d-inline" onsubmit="return confirm('Remover este aplicativo e seus dados vinculados?')">@csrf @method('DELETE')<button class="btn btn-danger btn-sm"><i class="fas fa-trash"></i></button></form>
                                </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
