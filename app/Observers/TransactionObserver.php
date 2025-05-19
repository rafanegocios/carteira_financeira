<?php

namespace App\Observers;

use App\Models\Transaction;
use Illuminate\Support\Facades\Log;

class TransactionObserver
{
    /**
     * Handle the Transaction "created" event.
     */
    public function created(Transaction $transaction): void
    {
        $logData = $this->prepareLogData($transaction, 'created');
        Log::channel('transactions')->info('Transaction created', $logData);
    }

    /**
     * Handle the Transaction "updated" event.
     */
    public function updated(Transaction $transaction): void
    {
        $changes = $transaction->getChanges();

        // Log specific information if the transaction was reversed
        if (isset($changes['is_reversed']) && $changes['is_reversed']) {
            $logData = $this->prepareLogData($transaction, 'reversed');
            Log::channel('transactions')->info('Transaction reversed', $logData);
        } else {
            $logData = $this->prepareLogData($transaction, 'updated');
            Log::channel('transactions')->info('Transaction updated', $logData);
        }
    }

    /**
     * Prepare log data for the transaction.
     *
     * @param Transaction $transaction
     * @param string $action
     * @return array
     */
    private function prepareLogData(Transaction $transaction, string $action): array
    {
        $data = [
            'transaction_id' => $transaction->id,
            'type' => $transaction->type,
            'user_id' => $transaction->user_id,
            'amount' => $transaction->amount,
            'action' => $action,
            'timestamp' => now()->toIso8601String(),
        ];

        if ($transaction->recipient_id) {
            $data['recipient_id'] = $transaction->recipient_id;
        }

        if ($transaction->is_reversed) {
            $data['is_reversed'] = true;

            if ($transaction->reversed_by) {
                $data['reversed_by'] = $transaction->reversed_by;
            }
        }

        if ($transaction->original_transaction) {
            $data['original_transaction'] = $transaction->original_transaction;
        }

        return $data;
    }
}
