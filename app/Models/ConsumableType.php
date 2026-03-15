<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ConsumableType extends Model
{
    use SoftDeletes;
    use HasFactory;

    protected $fillable = ['category_id', 'name', 'description'];

    // A Consumable Type belongs to a Category
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    // A consumable type can be used in many different printers
    public function printers()
    {
        return $this->belongsToMany(Printer::class);
    }
}
