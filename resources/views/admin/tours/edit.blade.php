@extends('admin.layout')

@section('title', 'Modifier : ' . $tour->title)

@push('styles')
<style>[x-cloak] { display: none !important; }</style>
@endpush

@section('content')
    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between bg-white p-6 rounded-lg shadow-sm border border-gray-200">
        <div class="flex items-center min-w-0">
            @if($tour->images->first())
                <img src="{{ Storage::url($tour->images->first()->path) }}"
                     alt="{{ $tour->title }}"
                     class="w-12 h-12 rounded-lg object-cover mr-3 flex-shrink-0">
            @else
                <div class="w-12 h-12 rounded-lg bg-gray-200 flex items-center justify-center mr-3 flex-shrink-0">
                    <i class="fas fa-image text-gray-400 text-sm"></i>
                </div>
            @endif
            <div class="min-w-0">
                <h1 class="text-lg font-semibold text-gray-900 truncate">
                    {{ $tour->title }}
                    <a href="{{ route('tours.show', $tour->url_key) }}"
                       target="_blank"
                       rel="noopener noreferrer"
                       class="inline-flex align-middle ml-1 text-blue-600 hover:text-blue-800"
                       title="Prévisualiser sur le site">
                        <i class="fas fa-external-link-alt text-xs"></i>
                    </a>
                </h1>
                <p class="text-xs text-gray-500 truncate">{{ $tour->slug }}</p>
            </div>
        </div>
        <a href="{{ route('admin.tours.index') }}"
           class="inline-flex items-center text-purple-600 hover:text-purple-800 font-semibold flex-shrink-0">
            <span aria-hidden="true" class="mr-1.5">&larr;</span>
            Retour aux tours
        </a>
    </div>

    @if($errors->any())
        <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded">
            <div class="flex items-start">
                <svg class="w-6 h-6 text-red-500 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <div>
                    <p class="font-semibold text-red-800 mb-2">Erreur :</p>
                    <ul class="list-disc list-inside text-red-700 space-y-1">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endif

    {{-- Tabs: Modifier le tour / dates / tarifs / add-ons / promotions --}}
    <div class="bg-white rounded-xl shadow-lg border border-gray-200 overflow-hidden mb-6" x-data="{ activeTab: '{{ request('tab', 'edit') }}' }">
        <div class="border-b border-gray-200 bg-gray-50 px-4">
            <div class="flex flex-wrap gap-1 pt-2">
                <button type="button"
                    @click="activeTab = 'edit'"
                    :class="activeTab === 'edit' ? 'border-indigo-500 text-indigo-600 bg-white' : 'border-transparent text-gray-600 hover:text-gray-900 hover:border-gray-300'"
                    class="inline-flex items-center px-4 py-2.5 border-b-2 text-sm font-medium transition-colors">
                    <i class="fas fa-edit mr-2 text-indigo-500"></i>Modifier le tour
                </button>
                <button type="button"
                    @click="activeTab = 'dates'"
                    :class="activeTab === 'dates' ? 'border-indigo-500 text-indigo-600 bg-white' : 'border-transparent text-gray-600 hover:text-gray-900 hover:border-gray-300'"
                    class="inline-flex items-center px-4 py-2.5 border-b-2 text-sm font-medium transition-colors">
                    <i class="fas fa-calendar-alt mr-2 text-indigo-500"></i>Gérer les dates
                </button>
                <button type="button"
                    @click="activeTab = 'tarifs'"
                    :class="activeTab === 'tarifs' ? 'border-indigo-500 text-indigo-600 bg-white' : 'border-transparent text-gray-600 hover:text-gray-900 hover:border-gray-300'"
                    class="inline-flex items-center px-4 py-2.5 border-b-2 text-sm font-medium transition-colors">
                    <i class="fas fa-tags mr-2 text-indigo-500"></i>Gérer les tarifs
                </button>
                <button type="button"
                    @click="activeTab = 'addons'"
                    :class="activeTab === 'addons' ? 'border-indigo-500 text-indigo-600 bg-white' : 'border-transparent text-gray-600 hover:text-gray-900 hover:border-gray-300'"
                    class="inline-flex items-center px-4 py-2.5 border-b-2 text-sm font-medium transition-colors">
                    <i class="fas fa-puzzle-piece mr-2 text-indigo-500"></i>Gérer les add-ons
                </button>
                <button type="button"
                    @click="activeTab = 'promotions'"
                    :class="activeTab === 'promotions' ? 'border-indigo-500 text-indigo-600 bg-white' : 'border-transparent text-gray-600 hover:text-gray-900 hover:border-gray-300'"
                    class="inline-flex items-center px-4 py-2.5 border-b-2 text-sm font-medium transition-colors">
                    <i class="fas fa-percent mr-2 text-indigo-500"></i>Gérer les promotions
                </button>
            </div>
        </div>
        <div class="p-6">
            <div x-show="activeTab === 'edit'" x-transition>
                @include('admin.tours.partials.tab-edit')
            </div>
            <div x-show="activeTab === 'dates'" x-transition x-cloak>
                @include('admin.tours.partials.tab-dates')
            </div>
            <div x-show="activeTab === 'tarifs'" x-transition x-cloak>
                @include('admin.tours.partials.tab-pricings')
            </div>
            <div x-show="activeTab === 'addons'" x-transition x-cloak>
                @include('admin.tours.partials.tab-addons')
            </div>
            <div x-show="activeTab === 'promotions'" x-transition x-cloak>
                @include('admin.tours.partials.tab-promotions')
            </div>
        </div>
    </div>

    <script>
        // Validation: au moins une catégorie doit être sélectionnée
        const updateForm = document.querySelector('form[action*="/admin/tours/"][method="POST"]');
        if (updateForm) {
            updateForm.addEventListener('submit', function(e) {
                const checkboxes = document.querySelectorAll('input[name="category_ids[]"]:checked');
                if (checkboxes.length === 0) {
                    e.preventDefault();
                    alert('Veuillez sélectionner au moins une catégorie.');
                    return false;
                }
            });
        }
    </script>

    @push('scripts')
        <script src="https://cdn.ckeditor.com/ckeditor5/39.0.1/classic/ckeditor.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const editors = [];

                document.querySelectorAll('.js-tour-description-editor').forEach((textarea) => {
                    ClassicEditor.create(textarea).then((editor) => {
                        editors.push(editor);
                    }).catch((error) => {
                        console.error('CKEditor init error:', error);
                    });
                });

                const form = document.querySelector('form[action*="/admin/tours/"][method="POST"]');
                if (form) {
                    form.addEventListener('submit', function () {
                        editors.forEach((editor) => editor.updateSourceElement());
                    });
                }
            });
        </script>
    @endpush
@endsection

