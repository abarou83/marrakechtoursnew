@extends('admin.layout')

@section('title', 'Détail audit #' . $log->id)

@section('content')
<div class="space-y-6">
    {{-- Header --}}
    <div class="flex items-center gap-4">
        <a href="{{ route('admin.audit-logs.index') }}" class="text-gray-500 hover:text-gray-700">
            <x-heroicon-o-arrow-left class="w-6 h-6" />
        </a>
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Entrée d'audit #{{ $log->id }}</h1>
            <p class="mt-1 text-sm text-gray-500">
                {{ $log->created_at->format('d/m/Y à H:i:s') }}
            </p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Main Info --}}
        <div class="lg:col-span-2 space-y-6">
            {{-- Summary --}}
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-lg font-medium text-gray-900 mb-4">Résumé</h2>
                
                <dl class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Action</dt>
                        <dd class="mt-1">
                            @php
                                $actionColors = [
                                    'create' => 'bg-green-100 text-green-800',
                                    'update' => 'bg-blue-100 text-blue-800',
                                    'delete' => 'bg-red-100 text-red-800',
                                    'login' => 'bg-purple-100 text-purple-800',
                                    'logout' => 'bg-gray-100 text-gray-800',
                                    'export' => 'bg-yellow-100 text-yellow-800',
                                    'refund' => 'bg-orange-100 text-orange-800',
                                    'status_change' => 'bg-indigo-100 text-indigo-800',
                                ];
                                $color = $actionColors[$log->action] ?? 'bg-gray-100 text-gray-800';
                            @endphp
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $color }}">
                                {{ ucfirst($log->action) }}
                            </span>
                        </dd>
                    </div>
                    
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Administrateur</dt>
                        <dd class="mt-1 text-sm text-gray-900">
                            <div class="flex items-center gap-2">
                                <div class="w-8 h-8 bg-primary-100 rounded-full flex items-center justify-center text-primary-700 font-medium text-sm">
                                    {{ substr($log->admin_name, 0, 2) }}
                                </div>
                                <span>{{ $log->admin_name }}</span>
                            </div>
                        </dd>
                    </div>
                    
                    <div class="md:col-span-2">
                        <dt class="text-sm font-medium text-gray-500">Description</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $log->description }}</dd>
                    </div>
                    
                    @if($log->subject_type)
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Type d'objet</dt>
                            <dd class="mt-1 text-sm text-gray-900 font-mono">
                                {{ class_basename($log->subject_type) }}
                            </dd>
                        </div>
                        
                        <div>
                            <dt class="text-sm font-medium text-gray-500">ID de l'objet</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $log->subject_id }}</dd>
                        </div>
                    @endif
                </dl>
            </div>

            {{-- Properties --}}
            @if($log->properties)
                <div class="bg-white rounded-lg shadow p-6">
                    <h2 class="text-lg font-medium text-gray-900 mb-4">Données</h2>
                    
                    @if(isset($log->properties['changes']))
                        <div class="space-y-4">
                            <h3 class="text-sm font-medium text-gray-700">Modifications</h3>
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Champ</th>
                                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Ancienne valeur</th>
                                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Nouvelle valeur</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-200">
                                        @foreach($log->properties['changes'] as $field => $change)
                                            <tr>
                                                <td class="px-4 py-2 text-sm font-medium text-gray-900">{{ $field }}</td>
                                                <td class="px-4 py-2 text-sm text-red-600">
                                                    <code class="bg-red-50 px-1 rounded">{{ is_array($change['old']) ? json_encode($change['old']) : $change['old'] }}</code>
                                                </td>
                                                <td class="px-4 py-2 text-sm text-green-600">
                                                    <code class="bg-green-50 px-1 rounded">{{ is_array($change['new']) ? json_encode($change['new']) : $change['new'] }}</code>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @else
                        <pre class="bg-gray-50 rounded-lg p-4 text-sm text-gray-700 overflow-x-auto"><code>{{ json_encode($log->properties, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</code></pre>
                    @endif
                </div>
            @endif
        </div>

        {{-- Metadata --}}
        <div class="space-y-6">
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-lg font-medium text-gray-900 mb-4">Métadonnées</h2>
                
                <dl class="space-y-4">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Date/Heure</dt>
                        <dd class="mt-1 text-sm text-gray-900">
                            {{ $log->created_at->format('d/m/Y H:i:s') }}
                        </dd>
                    </div>
                    
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Adresse IP</dt>
                        <dd class="mt-1 text-sm text-gray-900 font-mono">
                            {{ $log->ip_address ?? 'N/A' }}
                        </dd>
                    </div>
                    
                    <div>
                        <dt class="text-sm font-medium text-gray-500">User Agent</dt>
                        <dd class="mt-1 text-xs text-gray-600 break-all">
                            {{ $log->user_agent ?? 'N/A' }}
                        </dd>
                    </div>
                </dl>
            </div>

            {{-- Quick Actions --}}
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-lg font-medium text-gray-900 mb-4">Actions</h2>
                
                <div class="space-y-2">
                    @if($log->subject_type && $log->subject_id)
                        @php
                            $route = null;
                            $modelClass = class_basename($log->subject_type);
                            if ($modelClass === 'Tour') {
                                $route = route('admin.tours.edit', $log->subject_id);
                            } elseif ($modelClass === 'Booking') {
                                $route = route('admin.bookings.show', $log->subject_id);
                            } elseif ($modelClass === 'Review') {
                                $route = route('admin.reviews.edit', $log->subject_id);
                            }
                        @endphp
                        @if($route)
                            <a href="{{ $route }}" class="btn-outline w-full text-center">
                                Voir {{ $modelClass }}
                            </a>
                        @endif
                    @endif

                    @if($log->admin_id)
                        <a href="{{ route('admin.audit-logs.index', ['admin_id' => $log->admin_id]) }}" class="btn-outline w-full text-center">
                            Autres actions de cet admin
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
