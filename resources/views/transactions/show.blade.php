@extends('layouts.app')

@section('content')
<div class="bg-white rounded-lg shadow-md overflow-hidden">
    <div class="p-6">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold text-gray-800">Detalhes da Transação</h2>
            <a href="{{ url()->previous() }}" class="text-indigo-600 hover:text-indigo-800">
                Voltar
            </a>
        </div>

        <div class="bg-gray-50 rounded-lg p-4 mb-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <p class="text-sm text-gray-600">Identificador</p>
                    <p class="font-semibold">#{{ $transaction->id }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Data</p>
                    <p class="font-semibold">{{ $transaction->created_at->format('d/m/Y H:i:s') }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Tipo</p>
                    <p class="font-semibold">
                        @if($transaction->type == 'deposit')
                            <span class="text-green-600">Depósito</span>
                        @elseif($transaction->type == 'transfer')
                            @if($transaction->user_id == Auth::id())
                                <span class="text-red-600">Transferência Enviada</span>
                            @else
                                <span class="text-green-600">Transferência Recebida</span>
                            @endif
                        @elseif($transaction->type == 'reversal')
                            <span class="text-orange-600">Reversão</span>
                        @endif
                    </p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Valor</p>
                    <p class="font-semibold">
                        @if(($transaction->type == 'transfer' && $transaction->user_id == Auth::id()) || $transaction->type == 'reversal')
                            <span class="text-red-600">- R$ {{ number_format($transaction->amount, 2, ',', '.') }}</span>
                        @else
                            <span class="text-green-600">+ R$ {{ number_format($transaction->amount, 2, ',', '.') }}</span>
                        @endif
                    </p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Status</p>
                    <p class="font-semibold">
                        @if($transaction->is_reversed)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                Revertida
                            </span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                Concluída
                            </span>
                        @endif
                    </p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Descrição</p>
                    <p class="font-semibold">{{ $transaction->description }}</p>
                </div>
            </div>
        </div>

        @if($transaction->type == 'transfer')
            <div class="mb-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-2">Detalhes da Transferência</h3>
                <div class="bg-gray-50 rounded-lg p-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm text-gray-600">De</p>
                            <p class="font-semibold">{{ $transaction->user->name }}</p>
                            <p class="text-xs text-gray-500">{{ $transaction->user->email }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Para</p>
                            <p class="font-semibold">{{ $transaction->recipient->name }}</p>
                            <p class="text-xs text-gray-500">{{ $transaction->recipient->email }}</p>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        @if($transaction->type == 'reversal')
            <div class="mb-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-2">Detalhes da Reversão</h3>
                <div class="bg-gray-50 rounded-lg p-4">
                    <p class="text-sm text-gray-600">Transação Original</p>
                    <p class="font-semibold">
                        <a href="{{ route('transactions.show', $transaction->original_transaction) }}" class="text-indigo-600 hover:text-indigo-800">
                            #{{ $transaction->original_transaction }}
                        </a>
                    </p>

                    @if($transaction->metadata && isset($transaction->metadata['reason']))
                        <p class="text-sm text-gray-600 mt-2">Motivo da Reversão</p>
                        <p class="font-semibold">{{ $transaction->metadata['reason'] }}</p>
                    @endif
                </div>
            </div>
        @endif

        @if(!$transaction->is_reversed && !$transaction->type == 'reversal' && Auth::id() == $transaction->user_id)
            <div class="mt-8 border-t pt-4">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Ações</h3>

                <form method="POST" action="{{ route('transactions.reverse', $transaction->id) }}">
                    @csrf
                    <div class="mb-4">
                        <label for="reason" class="block text-gray-700 text-sm font-bold mb-2">Motivo da Reversão (opcional)</label>
                        <input id="reason" type="text" class="appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" name="reason">
                    </div>
                    <button type="submit" class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline" onclick="return confirm('Tem certeza que deseja reverter esta transação?')">
                        Reverter Transação
                    </button>
                </form>
            </div>
        @endif
    </div>
</div>
@endsection
