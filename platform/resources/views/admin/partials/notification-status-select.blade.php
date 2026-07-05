<select name="status" class="form-control form-control-sm">
    @foreach (['draft', 'scheduled', 'sent', 'canceled'] as $status)
        <option value="{{ $status }}" @selected($value === $status)>{{ $status }}</option>
    @endforeach
</select>
