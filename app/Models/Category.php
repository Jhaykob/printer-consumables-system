<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'requires_color'];

    // We cast it to boolean so Laravel treats it as true/false, not 1/0
    protected $casts = [
        'requires_color' => 'boolean',
    ];

    // A Category can have many Consumable Types
    public function consumableTypes()
    {
        return $this->hasMany(ConsumableType::class);
    }
}
