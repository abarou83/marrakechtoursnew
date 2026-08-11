@props([
    'tours' => collect(),
    'limit' => 6,
])

<section
    x-data="{
        recentlyViewed: [],
        init() {
            this.loadRecentlyViewed();
        },
        loadRecentlyViewed() {
            const stored = localStorage.getItem('recently_viewed_tours');
            if (stored) {
                try {
                    this.recentlyViewed = JSON.parse(stored).slice(0, {{ $limit }});
                } catch (e) {
                    this.recentlyViewed = [];
                }
            }
        },
        hasItems() {
            return this.recentlyViewed.length > 0;
        }
    }"
    x-show="hasItems()"
    x-cloak
    {{ $attributes->merge(['class' => 'py-12 bg-white']) }}
>
    <div class="container-app">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-display font-bold text-sand-900">
                {{ __('Récemment consultés') }}
            </h2>
            <button
                type="button"
                @click="localStorage.removeItem('recently_viewed_tours'); recentlyViewed = []"
                class="text-sm text-sand-500 hover:text-sand-700"
            >
                {{ __('Effacer') }}
            </button>
        </div>

        {{-- Horizontal scrollable cards --}}
        <div class="relative -mx-4 px-4">
            <div
                class="flex gap-4 overflow-x-auto pb-4 snap-x snap-mandatory scrollbar-hide"
                style="-webkit-overflow-scrolling: touch;"
            >
                <template x-for="tour in recentlyViewed" :key="tour.id">
                    <a
                        :href="tour.url"
                        class="flex-shrink-0 w-64 snap-start"
                    >
                        <div class="card card-hover h-full">
                            <div class="aspect-[4/3] overflow-hidden">
                                <img
                                    :src="tour.image"
                                    :alt="tour.title"
                                    class="w-full h-full object-cover"
                                    loading="lazy"
                                />
                            </div>
                            <div class="p-3">
                                <h3
                                    class="font-medium text-sand-900 line-clamp-2 text-sm"
                                    x-text="tour.title"
                                ></h3>
                                <div class="mt-2 flex items-baseline gap-1">
                                    <span class="text-xs text-sand-500">{{ __('Dès') }}</span>
                                    <span class="font-bold text-primary-500" x-text="tour.price"></span>
                                </div>
                            </div>
                        </div>
                    </a>
                </template>
            </div>
        </div>
    </div>
</section>
