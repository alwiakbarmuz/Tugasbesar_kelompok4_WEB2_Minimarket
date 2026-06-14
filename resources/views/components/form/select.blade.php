@props(['name', 'id' => null, 'label' => null, 'options' => [], 'selected' => null, 'required' => false])

<div>
    @if($label)
    <label for="{{ $id }}" class="block text-sm font-medium text-gray-700 mb-1">
        {{ $label }}
        @if($required)
        <span class="text-red-500">*</span>
        @endif
    </label>
    @endif
    
    <select name="{{ $name }}" id="{{ $id }}" {{ $required ? 'required' : '' }}
            {{ $attributes->merge(['class' => 'rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 w-full']) }}>
        @foreach($options as $value => $text)
            <option value="{{ $value }}" {{ ($selected == $value) ? 'selected' : '' }}>{{ $text }}</option>
        @endforeach
    </select>
</div>