@extends('layouts.app')

@section('content')
<div class="bg-white rounded-lg shadow-md overflow-hidden">
    <div class="p-6">
        <h2 class="text-2xl font-bold text-gray-800 mb-6">Depositar</h2>

        <form method="POST" action="{{ route('deposit.store') }}">
            @csrf

            <div class="mb-4">
                <label for="amount" class="block text-gray-700 text-sm font-bold mb-2">Valor (R$)</label>
                <input id="amount" type="number" step="0.01" min="0.01" class="appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('amount') border-red-500 @enderror" name="amount" value="{{ old('amount') }}" required>
                @error('amount')
                    <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="description" class="block text-gray-700 text-sm font-bold mb-2">Descrição (opcional)</label>
                <input id="description" type="text" class="appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('description') border-red-500 @enderror" name="description" value="{{ old('description') }}">
                @error('description')
                    <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-center justify-between">
                <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                    Confirmar Depósito
                </button>
                <a href="{{ route('dashboard') }}" class="inline-block align-baseline font-bold text-sm text-indigo-600 hover:text-indigo-800">
                    Cancelar
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
