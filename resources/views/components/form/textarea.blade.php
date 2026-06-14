@props(['name', 'id' => null, 'label' => null, 'rows' => 3, 'placeholder' => null, 'required' => false, 'value' => ''])

<div>
    @if($label)
    <label for="{{ $id }}" class="block text-sm font-medium text-gray-700 mb-1">
        {{ $label }}
        @if($required)
        <span class="text-red-500">*</span>
        @endif
    </label>
    @endif
    
    <textarea name="{{ $name }}" id="{{ $id }}" rows="{{ $rows }}" 
              placeholder="{{ $placeholder }}" {{ $required ? 'required' : '' }}
              {{ $attributes->merge(['class' => 'rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 w-full']) }}>{{ $value }}</textarea>
</div>