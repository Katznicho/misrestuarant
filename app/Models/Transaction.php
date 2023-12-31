<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'amount',
        'phone_number',
        'payment_mode',
        'description',
        'reference',
        'customer_id',
        'card_id',
        'branch_id',
        'user_id',
        'status',
        'order_tracking_id',
        'OrderNotificationType',
    ];


    // a transaction belongs to a customer
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    //a transaction belong to a branch
    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    // a transaction belongs to a user
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    
}
