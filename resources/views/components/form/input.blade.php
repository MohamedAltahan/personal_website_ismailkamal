@props(['type' => 'text', 'name' => '', 'value' => '', 'class' => '', 'label' => ''])
@if ($label)
    <label for="">{{ $label }}</label>
@endif

<input type="{{ $type }}" name="{{ $name }}" value='{{ old($name, $value) }}'
    class="form-control {{ $class }}" {{ $attributes }}>

@error($name)
    <div class="text-danger">
        {{ $message }}
    </div>
@enderror

{{-- $attributes > will be replaced by any sent attributes which isn't denfined in propes --}}
