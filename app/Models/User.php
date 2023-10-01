<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Models\Transactions;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
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
    ];

    public function getBalance()
    {
        // Calculate the user's current balance by summing up their credit and debit transactions
        $creditAmount = $this->transactions()
            ->where('transaction_type', 'credit')
            ->sum('amount');

        $debitAmount = $this->transactions()
            ->where('transaction_type', 'debit')
            ->sum('amount');

        return $creditAmount - $debitAmount;
    }

    public function transactions()
    {
        return $this->hasMany(Transactions::class);
    }
}
