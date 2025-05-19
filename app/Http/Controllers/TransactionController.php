<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Transaction;
use App\Services\TransactionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Exception;

class TransactionController extends Controller
{
    protected $transactionService;

    /**
     * Create a new controller instance.
     *
     * @param TransactionService $transactionService
     * @return void
     */
    public function __construct(TransactionService $transactionService)
    {
        $this->transactionService = $transactionService;
    }

    /**
     * Display user's dashboard with balance and recent transactions.
     *
     * @return \Illuminate\View\View
     */
    public function dashboard()
    {
        $user = Auth::user();
        $transactions = $this->transactionService->getUserTransactions($user);

        return view('dashboard', [
            'user' => $user,
            'transactions' => $transactions->take(5),
        ]);
    }

    /**
     * Show the form for making a deposit.
     *
     * @return \Illuminate\View\View
     */
    public function showDepositForm()
    {
        return view('transactions.deposit');
    }

    /**
     * Process a deposit request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function deposit(Request $request)
    {
        $validated = $request->validate([
            'amount' => ['required', 'numeric', 'min:0.01'],
            'description' => ['nullable', 'string', 'max:255'],
        ]);

        try {
            $user = Auth::user();
            $this->transactionService->deposit(
                $user,
                $validated['amount'],
                $validated['description'] ?? null
            );

            return redirect()->route('dashboard')
                ->with('success', 'Depósito realizado com sucesso!');
        } catch (Exception $e) {
            return back()->withInput()
                ->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Show the form for making a transfer.
     *
     * @return \Illuminate\View\View
     */
    public function showTransferForm()
    {
        return view('transactions.transfer');
    }

    /**
     * Process a transfer request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function transfer(Request $request)
    {
        $validated = $request->validate([
            'email' => ['required', 'email', 'exists:users,email'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'description' => ['nullable', 'string', 'max:255'],
        ]);

        try {
            $sender = Auth::user();
            $recipient = User::where('email', $validated['email'])->first();

            if (!$recipient) {
                return back()->withInput()
                    ->withErrors(['email' => 'Usuário destinatário não encontrado.']);
            }

            $this->transactionService->transfer(
                $sender,
                $recipient,
                $validated['amount'],
                $validated['description'] ?? null
            );

            return redirect()->route('dashboard')
                ->with('success', 'Transferência realizada com sucesso!');
        } catch (Exception $e) {
            return back()->withInput()
                ->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Show the transaction history.
     *
     * @return \Illuminate\View\View
     */
    public function history()
    {
        $user = Auth::user();
        $transactions = $this->transactionService->getUserTransactions($user);

        return view('transactions.history', [
            'transactions' => $transactions,
        ]);
    }

    /**
     * Show the transaction details.
     *
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function show($id)
    {
        $transaction = Transaction::with(['user', 'recipient', 'reversedByTransaction', 'originalTransaction'])
            ->findOrFail($id);

        // Check if user is authorized to view this transaction
        $user = Auth::user();
        if ($transaction->user_id !== $user->id && $transaction->recipient_id !== $user->id) {
            abort(403, 'Unauthorized action.');
        }

        return view('transactions.show', [
            'transaction' => $transaction
        ]);
    }

    /**
     * Reverse a transaction.
     *
     * @param  int  $id
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function reverse($id, Request $request)
    {
        $validated = $request->validate([
            'reason' => ['nullable', 'string', 'max:255'],
        ]);

        try {
            $transaction = Transaction::findOrFail($id);

            // Verificar se o usuário pode reverter esta transação
            $user = Auth::user();

            if ($transaction->type === 'transfer') {
                // Para transferências, apenas o remetente ou destinatário pode reverter
                if ($transaction->user_id !== $user->id && $transaction->recipient_id !== $user->id) {
                    throw new Exception('Você não está autorizado a reverter esta transação.');
                }
            } else {
                // Para depósitos, apenas o próprio usuário pode reverter
                if ($transaction->user_id !== $user->id) {
                    throw new Exception('Você não está autorizado a reverter esta transação.');
                }
            }

            $this->transactionService->reverseTransaction(
                $transaction,
                $validated['reason'] ?? null
            );

            return redirect()->route('transactions.show', $id)
                ->with('success', 'Transação revertida com sucesso!');
        } catch (Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }
}
