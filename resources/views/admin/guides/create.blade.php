@extends('admin.layout')

@section('title', 'Nouveau guide')

@section('content')
    <div class="mb-6">
        <a href="{{ route('admin.guides.index') }}" class="text-indigo-600 hover:text-indigo-800 font-semibold">← Retour aux guides</a>
    </div>

    <div class="bg-white rounded-xl shadow-lg p-8 max-w-4xl">
        <h2 class="text-2xl font-bold text-gray-900 mb-6">Nouveau guide</h2>

        <form method="POST" action="{{ route('admin.guides.store') }}" enctype="multipart/form-data">
            @csrf
            @include('admin.guides.partials.form', ['guide' => null])
        </form>
    </div>
@endsection
