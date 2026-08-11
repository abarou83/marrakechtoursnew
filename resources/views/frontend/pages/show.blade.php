@push('seo_meta_tags')
    <title>{{ $translation->meta_title ?? $translation->title }} - {{ config('app.name', 'Tourify') }}</title>
    <meta name="description" content="{{ $translation->meta_description ?? '' }}">
    @if($translation->meta_keywords)
        <meta name="keywords" content="{{ $translation->meta_keywords }}">
    @endif
    
    {{-- Open Graph / Facebook --}}
    <meta property="og:type" content="article">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:title" content="{{ $translation->meta_title ?? $translation->title }}">
    <meta property="og:description" content="{{ $translation->meta_description ?? '' }}">
    <meta property="og:site_name" content="{{ config('app.name', 'Tourify') }}">
    
    {{-- Twitter Card --}}
    <meta name="twitter:card" content="summary">
    <meta name="twitter:url" content="{{ url()->current() }}">
    <meta name="twitter:title" content="{{ $translation->meta_title ?? $translation->title }}">
    <meta name="twitter:description" content="{{ $translation->meta_description ?? '' }}">
    
    {{-- Canonical URL --}}
    <link rel="canonical" href="{{ url()->current() }}">
@endpush

<x-app-layout>
    <div class="bg-light min-h-screen py-12 md:py-16">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            {{-- Breadcrumb --}}
            <nav class="mb-8" aria-label="Breadcrumb">
                <ol class="flex items-center space-x-2 text-sm text-gray-500">
                    <li>
                        <a href="{{ route('home') }}" class="hover:text-primary transition-colors">
                            <i class="fas fa-home"></i>
                        </a>
                    </li>
                    <li>
                        <i class="fas fa-chevron-right text-xs mx-2 text-gray-400"></i>
                    </li>
                    <li class="text-gray-900 font-medium">
                        {{ $translation->title }}
                    </li>
                </ol>
            </nav>

            {{-- Page Header --}}
            <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-8 md:p-10 mb-8">
                <div class="mb-6 pb-6 border-b-2 border-primary">
                    <h1 class="text-4xl md:text-5xl font-bold text-gray-900 mb-4 leading-tight">
                        {{ $translation->title }}
                    </h1>
                    @if($translation->meta_description)
                        <p class="text-lg text-gray-600 leading-relaxed max-w-3xl">
                            {{ $translation->meta_description }}
                        </p>
                    @endif
                </div>
            </div>

            {{-- Page Content --}}
            <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-8 md:p-10 lg:p-12">
                <div class="prose prose-lg prose-slate max-w-none 
                            prose-headings:text-gray-900 prose-headings:font-bold prose-headings:mt-8 prose-headings:mb-4
                            prose-h1:text-3xl prose-h1:font-bold prose-h1:mt-10 prose-h1:mb-6 prose-h1:border-b prose-h1:border-primary prose-h1:pb-3
                            prose-h2:text-2xl prose-h2:font-bold prose-h2:mt-8 prose-h2:mb-4 prose-h2:text-primary
                            prose-h3:text-xl prose-h3:font-semibold prose-h3:mt-6 prose-h3:mb-3 prose-h3:text-gray-800
                            prose-p:text-gray-700 prose-p:leading-relaxed prose-p:mb-4
                            prose-a:text-primary prose-a:font-medium prose-a:underline prose-a:decoration-primary/30 prose-a:underline-offset-2
                            hover:prose-a:text-secondary hover:prose-a:decoration-secondary/50
                            prose-strong:text-gray-900 prose-strong:font-semibold
                            prose-ul:text-gray-700 prose-ul:my-4 prose-ul:space-y-2
                            prose-ol:text-gray-700 prose-ol:my-4 prose-ol:space-y-2
                            prose-li:text-gray-700 prose-li:leading-relaxed
                            prose-blockquote:border-l-4 prose-blockquote:border-primary prose-blockquote:pl-4 prose-blockquote:italic prose-blockquote:text-gray-600 prose-blockquote:bg-light/50 prose-blockquote:py-2 prose-blockquote:rounded-r
                            prose-hr:border-gray-200 prose-hr:my-8
                            prose-code:text-primary prose-code:bg-light prose-code:px-1.5 prose-code:py-0.5 prose-code:rounded prose-code:text-sm
                            prose-pre:bg-gray-900 prose-pre:text-gray-100">
                    {!! $translation->content !!}
                </div>
            </div>

            {{-- Back to Home --}}
            <div class="mt-8 pt-8 border-t border-gray-200">
                <a href="{{ route('home') }}" 
                   class="inline-flex items-center text-primary hover:text-secondary font-semibold transition-all duration-300 group">
                    <i class="fas fa-arrow-left mr-2 group-hover:-translate-x-1 transition-transform"></i>
                    Retour à l'accueil
                </a>
            </div>
        </div>
    </div>
</x-app-layout>

