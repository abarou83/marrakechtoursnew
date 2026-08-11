@extends('admin.layout')

@section('title', 'Guides SEO')

@section('content')
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900"><i class="fas fa-map-signs mr-2"></i>Guides SEO</h1>
            <p class="text-gray-600 mt-2">Guides pratiques pour le référencement et l'aide à la décision</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('guides.index') }}" target="_blank"
               class="px-4 py-3 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 font-semibold">
                <i class="fas fa-external-link-alt mr-2"></i>Voir les guides
            </a>
            <a href="{{ route('admin.guides.create') }}"
               class="px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 font-bold">
                <i class="fas fa-plus mr-2"></i>Nouveau guide
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="mb-6 p-4 bg-green-50 border border-green-200 text-green-800 rounded-lg">{{ session('success') }}</div>
    @endif

    <div class="bg-white rounded-xl shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Titre (FR)</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Catégorie</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Statut</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Vues</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($guides as $guide)
                    @php $fr = $guide->translations->firstWhere('locale', 'fr') ?? $guide->translations->first(); @endphp
                    <tr>
                        <td class="px-6 py-4">
                            <div class="font-medium text-gray-900">{{ $fr?->title ?? '—' }}</div>
                            <div class="text-xs text-gray-400">{{ $fr?->slug }}</div>
                        </td>
                        <td class="px-6 py-4 text-sm capitalize">{{ $guide->category }}</td>
                        <td class="px-6 py-4">
                            @if($guide->is_published)
                                <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">Publié</span>
                            @else
                                <span class="px-2 py-1 text-xs rounded-full bg-gray-100 text-gray-600">Brouillon</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ number_format($guide->views_count) }}</td>
                        <td class="px-6 py-4 text-right space-x-2">
                            @if($fr)
                                <a href="{{ route('guides.show', $fr->slug) }}" target="_blank" class="text-gray-500 hover:text-gray-700"><i class="fas fa-eye"></i></a>
                            @endif
                            <a href="{{ route('admin.guides.edit', $guide) }}" class="text-blue-600 hover:text-blue-800"><i class="fas fa-edit"></i></a>
                            <form method="POST" action="{{ route('admin.guides.destroy', $guide) }}" class="inline" onsubmit="return confirm('Supprimer ce guide ?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-800"><i class="fas fa-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="px-6 py-8 text-center text-gray-500">Aucun guide. Lancez <code class="bg-gray-100 px-1 rounded">php artisan db:seed --class=GuideSeeder</code></td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($guides->hasPages())
        <div class="mt-6">{{ $guides->links() }}</div>
    @endif
@endsection
