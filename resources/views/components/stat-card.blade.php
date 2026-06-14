@props(['title', 'value', 'icon', 'color' => 'blue', 'trend' => null, 'trendValue' => null])

<div class="bg-white overflow-hidden shadow-lg rounded-xl hover:shadow-xl transition-all duration-300">
    <div class="p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-500 uppercase tracking-wide">{{ $title }}</p>
                <p class="text-2xl font-bold text-gray-800 mt-2">{{ $value }}</p>
                @if($trend)
                <div class="flex items-center mt-3">
                    @if($trend === 'up')
                        <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                        </svg>
                        <span class="text-sm text-green-600 ml-1">{{ $trendValue }}</span>
                    @elseif($trend === 'down')
                        <svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0v-8m0 8l-8-8-4 4-6-6"></path>
                        </svg>
                        <span class="text-sm text-red-600 ml-1">{{ $trendValue }}</span>
                    @endif
                    <span class="text-xs text-gray-400 ml-2">vs kemarin</span>
                </div>
                @endif
            </div>
            <div class="rounded-full p-3 bg-{{ $color }}-100">
                <i class="fas fa-{{ $icon }} text-{{ $color }}-600 text-2xl"></i>
            </div>
        </div>
    </div>
</div>