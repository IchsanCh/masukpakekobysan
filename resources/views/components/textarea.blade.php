@props(['label', 'name', 'placeholder' => '', 'required' => false, 'value' => '', 'rows' => 3])

<fieldset class="fieldset">
    <legend class="fieldset-legend">{{ $label }}</legend>
    <textarea name="{{ $name }}" rows="{{ $rows }}" placeholder="{{ $placeholder }}"
        {{ $required ? 'required' : '' }}
        {{ $attributes->merge(['class' => 'textarea textarea-bordered w-full' . ($errors->has($name) ? ' textarea-error' : '')]) }}>{{ old($name, $value) }}</textarea>
    @error($name)
        <p class="label text-error text-sm">{{ $message }}</p>
    @enderror
</fieldset>
