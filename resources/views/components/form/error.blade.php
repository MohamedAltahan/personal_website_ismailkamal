@props(['name', 'message'])

@if ($message)
    @error($name)
        <div class="text-danger">
            {{ $message }}
        </div>
    @enderror
@endif
