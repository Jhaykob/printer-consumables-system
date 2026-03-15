<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PrinterLocation extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description'];

    // A location can have many printers
    public function printers()
    {
        return $this->hasMany(Printer::class);
    }
}
