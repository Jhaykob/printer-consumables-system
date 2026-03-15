<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inventory extends Model
{
    use HasFactory;

    protected $fillable = ['consumable_type_id', 'color_id', 'stock_level', 'threshold'];

    public function consumableType()
    {
        return $this->belongsTo(ConsumableType::class);
    }

    public function color()
    {
        return $this->belongsTo(Color::class);
    }
}
