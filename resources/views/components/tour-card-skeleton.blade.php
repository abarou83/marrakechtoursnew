@props([])

<div {{ $attributes->merge(['class' => 'card animate-pulse']) }}>
    {{-- Image placeholder --}}
    <div class="aspect-[4/3] bg-sand-200"></div>

    {{-- Content --}}
    <div class="p-4 space-y-3">
        {{-- Category --}}
        <div class="h-3 w-20 bg-sand-200 rounded"></div>

        {{-- Title --}}
        <div class="space-y-2">
            <div class="h-5 w-full bg-sand-200 rounded"></div>
            <div class="h-5 w-3/4 bg-sand-200 rounded"></div>
        </div>

        {{-- Features --}}
        <div class="flex gap-3">
            <div class="h-4 w-16 bg-sand-200 rounded"></div>
            <div class="h-4 w-24 bg-sand-200 rounded"></div>
        </div>

        {{-- Rating --}}
        <div class="flex gap-2">
            <div class="h-4 w-24 bg-sand-200 rounded"></div>
            <div class="h-4 w-16 bg-sand-200 rounded"></div>
        </div>

        {{-- Price --}}
        <div class="flex items-baseline gap-2 pt-2">
            <div class="h-4 w-16 bg-sand-200 rounded"></div>
            <div class="h-6 w-20 bg-sand-200 rounded"></div>
        </div>

        {{-- Reassurance --}}
        <div class="h-3 w-48 bg-sand-200 rounded"></div>
    </div>
</div>
