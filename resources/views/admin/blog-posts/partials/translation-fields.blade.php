@php
    $translationIndex = $translationIndex ?? $loop->index ?? 0;
    $translation = $translation ?? null;
@endphp

<input type="hidden" name="translations[{{ $translationIndex }}][locale]" value="{{ $locale }}">

<div>
    <label class="block text-sm font-bold text-gray-700 mb-2">Slug (URL) <span class="text-red-500">*</span></label>
    <input type="text" name="translations[{{ $translationIndex }}][slug]"
           value="{{ old("translations.{$translationIndex}.slug", $translation?->slug ?? '') }}"
           placeholder="ex: decouvrir-marrakech"
           class="w-full border-gray-300 rounded-lg px-4 py-3" required>
    <p class="text-xs text-gray-500 mt-1">URL : /blog/[slug]</p>
    @error("translations.{$translationIndex}.slug")
        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
    @enderror
</div>

<div>
    <label class="block text-sm font-bold text-gray-700 mb-2">Titre <span class="text-red-500">*</span></label>
    <input type="text" name="translations[{{ $translationIndex }}][title]"
           value="{{ old("translations.{$translationIndex}.title", $translation?->title ?? '') }}"
           class="w-full border-gray-300 rounded-lg px-4 py-3" required>
    @error("translations.{$translationIndex}.title")
        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
    @enderror
</div>

<div>
    <label class="block text-sm font-bold text-gray-700 mb-2">Extrait</label>
    <textarea name="translations[{{ $translationIndex }}][excerpt]" rows="3"
              class="w-full border-gray-300 rounded-lg px-4 py-3"
              placeholder="Court résumé affiché dans la liste du blog">{{ old("translations.{$translationIndex}.excerpt", $translation?->excerpt ?? '') }}</textarea>
    @error("translations.{$translationIndex}.excerpt")
        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
    @enderror
</div>

<div class="blog-content-editor">
    <label class="block text-sm font-bold text-gray-700 mb-2">Contenu <span class="text-red-500">*</span></label>
    <textarea name="translations[{{ $translationIndex }}][content]" rows="20"
              class="w-full border-gray-300 rounded-lg px-4 py-3 js-blog-content-editor" required>{{ old("translations.{$translationIndex}.content", $translation?->content ?? '') }}</textarea>
    <p class="text-xs text-gray-500 mt-1">Utilisez l'éditeur pour formater le contenu (titres, listes, liens, images…)</p>
    @error("translations.{$translationIndex}.content")
        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
    @enderror
</div>

<div class="border-t border-gray-200 pt-6">
    <h5 class="text-md font-bold text-gray-900 mb-4"><i class="fas fa-search mr-2 text-indigo-600"></i>SEO</h5>
    <div class="space-y-4">
        <div>
            <label class="block text-sm font-bold text-gray-700 mb-2">Meta Title</label>
            <input type="text" name="translations[{{ $translationIndex }}][meta_title]"
                   value="{{ old("translations.{$translationIndex}.meta_title", $translation?->meta_title ?? '') }}"
                   maxlength="60" class="w-full border-gray-300 rounded-lg px-4 py-3">
        </div>
        <div>
            <label class="block text-sm font-bold text-gray-700 mb-2">Meta Description</label>
            <textarea name="translations[{{ $translationIndex }}][meta_description]" rows="2"
                      maxlength="160" class="w-full border-gray-300 rounded-lg px-4 py-3">{{ old("translations.{$translationIndex}.meta_description", $translation?->meta_description ?? '') }}</textarea>
        </div>
    </div>
</div>
