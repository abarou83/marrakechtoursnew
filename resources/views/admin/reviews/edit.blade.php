@extends('admin.layout')

@section('title', 'Modifier un avis')

@section('content')
<div class="mb-6 bg-white p-6 rounded-lg shadow-sm border border-gray-200">
    <h2 class="text-lg font-semibold text-gray-900 mb-1">Modifier un avis</h2>
    <p class="text-sm text-gray-500">Modifiez les informations de cet avis.</p>
</div>

<form action="{{ route('admin.reviews.update', $review) }}" method="POST" class="bg-white rounded-lg shadow p-6 border border-gray-200">
    @csrf
    @method('PUT')

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
        {{-- Tour --}}
        <div>
            <label for="tour_id" class="block text-sm font-semibold text-gray-700 mb-2">
                Tour <span class="text-red-500">*</span>
            </label>
            <select name="tour_id" id="tour_id" required class="w-full border-gray-300 rounded-lg px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500">
                <option value="">Sélectionner un tour</option>
                @foreach($tours as $tour)
                    <option value="{{ $tour->id }}" {{ old('tour_id', $review->tour_id) == $tour->id ? 'selected' : '' }}>
                        {{ translate_model($tour, 'title') }}
                    </option>
                @endforeach
            </select>
            @error('tour_id')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        {{-- Utilisateur --}}
        <div>
            <label for="user_id" class="block text-sm font-semibold text-gray-700 mb-2">
                Utilisateur <span class="text-red-500">*</span>
            </label>
            <select name="user_id" id="user_id" required class="w-full border-gray-300 rounded-lg px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500">
                <option value="">Sélectionner un utilisateur</option>
                @foreach($users as $user)
                    <option value="{{ $user->id }}" {{ old('user_id', $review->user_id) == $user->id ? 'selected' : '' }}>
                        {{ $user->name }} ({{ $user->email }})
                    </option>
                @endforeach
            </select>
            @error('user_id')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>
    </div>

    {{-- Note --}}
    <div class="mb-6">
        <label for="rating" class="block text-sm font-semibold text-gray-700 mb-2">
            Note <span class="text-red-500">*</span>
        </label>
        <select name="rating" id="rating" required class="w-full border-gray-300 rounded-lg px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500">
            <option value="">Sélectionner une note</option>
            @for($i = 5; $i >= 1; $i--)
                <option value="{{ $i }}" {{ old('rating', $review->rating) == $i ? 'selected' : '' }}>
                    {{ $i }} étoile{{ $i > 1 ? 's' : '' }}
                    @for($j = 1; $j <= $i; $j++)
                        ⭐
                    @endfor
                </option>
            @endfor
        </select>
        @error('rating')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    {{-- Commentaire --}}
    <div class="mb-6">
        <label for="comment" class="block text-sm font-semibold text-gray-700 mb-2">
            Commentaire
        </label>
        <textarea name="comment" id="comment" rows="5" class="w-full border-gray-300 rounded-lg px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="Laissez un commentaire sur ce tour...">{{ old('comment', $review->comment) }}</textarea>
        @error('comment')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    {{-- Approuvé --}}
    <div class="mb-6">
        <label class="flex items-center">
            <input type="checkbox" name="is_approved" value="1" {{ old('is_approved', $review->is_approved) ? 'checked' : '' }} class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
            <span class="ml-2 text-sm text-gray-700">Approuver cet avis</span>
        </label>
    </div>

    {{-- Actions --}}
    <div class="flex items-center justify-end gap-4 pt-6 border-t border-gray-200">
        <a href="{{ route('admin.reviews.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 font-semibold text-sm">
            Annuler
        </a>
        <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 font-semibold text-sm">
            <i class="fas fa-save mr-2"></i>Enregistrer
        </button>
    </div>
</form>
@endsection


