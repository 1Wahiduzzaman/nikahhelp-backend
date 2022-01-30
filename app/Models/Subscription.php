<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Laravel\Cashier\Subscription as CashierSubscription;

class Subscription extends CashierSubscription
{
    public function plans(){
        return $this->belongsTo(Package::class, 'plan_id', 'id');
    }

    public function team(){
        return $this->belongsTo(Team::class, 'team_id', 'id');
    }
}
