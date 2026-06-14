@props(['width' => 'auto', 'height' => '46'])

<img src="{{ asset('images/logo/LogoJM.png') }}" 
     alt="{{ config('app.name', 'JayusmanMart') }}"
     {{ $attributes->merge(['class' => 'block h-auto']) }}
     style="width: {{ $width }}; height: {{ $height }}px;">