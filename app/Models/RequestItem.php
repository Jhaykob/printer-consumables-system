<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RequestItem extends Model
{
    protected $fillable = [
        'consumable_request_id',
        'inventory_id',
        'quantity',
        'fulfilled_quantity', // <-- Add this
        'status',
        'rejection_reason',
        'recall_reason',
        'recall_action'
    ];

    public function consumableRequest()
    {
        return $this->belongsTo(ConsumableRequest::class);
    }

    public function inventory()
    {
        return $this->belongsTo(Inventory::class)->withTrashed();
    }
}
