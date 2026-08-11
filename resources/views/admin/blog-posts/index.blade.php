@extends('admin.layout')

@section('title', 'Gestion du Blog')

@section('content')
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900"><i class="fas fa-blog mr-2"></i>Gestion du Blog</h1>
            <p class="text-gray-600 mt-2">Créez et gérez les articles affichés sur le site</p>
        </div>
        <div class="flex flex-col lg:items-end gap-3">
            <div class="flex flex-wrap items-center gap-2">
                <a href="{{ route('blog.index') }}" target="_blank"
                   class="px-4 py-3 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 font-semibold transition">
                    <i class="fas fa-external-link-alt mr-2"></i>Voir le blog
                </a>
                <a href="{{ route('admin.blog-posts.create') }}"
                   class="px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 font-bold shadow-lg transition">
                    <i class="fas fa-plus mr-2"></i>Nouvel article
                </a>
            </div>

            <div class="flex flex-wrap items-center gap-2 bg-gray-50 border border-gray-200 rounded-lg p-3">
                <a href="{{ route('admin.blog-posts.import.example.download') }}"
                   class="inline-flex items-center px-4 py-2.5 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-100 font-semibold text-sm">
                    <i class="fas fa-download mr-2"></i>Télécharger JSON exemple
                </a>
                <form method="POST" action="{{ route('admin.blog-posts.import.example') }}">
                    @csrf
                    <button type="submit"
                            class="inline-flex items-center px-4 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-semibold text-sm">
                        <i class="fas fa-file-import mr-2"></i>Importer l'exemple
                    </button>
                </form>
                <form method="POST" action="{{ route('admin.blog-posts.import.json') }}" enctype="multipart/form-data" class="flex flex-wrap items-center gap-2">
                    @csrf
                    <input type="file"
                           name="json_file"
                           accept=".json,application/json,text/plain"
                           class="text-sm border border-gray-300 rounded-lg px-3 py-2 bg-white max-w-xs"
                           required>
                    <button type="submit"
                            class="inline-flex items-center px-4 py-2.5 bg-gray-900 text-white rounded-lg hover:bg-black font-semibold text-sm">
                        <i class="fas fa-upload mr-2"></i>Importer JSON
                    </button>
                </form>
            </div>
        </div>
    </div>

    @if($errors->has('import'))
        <div class="mb-6 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
            {{ $errors->first('import') }}
        </div>
    @endif

    @if(session('success'))
        <div class="mb-6 bg-green-50 border-l-4 border-green-500 p-4 rounded">
            <p class="text-green-700">{{ session('success') }}</p>
        </div>
    @endif

    <div class="bg-white rounded-xl shadow-lg overflow-visible">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase">Image</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase">Titre</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase">Publication</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase">Statut</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($posts as $post)
                    @php $translation = $post->translate(); @endphp
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4">
                            @if($post->featured_image)
                                <img src="{{ Storage::url($post->featured_image) }}" alt="" class="w-16 h-12 object-cover rounded-lg">
                            @else
                                <div class="w-16 h-12 bg-gray-100 rounded-lg flex items-center justify-center text-gray-400">
                                    <i class="fas fa-image"></i>
                                </div>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm font-semibold text-gray-900">{{ $translation?->title ?? 'Sans titre' }}</div>
                            <div class="text-xs text-gray-500">{{ $translation?->slug }}</div>
                            @if($post->author)
                                <div class="text-xs text-gray-400 mt-1"><i class="fas fa-user mr-1"></i>{{ $post->author }}</div>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">
                            {{ $post->published_at?->format('d/m/Y H:i') ?? '—' }}
                        </td>
                        <td class="px-6 py-4">
                            @if($post->is_active)
                                <span class="px-3 py-1 text-xs font-bold bg-green-100 text-green-800 rounded-full">Publié</span>
                            @else
                                <span class="px-3 py-1 text-xs font-bold bg-gray-100 text-gray-600 rounded-full">Brouillon</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm font-medium">
                            <x-admin.action-menu>
                                @if($translation?->slug)
                                    <a href="{{ route('blog.show', $translation->slug) }}" target="_blank" class="flex items-center gap-2 px-4 py-2 hover:bg-gray-50">
                                        <i class="fas fa-eye text-blue-500"></i>Voir
                                    </a>
                                @endif
                                <a href="{{ route('admin.blog-posts.edit', $post) }}" class="flex items-center gap-2 px-4 py-2 hover:bg-gray-50">
                                    <i class="fas fa-edit text-indigo-500"></i>Modifier
                                </a>
                                <form action="{{ route('admin.blog-posts.destroy', $post) }}" method="POST" onsubmit="return confirm('Supprimer cet article ?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="flex w-full items-center gap-2 px-4 py-2 text-red-600 hover:bg-red-50">
                                        <i class="fas fa-trash"></i>Supprimer
                                    </button>
                                </form>
                            </x-admin.action-menu>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-8 text-center text-gray-500">Aucun article pour le moment.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        @if($posts->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">{{ $posts->links() }}</div>
        @endif
    </div>
@endsection
