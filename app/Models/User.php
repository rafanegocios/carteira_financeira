<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'balance',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'balance' => 'decimal:2',
    ];

    public function transactions()
    {
        return $this->hasMany(Transaction::class, 'user_id');
    }


    public function receivedTransactions()
    {
        return $this->hasMany(Transaction::class, 'recipient_id');
    }

    /**
     * Check if user has sufficient balance for a transaction.
     *
     * @param float $amount
     * @return bool
     */
    public function hasSufficientBalance(float $amount): bool
    {
        return $this->balance >= $amount;
    }

    /**
     * Add balance to the user's account.
     *
     * @param float $amount
     * @return void
     */
    public function addBalance(float $amount): void
    {
        $this->balance += $amount;
        $this->save();
    }

    /**
     * Deduct balance from the user's account.
     *
     * @param float $amount
     * @return void
     */
    public function deductBalance(float $amount): void
    {
        $this->balance -= $amount;
        $this->save();
    }
}
