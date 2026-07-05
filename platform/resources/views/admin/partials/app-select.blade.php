<select name="{{ $name }}" @if(! empty($required)) required @endif class="form-control form-control-sm">
    @if (empty($required))
        <option value="">Global</option>
    @endif
    @foreach ($applications as $application)
        <option value="{{ $application->id }}" @selected((string) $value === (string) $application->id)>{{ $application->name }}</option>
    @endforeach
</select>
