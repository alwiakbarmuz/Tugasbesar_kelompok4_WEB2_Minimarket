@props(['type' => 'info', 'message' => null, 'dismissible' => false])

@php
    $colors = [
        'success' => 'green',
        'error' => 'red',
        'warning' => 'yellow',
        'info' => 'blue',
    ];
    $color = $colors[$type] ?? 'blue';
    
    $icons = [
        'success' => 'check-circle',
        'error' => 'exclamation-circle',
        'warning' => 'exclamation-triangle',
        'info' => 'information-circle',
    ];
    $icon = $icons[$type] ?? 'information-circle';
@endphp

@if($message || !empty($slot))
<div x-data="{ show: true }" x-show="show" x-transition.duration.300ms 
     class="rounded-lg bg-{{ $color }}-50 border-l-4 border-{{ $color }}-500 p-4 mb-4">
    <div class="flex items-center">
        <div class="flex-shrink-0">
            <i class="fas fa-{{ $icon }} text-{{ $color }}-500 text-lg"></i>
        </div>
        <div class="ml-3">
            <p class="text-sm text-{{ $color }}-700">{{ $message ?? $slot }}</p>
        </div>
        @if($dismissible)
        <div class="ml-auto pl-3">
            <button @click="show = false" type="button" 
                    class="inline-flex rounded-md p-1.5 text-{{ $color }}-500 hover:bg-{{ $color }}-100 focus:outline-none">
                <i class="fas fa-times"></i>
            </button>
        </div>
        @endif
    </div>
</div>
@endif