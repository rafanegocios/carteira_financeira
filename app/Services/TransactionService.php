<?php

namespace App\Services;

use App\Models\Transaction;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Exception;

class TransactionService
{
    /**
     * Create a deposit transaction.
     *
     * @param User $user
     * @param float $amount
     * @param string|null $description
     * @return Transaction
     * @throws Exception
     */
    public function deposit(User $user, float $amount, ?string $description = null): Transaction
    {
        if ($amount <= 0) {
            throw new Exception('O valor do depósito deve ser maior que zero');
        }

        try {
            DB::beginTransaction();

            // Criar transação de depósito
            $transaction = Transaction::create([
                'type' => 'deposit',
                'user_id' => $user->id,
                'amount' => $amount,
                'description' => $description ?? 'Depósito',
            ]);

            // Atualizar saldo do usuário
            $user->addBalance($amount);

            DB::commit();
            return $transaction;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Create a transfer transaction.
     *
     * @param User $sender
     * @param User $recipient
     * @param float $amount
     * @param string|null $description
     * @return Transaction
     * @throws Exception
     */
    public function transfer(User $sender, User $recipient, float $amount, ?string $description = null): Transaction
    {
        if ($amount <= 0) {
            throw new Exception('O valor da transferência deve ser maior que zero');
        }

        if ($sender->id === $recipient->id) {
            throw new Exception('Você não pode transferir para você mesmo');
        }

        if (!$sender->hasSufficientBalance($amount)) {
            throw new Exception('Saldo insuficiente para realizar esta transferência');
        }

        try {
            DB::beginTransaction();

            // Criar transação de transferência
            $transaction = Transaction::create([
                'type' => 'transfer',
                'user_id' => $sender->id,
                'recipient_id' => $recipient->id,
                'amount' => $amount,
                'description' => $description ?? 'Transferência',
            ]);

            // Atualizar saldos
            $sender->deductBalance($amount);
            $recipient->addBalance($amount);

            DB::commit();
            return $transaction;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Reverse a transaction.
     *
     * @param Transaction $transaction
     * @param string|null $reason
     * @return Transaction
     * @throws Exception
     */
    public function reverseTransaction(Transaction $transaction, ?string $reason = null): Transaction
    {
        if ($transaction->is_reversed) {
            throw new Exception('Esta transação já foi revertida');
        }

        try {
            DB::beginTransaction();

            $user = $transaction->user;
            $recipient = $transaction->recipient;
            $amount = $transaction->amount;

            // Criar transação de reversão
            $reversal = Transaction::create([
                'type' => 'reversal',
                'user_id' => $user->id,
                'recipient_id' => $recipient?->id,
                'amount' => $amount,
                'description' => $reason ?? 'Reversão de transação #' . $transaction->id,
                'original_transaction' => $transaction->id,
                'metadata' => [
                    'original_transaction_type' => $transaction->type,
                    'reason' => $reason,
                    'reversed_at' => now(),
                ],
            ]);

            // Marcar transação original como revertida
            $transaction->update([
                'is_reversed' => true,
                'reversed_by' => $reversal->id,
            ]);

            // Restaurar saldos
            if ($transaction->type === 'deposit') {
                // Se foi um depósito, reduzir saldo do usuário
                $user->deductBalance($amount);
            } elseif ($transaction->type === 'transfer') {
                // Se foi uma transferência, devolver ao remetente e retirar do destinatário
                $user->addBalance($amount);
                if ($recipient) {
                    $recipient->deductBalance($amount);
                }
            }

            DB::commit();
            return $reversal;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Get user transactions.
     *
     * @param User $user
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getUserTransactions(User $user)
    {
        return Transaction::where(function ($query) use ($user) {
            $query->where('user_id', $user->id)
                  ->orWhere('recipient_id', $user->id);
        })
        ->with(['user', 'recipient'])
        ->orderBy('created_at', 'desc')
        ->get();
    }
}
