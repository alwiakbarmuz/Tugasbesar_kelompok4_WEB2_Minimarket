@props(['hover' => true, 'border' => true])

<tr {{ $attributes->merge(['class' => ($hover ? 'hover:bg-gray-50 ' : '') . ($border ? 'border-b ' : '')]) }}>
    {{ $slot }}
</tr>