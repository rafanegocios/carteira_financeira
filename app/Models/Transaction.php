<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'type',
        'user_id',
        'recipient_id',
        'amount',
        'description',
        'reversed_by',
        'original_transaction',
        'is_reversed',
        'metadata',
    ];

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'amount' => 'decimal:2',
        'is_reversed' => 'boolean',
        'metadata' => 'json',
    ];

    /**
     * Get the user who made the transaction.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the recipient of the transaction.
     */
    public function recipient()
    {
        return $this->belongsTo(User::class, 'recipient_id');
    }

    /**
     * Get the transaction that reversed this one.
     */
    public function reversedByTransaction()
    {
        return $this->belongsTo(Transaction::class, 'reversed_by');
    }

    /**
     * Get the original transaction that this reversal relates to.
     */
    public function originalTransaction()
    {
        return $this->belongsTo(Transaction::class, 'original_transaction');
    }

    /**
     * Get reversals for this transaction.
     */
    public function reversals()
    {
        return $this->hasMany(Transaction::class, 'original_transaction');
    }

    /**
     * Scope query to deposits only.
     */
    public function scopeDeposits($query)
    {
        return $query->where('type', 'deposit');
    }

    /**
     * Scope query to transfers only.
     */
    public function scopeTransfers($query)
    {
        return $query->where('type', 'transfer');
    }

    /**
     * Scope query to non-reversed transactions.
     */
    public function scopeActive($query)
    {
        return $query->where('is_reversed', false);
    }
}
