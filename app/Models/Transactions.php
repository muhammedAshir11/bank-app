<?php

namespace App\Models;
use App\Models\User;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transactions extends Model
{
    use HasFactory;
    protected $fillable = [
        'amount', 'user_id', 'transfer_to_id', 'transaction_type', 'details',
    ];

    public function secondPartyDetails (){
        return $this->belongsTo(User::class, 'transfer_to_id');
    }
}
