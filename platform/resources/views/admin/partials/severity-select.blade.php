<select name="severity" class="form-control form-control-sm">
    @foreach (['info', 'success', 'warning', 'danger'] as $severity)
        <option value="{{ $severity }}" @selected($value === $severity)>{{ $severity }}</option>
    @endforeach
</select>
