@extends('admin.layout')

@section('title', 'Gestion des FAQs')

@section('content')
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900"><i class="fas fa-question-circle mr-2"></i>Gestion des FAQs</h1>
            <p class="text-gray-600 mt-2">Gérez les questions fréquentes de votre site</p>
        </div>
        <a href="{{ route('admin.faqs.create') }}" 
           class="px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 font-bold shadow-lg transition">
            <i class="fas fa-plus mr-2"></i>Ajouter une FAQ
        </a>
    </div>

    <div class="bg-white rounded-xl shadow-lg overflow-visible">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                        Ordre
                    </th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                        Question
                    </th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                        Statut
                    </th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                        Actions
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($faqs as $faq)
                    @php
                        $translation = $faq->translate();
                    @endphp
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="text-sm font-bold text-gray-900">{{ $faq->order }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm font-medium text-gray-900">
                                {{ $translation ? $translation->question : 'Aucune traduction' }}
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($faq->is_active)
                                <span class="px-3 py-1 text-xs font-bold bg-green-100 text-green-800 rounded-full">
                                    ✓ Active
                                </span>
                            @else
                                <span class="px-3 py-1 text-xs font-bold bg-gray-100 text-gray-600 rounded-full">
                                    ✗ Inactive
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <x-admin.action-menu>
                                <a href="{{ route('admin.faqs.edit', $faq) }}" class="flex items-center gap-2 px-4 py-2 hover:bg-gray-50">
                                    <i class="fas fa-edit text-indigo-500"></i>
                                    Modifier
                                </a>
                                <form action="{{ route('admin.faqs.destroy', $faq) }}" method="POST" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette FAQ ?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="flex w-full items-center gap-2 px-4 py-2 text-red-600 hover:bg-red-50">
                                        <i class="fas fa-trash"></i>
                                        Supprimer
                                    </button>
                                </form>
                            </x-admin.action-menu>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-6 py-8 text-center text-gray-500">
                            Aucune FAQ pour le moment.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
