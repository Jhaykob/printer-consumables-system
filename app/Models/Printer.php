<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Printer extends Model
{
    use SoftDeletes;
    use HasFactory;

    protected $fillable = [
        'department_id',
        'printer_location_id',
        'name',
        'serial_number',
        'ip_address'
    ];

    // A printer belongs to one location
    public function location()
    {
        return $this->belongsTo(PrinterLocation::class, 'printer_location_id')->withTrashed();
    }

    // A printer can use many consumable types (e.g., a Black toner, a Cyan toner, and a Drum)
    public function consumableTypes()
    {
        return $this->belongsToMany(ConsumableType::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class)->withTrashed();
    }
}
