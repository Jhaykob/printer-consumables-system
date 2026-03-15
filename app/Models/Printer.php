<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Printer extends Model
{
    use HasFactory;

    protected $fillable = [
        'printer_location_id',
        'name',
        'serial_number',
        'ip_address'
    ];

    // A printer belongs to one location
    public function location()
    {
        return $this->belongsTo(PrinterLocation::class, 'printer_location_id');
    }
}
