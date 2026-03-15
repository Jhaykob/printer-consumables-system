<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ConsumableRequest extends Model
{
    protected $fillable = ['user_id', 'status', 'notes'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(RequestItem::class);
    }
}
