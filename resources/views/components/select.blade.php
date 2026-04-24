@props(['label', 'name', 'options' => [], 'required' => false, 'selected' => '', 'placeholder' => 'Pilih...'])

<fieldset class="fieldset">
    <legend class="fieldset-legend">{{ $label }}</legend>
    <select name="{{ $name }}" {{ $required ? 'required' : '' }}
        {{ $attributes->merge(['class' => 'select select-bordered w-full' . ($errors->has($name) ? ' select-error' : '')]) }}>
        <option value="" disabled {{ old($name, $selected) ? '' : 'selected' }}>{{ $placeholder }}</option>
        @foreach ($options as $value => $text)
            <option value="{{ $value }}" {{ old($name, $selected) == $value ? 'selected' : '' }}>
                {{ $text }}
            </option>
        @endforeach
    </select>
    @error($name)
        <p class="label text-error text-sm">{{ $message }}</p>
    @enderror
</fieldset>
