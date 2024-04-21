@props(['name' => '', 'options', 'selected' => '', 'class' => '', 'label' => ''])
@if ($label)
    <label for="">{{ $label }}</label>
@endif
<select name="{{ $name }}" class="form-control {{ $class }}" {{ $attributes }}>


    @foreach ($options as $value => $text)
        <option value="{{ $value }}" @selected($value == $selected)>{{ $text }}</option>
    @endforeach

    @error($name)
        <div class="text-danger">
            {{ $message }}
        </div>
    @enderror

</select>
