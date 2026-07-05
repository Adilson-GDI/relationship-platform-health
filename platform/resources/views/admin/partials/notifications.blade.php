<div id="notifications" class="card shadow mb-4">
    <div class="card-header py-3"><h6 class="m-0 font-weight-bold text-primary">Push notifications</h6></div>
    <div class="card-body table-responsive">
        <table class="table table-bordered table-sm">
            <thead><tr><th>Destino</th><th>Conteudo</th><th>Data JSON</th><th>Agenda</th><th>Status</th><th>Acoes</th></tr></thead>
            <tbody>
                <tr class="bg-light">
                    <form method="POST" action="{{ route('admin.notifications.store') }}">@csrf
                        <td>
                            @include('admin.partials.app-select', ['name' => 'health_application_id', 'value' => null, 'applications' => $applications, 'required' => false])
                            <select name="professional_id" class="form-control form-control-sm mt-2">
                                <option value="">Todos</option>
                                @foreach ($professionals as $professional)
                                    <option value="{{ $professional->id }}">{{ $professional->name }}</option>
                                @endforeach
                            </select>
                        </td>
                        <td><input name="title" required class="form-control form-control-sm mb-2" placeholder="Titulo"><textarea name="body" required rows="2" class="form-control form-control-sm" placeholder="Mensagem"></textarea></td>
                        <td><textarea name="data" rows="2" class="form-control form-control-sm"></textarea></td>
                        <td><input type="datetime-local" name="scheduled_at" class="form-control form-control-sm"></td>
                        <td>@include('admin.partials.notification-status-select', ['value' => 'draft'])</td>
                        <td><button class="btn btn-success btn-sm"><i class="fas fa-plus"></i></button></td>
                    </form>
                </tr>
                @foreach ($notifications as $notification)
                    <tr>
                        <form method="POST" action="{{ route('admin.notifications.update', $notification) }}">@csrf @method('PUT')
                            <td>
                                @include('admin.partials.app-select', ['name' => 'health_application_id', 'value' => $notification->health_application_id, 'applications' => $applications, 'required' => false])
                                <select name="professional_id" class="form-control form-control-sm mt-2">
                                    <option value="">Todos</option>
                                    @foreach ($professionals as $professional)
                                        <option value="{{ $professional->id }}" @selected($notification->professional_id === $professional->id)>{{ $professional->name }}</option>
                                    @endforeach
                                </select>
                            </td>
                            <td><input name="title" value="{{ $notification->title }}" required class="form-control form-control-sm mb-2"><textarea name="body" required rows="2" class="form-control form-control-sm">{{ $notification->body }}</textarea></td>
                            <td><textarea name="data" rows="2" class="form-control form-control-sm">{{ $notification->data ? json_encode($notification->data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) : '' }}</textarea></td>
                            <td><input type="datetime-local" name="scheduled_at" value="{{ $notification->scheduled_at?->format('Y-m-d\\TH:i') }}" class="form-control form-control-sm mb-2"><input type="datetime-local" name="sent_at" value="{{ $notification->sent_at?->format('Y-m-d\\TH:i') }}" class="form-control form-control-sm"></td>
                            <td>@include('admin.partials.notification-status-select', ['value' => $notification->status])</td>
                            <td class="text-nowrap"><button class="btn btn-primary btn-sm"><i class="fas fa-save"></i></button>
                        </form>
                                <form method="POST" action="{{ route('admin.notifications.destroy', $notification) }}" class="d-inline" onsubmit="return confirm('Excluir notificacao?')">@csrf @method('DELETE')<button class="btn btn-danger btn-sm"><i class="fas fa-trash"></i></button></form>
                            </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
