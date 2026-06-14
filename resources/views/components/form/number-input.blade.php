@props(['name', 'id' => null, 'label' => null, 'min' => 0, 'max' => null, 'step' => 1, 'required' => false, 'value' => ''])

<div>
    @if($label)
    <label for="{{ $id }}" class="block text-sm font-medium text-gray-700 mb-1">
        {{ $label }}
        @if($required)
        <span class="text-red-500">*</span>
        @endif
    </label>
    @endif
    
    <input type="number" name="{{ $name }}" id="{{ $id }}" 
           min="{{ $min }}" max="{{ $max }}" step="{{ $step }}" 
           value="{{ $value }}" {{ $required ? 'required' : '' }}
           {{ $attributes->merge(['class' => 'rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 w-full']) }}>
</div>