<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ConsumableRequest extends Model
{
    protected $fillable = ['user_id', 'department_id', 'printer_location_id', 'printer_id', 'status', 'notes'];

    public function department()
    {
        return $this->belongsTo(Department::class)->withTrashed();
    }

    public function location()
    {
        return $this->belongsTo(PrinterLocation::class, 'printer_location_id')->withTrashed();
    }

    public function printer()
    {
        return $this->belongsTo(Printer::class)->withTrashed();
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(RequestItem::class);
    }
}
