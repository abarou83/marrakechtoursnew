<div class="mb-6 flex justify-between items-center bg-white p-6 rounded-lg shadow-sm border border-gray-200">
    <div>
        <h2 class="text-lg font-semibold text-gray-900 mt-2">Promotions pour: {{ translate_model($tour, 'title') }}</h2>
        <p class="text-sm text-gray-500">Définissez des réductions appliquées automatiquement sur le tarif affiché.</p>
    </div>
    <a href="{{ route('admin.tour-promotions.create', $tour) }}" class="inline-flex items-center px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 font-bold shadow-lg hover:shadow-xl transition-all transform hover:scale-105">
        <i class="fas fa-plus mr-2"></i>Nouvelle promotion
    </a>
</div>

<div class="bg-white rounded-lg shadow overflow-visible">
    <table class="w-full">
        <thead class="bg-gray-50 border-b border-gray-200">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Nom</th>
                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Type</th>
                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Valeur</th>
                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Période</th>
                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Active</th>
                <th class="px-6 py-3 text-right text-xs font-semibold text-gray-600 uppercase">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
            @forelse($promotions as $promo)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 text-sm text-gray-900">
                        <div class="font-semibold">{{ $promo->name }}</div>
                        @if($promo->description)
                            <div class="text-xs text-gray-500">{{ Str::limit($promo->description, 80) }}</div>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-900">{{ $promo->discount_type === 'percentage' ? 'Pourcentage' : 'Montant fixe' }}</td>
                    <td class="px-6 py-4 text-sm text-gray-900">
                        @if($promo->discount_type === 'percentage')
                            -{{ number_format($promo->discount_value, 0) }}%
                        @else
                            -{{ \App\Helpers\CurrencyHelper::format(\App\Helpers\CurrencyHelper::convert((float)$promo->discount_value)) }}
                        @endif
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-900">{{ \Carbon\Carbon::parse($promo->start_date)->format('d/m/Y') }} → {{ \Carbon\Carbon::parse($promo->end_date)->format('d/m/Y') }}</td>
                    <td class="px-6 py-4">
                        @if($promo->is_active)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-green-100 text-green-800">Oui</span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-gray-100 text-gray-600">Non</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-right">
                        <x-admin.action-menu>
                            <a href="{{ route('admin.tour-promotions.edit', [$tour, $promo]) }}" class="flex items-center gap-2 px-4 py-2 hover:bg-gray-50">
                                <i class="fas fa-edit text-indigo-500"></i>
                                Modifier
                            </a>
                            <form method="POST" action="{{ route('admin.tour-promotions.destroy', [$tour, $promo]) }}" onsubmit="return confirm('Supprimer cette promotion ?');">
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
                    <td colspan="6" class="px-6 py-12 text-center text-gray-500">Aucune promotion définie.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
