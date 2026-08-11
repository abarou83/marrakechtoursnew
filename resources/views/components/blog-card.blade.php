@props(['post'])

@php
    $translation = $post->translate();
    $slug = $translation?->slug;
@endphp

@if($translation && $slug)
    <article class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-lg transition-shadow duration-300 flex flex-col h-full">
        <a href="{{ route('blog.show', $slug) }}" class="block">
            @if($post->featured_image)
                <img src="{{ Storage::url($post->featured_image) }}"
                     alt="{{ $translation->title }}"
                     class="w-full h-48 object-cover">
            @else
                <div class="w-full h-48 bg-gradient-to-br from-teal-400 to-blue-500 flex items-center justify-center">
                    <i class="fas fa-newspaper text-white text-4xl opacity-80"></i>
                </div>
            @endif
        </a>
        <div class="p-5 flex flex-col flex-1">
            <div class="flex items-center gap-3 text-xs text-gray-500 mb-3">
                @if($post->published_at)
                    <span><i class="far fa-calendar mr-1"></i>{{ $post->published_at->format('d/m/Y') }}</span>
                @endif
                @if($post->author)
                    <span><i class="far fa-user mr-1"></i>{{ $post->author }}</span>
                @endif
            </div>
            <h2 class="text-lg font-bold text-gray-900 mb-2 line-clamp-2">
                <a href="{{ route('blog.show', $slug) }}" class="hover:text-primary transition">{{ $translation->title }}</a>
            </h2>
            @if($translation->excerpt)
                <p class="text-sm text-gray-600 line-clamp-3 mb-4 flex-1">{{ $translation->excerpt }}</p>
            @endif
            <a href="{{ route('blog.show', $slug) }}"
               class="inline-flex items-center text-sm font-semibold text-primary hover:text-secondary transition mt-auto">
                {{ __('Read more') }}
                <i class="fas fa-arrow-right ml-2 text-xs"></i>
            </a>
        </div>
    </article>
@endif
