<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Customer extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'account_balance',
        'gender',
        'address',
        'card_id',
        'pin',
        'status',
        'minimum_deposit_amount'
        
    ];
// a customer has one card
    public function card(): HasOne
    {
        return $this->hasOne(Card::class, 'id', 'card_id');
    }

    // a customer has many transactions
    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class, 'customer_id', 'id');
    }
}
