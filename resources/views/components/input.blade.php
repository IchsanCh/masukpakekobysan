@props(['label', 'name', 'type' => 'text', 'placeholder' => '', 'required' => false, 'value' => ''])

<fieldset class="fieldset">
    <legend class="fieldset-legend">{{ $label }}</legend>
    <input type="{{ $type }}" name="{{ $name }}" value="{{ old($name, $value) }}"
        placeholder="{{ $placeholder }}" {{ $required ? 'required' : '' }}
        {{ $attributes->merge(['class' => 'input input-bordered w-full' . ($errors->has($name) ? ' input-error' : '')]) }} />
    @error($name)
        <p class="label text-error text-sm">{{ $message }}</p>
    @enderror
</fieldset>
