@props([
    'src',
    'alt' => '',
    'lazy' => true,
    'responsive' => false,
    'picture' => false,
    'placeholder' => null,
    'aspectRatio' => null,
])

@php
    $imgClass = $attributes->get('class', '');
    
    if ($aspectRatio) {
        $ratioClass = match($aspectRatio) {
            '16:9' => 'aspect-video',
            '4:3' => 'aspect-[4/3]',
            '3:2' => 'aspect-[3/2]',
            '1:1' => 'aspect-square',
            '2:3' => 'aspect-[2/3]',
            default => '',
        };
        $imgClass = trim($imgClass . ' ' . $ratioClass . ' object-cover');
    }

    $loadingAttr = $lazy ? 'lazy' : 'eager';
    $decodingAttr = $lazy ? 'async' : 'sync';

    $placeholderSrc = $placeholder ?? "data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 1 1'%3E%3C/svg%3E";
@endphp

@if($picture && $src)
    <picture>
        @if(Str::endsWith($src, ['.jpg', '.jpeg', '.png']))
            <source 
                srcset="{{ Str::replaceLast('.jpg', '.webp', Str::replaceLast('.jpeg', '.webp', Str::replaceLast('.png', '.webp', $src))) }}" 
                type="image/webp"
            >
        @endif
        <img 
            src="{{ $src }}"
            alt="{{ $alt }}"
            loading="{{ $loadingAttr }}"
            decoding="{{ $decodingAttr }}"
            {{ $attributes->merge(['class' => $imgClass]) }}
        >
    </picture>
@elseif($responsive && $src)
    <img 
        src="{{ $src }}"
        alt="{{ $alt }}"
        loading="{{ $loadingAttr }}"
        decoding="{{ $decodingAttr }}"
        sizes="(max-width: 640px) 100vw, (max-width: 1024px) 50vw, 33vw"
        {{ $attributes->merge(['class' => $imgClass]) }}
    >
@else
    <img 
        @if($lazy && $src)
            src="{{ $placeholderSrc }}"
            data-src="{{ $src }}"
            x-data
            x-intersect.once="$el.src = $el.dataset.src"
        @else
            src="{{ $src }}"
        @endif
        alt="{{ $alt }}"
        loading="{{ $loadingAttr }}"
        decoding="{{ $decodingAttr }}"
        {{ $attributes->merge(['class' => $imgClass]) }}
    >
@endif
