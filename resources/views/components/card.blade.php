@props(['title' => null, 'subtitle' => null, 'padding' => '6', 'shadow' => 'sm'])

<div class="bg-white overflow-hidden shadow-{{ $shadow }} rounded-lg">
    @if($title || $subtitle)
    <div class="px-{{ $padding }} py-4 border-b border-gray-200">
        @if($title)
        <h3 class="text-lg font-semibold text-gray-800">{{ $title }}</h3>
        @endif
        @if($subtitle)
        <p class="text-sm text-gray-500 mt-1">{{ $subtitle }}</p>
        @endif
    </div>
    @endif
    
    <div class="p-{{ $padding }}">
        {{ $slot }}
    </div>
</div>