@extends('layouts.app')

@section('content')
<div class="bg-white rounded-lg shadow-md overflow-hidden">
    <div class="p-6">
        <h2 class="text-2xl font-bold text-gray-800 mb-6">Histórico de Transações</h2>

        @if(count($transactions) > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full bg-white">
                    <thead>
                        <tr>
                            <th class="py-2 px-4 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipo</th>
                            <th class="py-2 px-4 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Descrição</th>
                            <th class="py-2 px-4 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Valor</th>
                            <th class="py-2 px-4 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Data</th>
                            <th class="py-2 px-4 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="py-2 px-4 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($transactions as $transaction)
                            <tr>
                                <td class="py-3 px-4 text-sm">
                                    @if($transaction->type == 'deposit')
                                        <span class="text-green-600">Depósito</span>
                                    @elseif($transaction->type == 'transfer')
                                        @if($transaction->user_id == Auth::id())
                                            <span class="text-red-600">Enviada</span>
                                        @else
                                            <span class="text-green-600">Recebida</span>
                                        @endif
                                    @elseif($transaction->type == 'reversal')
                                        <span class="text-orange-600">Reversão</span>
                                    @endif
                                </td>
                                <td class="py-3 px-4 text-sm">
                                    {{ $transaction->description }}
                                </td>
                                <td class="py-3 px-4 text-sm">
                                    @if(($transaction->type == 'transfer' && $transaction->user_id == Auth::id()) || $transaction->type == 'reversal')
                                        <span class="text-red-600">- R$ {{ number_format($transaction->amount, 2, ',', '.') }}</span>
                                    @else
                                        <span class="text-green-600">+ R$ {{ number_format($transaction->amount, 2, ',', '.') }}</span>
                                    @endif
                                </td>
                                <td class="py-3 px-4 text-sm text-gray-500">
                                    {{ $transaction->created_at->format('d/m/Y H:i') }}
                                </td>
                                <td class="py-3 px-4 text-sm">
                                    @if($transaction->is_reversed)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                            Revertida
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            Concluída
                                        </span>
                                    @endif
                                </td>
                                <td class="py-3 px-4 text-sm">
                                    <a href="{{ route('transactions.show', $transaction->id) }}" class="text-indigo-600 hover:text-indigo-900">Detalhes</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <p class="text-gray-500 text-center py-4">Nenhuma transação encontrada.</p>
        @endif
    </div>
</div>
@endsection
