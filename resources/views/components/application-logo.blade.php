@php
    $logoPath = site_setting('logo_path');
    // Get dynamic colors for logo
    try {
        $primaryColor = primary_color();
        $secondaryColor = secondary_color();
    } catch (\Exception $e) {
        $primaryColor = '#211951';
        $secondaryColor = '#836FFF';
    }
@endphp

@if($logoPath && Storage::disk('public')->exists($logoPath))
    {{-- Custom uploaded logo --}}
    <img src="{{ Storage::url($logoPath) }}" {{ $attributes }} alt="{{ config('app.name') }}">
@else
    {{-- Default SVG logo with dynamic colors --}}
    <svg viewBox="0 0 200 200" xmlns="http://www.w3.org/2000/svg" {{ $attributes }}>
    <!-- Gradient Definitions -->
    <defs>
        <linearGradient id="logoGradient1" x1="0%" y1="0%" x2="100%" y2="100%">
            <stop offset="0%" style="stop-color:{{ $primaryColor }};stop-opacity:1" />
            <stop offset="100%" style="stop-color:{{ $secondaryColor }};stop-opacity:1" />
        </linearGradient>
        <filter id="shadow">
            <feDropShadow dx="0" dy="2" stdDeviation="3" flood-opacity="0.3"/>
        </filter>
    </defs>
    
    <!-- Globe Background (subtle) -->
    <circle cx="100" cy="100" r="80" fill="url(#logoGradient1)" opacity="0.15"/>
    
    <!-- Airplane Icon -->
    <g transform="translate(100, 100)" filter="url(#shadow)">
        <path d="M-30,-10 L-50,-25 L-25,-25 L-15,-35 L5,-25 L-5,-25 L15,-35 L25,-25 L50,-25 L30,-10 L50,25 L25,25 L15,35 L-5,25 L5,25 L-15,35 L-25,25 L-50,25 Z" 
              fill="url(#logoGradient1)"/>
    </g>
    
    <!-- Text -->
    <text x="100" y="150" 
          font-family="Arial, sans-serif" 
          font-size="24" 
          font-weight="bold" 
          fill="url(#logoGradient1)" 
          text-anchor="middle"
          filter="url(#shadow)">TourBo</text>
    </svg>
@endif
