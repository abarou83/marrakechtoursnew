@php
    $availableLocales = \App\Models\Language::active()->pluck('code')->toArray();
    if (empty($availableLocales)) {
        $availableLocales = ['fr', 'en', 'es'];
    }
@endphp

<div class="space-y-6">
    <div class="border border-gray-200 rounded-lg p-6 grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-bold text-gray-700 mb-2">Catégorie</label>
            <select name="category" class="w-full border-gray-300 rounded-lg px-4 py-3" required>
                @foreach($categories as $key => $label)
                    <option value="{{ $key }}" @selected(old('category', $guide?->category) === $key)>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-sm font-bold text-gray-700 mb-2">Temps de lecture (min)</label>
            <input type="number" name="reading_time" min="1" max="120"
                   value="{{ old('reading_time', $guide?->reading_time ?? 5) }}"
                   class="w-full border-gray-300 rounded-lg px-4 py-3">
        </div>
        <div>
            <label class="block text-sm font-bold text-gray-700 mb-2">Position</label>
            <input type="number" name="position" min="0"
                   value="{{ old('position', $guide?->position ?? 0) }}"
                   class="w-full border-gray-300 rounded-lg px-4 py-3">
        </div>
        <div>
            <label class="block text-sm font-bold text-gray-700 mb-2">Date de publication</label>
            <input type="datetime-local" name="published_at"
                   value="{{ old('published_at', optional($guide?->published_at)->format('Y-m-d\TH:i') ?? now()->format('Y-m-d\TH:i')) }}"
                   class="w-full border-gray-300 rounded-lg px-4 py-3">
        </div>
        <div class="md:col-span-2">
            <label class="block text-sm font-bold text-gray-700 mb-2">Image</label>
            @if($guide?->featured_image)
                <img src="{{ asset('storage/' . $guide->featured_image) }}" alt="" class="h-24 mb-2 rounded">
                <label class="flex items-center gap-2 text-sm">
                    <input type="checkbox" name="remove_featured_image" value="1"> Supprimer l'image
                </label>
            @endif
            <input type="file" name="featured_image" accept="image/*" class="w-full mt-2 border-gray-300 rounded-lg px-4 py-2">
        </div>
        <div class="md:col-span-2 flex items-center">
            <input type="checkbox" name="is_published" id="is_published" value="1"
                   {{ old('is_published', $guide?->is_published ?? true) ? 'checked' : '' }}
                   class="w-5 h-5 text-green-600 border-gray-300 rounded">
            <label for="is_published" class="ml-3 text-sm font-medium text-gray-700">Publié</label>
        </div>
    </div>

    <div class="border border-gray-200 rounded-lg p-6" x-data="{ tab: '{{ $availableLocales[0] }}' }">
        <h3 class="text-lg font-bold mb-4">Traductions</h3>
        <div class="flex gap-2 mb-4 border-b pb-2">
            @foreach($availableLocales as $locale)
                <button type="button" @click="tab='{{ $locale }}'"
                        :class="tab==='{{ $locale }}' ? 'text-indigo-600 border-indigo-500' : 'text-gray-500 border-transparent'"
                        class="px-3 py-1 border-b-2 text-sm font-semibold uppercase">{{ $locale }}</button>
            @endforeach
        </div>
        @foreach($availableLocales as $locale)
            @php
                $translation = $guide?->translations->firstWhere('locale', $locale);
                $idx = $loop->index;
            @endphp
            <div x-show="tab==='{{ $locale }}'" class="space-y-4">
                <input type="hidden" name="translations[{{ $idx }}][locale]" value="{{ $locale }}">
                <div>
                    <label class="block text-sm font-bold mb-1">Titre ({{ $locale }})</label>
                    <input type="text" name="translations[{{ $idx }}][title]" required
                           value="{{ old("translations.{$idx}.title", $translation?->title) }}"
                           class="w-full border-gray-300 rounded-lg px-4 py-3">
                </div>
                <div>
                    <label class="block text-sm font-bold mb-1">Slug</label>
                    <input type="text" name="translations[{{ $idx }}][slug]" required
                           value="{{ old("translations.{$idx}.slug", $translation?->slug) }}"
                           class="w-full border-gray-300 rounded-lg px-4 py-3">
                </div>
                <div>
                    <label class="block text-sm font-bold mb-1">Extrait</label>
                    <textarea name="translations[{{ $idx }}][excerpt]" rows="2"
                              class="w-full border-gray-300 rounded-lg px-4 py-3">{{ old("translations.{$idx}.excerpt", $translation?->excerpt) }}</textarea>
                </div>
                <div>
                    <label class="block text-sm font-bold mb-1">Contenu HTML</label>
                    <textarea name="translations[{{ $idx }}][content]" rows="10" required
                              class="w-full border-gray-300 rounded-lg px-4 py-3 font-mono text-sm">{{ old("translations.{$idx}.content", $translation?->content) }}</textarea>
                </div>
                <div class="grid md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-bold mb-1">Meta title</label>
                        <input type="text" name="translations[{{ $idx }}][meta_title]"
                               value="{{ old("translations.{$idx}.meta_title", $translation?->meta_title) }}"
                               class="w-full border-gray-300 rounded-lg px-4 py-3">
                    </div>
                    <div>
                        <label class="block text-sm font-bold mb-1">Meta description</label>
                        <input type="text" name="translations[{{ $idx }}][meta_description]"
                               value="{{ old("translations.{$idx}.meta_description", $translation?->meta_description) }}"
                               class="w-full border-gray-300 rounded-lg px-4 py-3">
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    @if($tours->isNotEmpty())
        <div class="border border-gray-200 rounded-lg p-6">
            <h3 class="text-lg font-bold mb-4">Tours liés</h3>
            <div class="grid md:grid-cols-2 gap-2 max-h-48 overflow-y-auto">
                @foreach($tours as $tour)
                    <label class="flex items-center gap-2 text-sm">
                        <input type="checkbox" name="tour_ids[]" value="{{ $tour->id }}"
                               @checked(in_array($tour->id, old('tour_ids', $guide?->tours->pluck('id')->all() ?? [])))>
                        {{ $tour->translate()?->title ?? $tour->title }}
                    </label>
                @endforeach
            </div>
        </div>
    @endif

    <div class="flex gap-4">
        <button type="submit" class="px-8 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 font-bold">Enregistrer</button>
        <a href="{{ route('admin.guides.index') }}" class="px-8 py-3 bg-gray-300 text-gray-700 rounded-lg font-bold">Annuler</a>
    </div>
</div>
