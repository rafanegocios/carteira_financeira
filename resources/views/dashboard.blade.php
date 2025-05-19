@extends('layouts.app')

@section('content')
<div class="bg-white rounded-lg shadow-md overflow-hidden">
    <div class="p-6">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold text-gray-800">Seu Dashboard</h2>
            <div class="text-right">
                <p class="text-sm text-gray-600">Saldo atual</p>
                <p class="text-2xl font-bold text-indigo-600">R$ {{ number_format($user->balance, 2, ',', '.') }}</p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <a href="{{ route('deposit.form') }}" class="bg-indigo-50 hover:bg-indigo-100 p-6 rounded-lg flex items-center justify-center">
                <div class="text-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-indigo-600 mx-auto mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m6-6H6" />
                    </svg>
                    <h3 class="text-lg font-semibold text-gray-800">Depositar</h3>
                    <p class="text-sm text-gray-600">Adicionar fundos à sua carteira</p>
                </div>
            </a>
            <a href="{{ route('transfer.form') }}" class="bg-indigo-50 hover:bg-indigo-100 p-6 rounded-lg flex items-center justify-center">
                <div class="text-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-indigo-600 mx-auto mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
                    </svg>
                    <h3 class="text-lg font-semibold text-gray-800">Transferir</h3>
                    <p class="text-sm text-gray-600">Enviar fundos para outro usuário</p>
                </div>
            </a>
        </div>

        <div class="mt-8">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold text-gray-800">Transações Recentes</h3>
                <a href="{{ route('transactions.history') }}" class="text-sm text-indigo-600 hover:text-indigo-800">Ver todas</a>
            </div>

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
                                            @if($transaction->user_id == $user->id)
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
                                        @if(($transaction->type == 'transfer' && $transaction->user_id == $user->id) || $transaction->type == 'reversal')
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
</div>
@endsection
