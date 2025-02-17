<?php

namespace Modules\Subscriptions\Models;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SubscriptionTransactions extends BaseModel
{
    use HasFactory;

    protected $table = 'subscriptions_transactions';

    protected $fillable = ['subscriptions_id', 'user_id', 'amount', 'payment_type', 'payment_status', 'other_transactions_details'];

    protected static function newFactory()
    {
        return \Modules\Subscriptions\Database\factories\SubscriptionTransactionsFactory::new();
    }
}
