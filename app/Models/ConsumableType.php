<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConsumableType extends Model
{
    use HasFactory;

    protected $fillable = ['category_id', 'name', 'description'];

    // A Consumable Type belongs to a Category
    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
