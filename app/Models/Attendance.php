<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    protected $fillable = [
        'employee_id',
        'location_id',
        'date',
        'clock_in',
        'clock_in_latitude',
        'clock_in_longitude',
        'clock_out',
        'clock_out_latitude',
        'clock_out_longitude',
        'clock_in_photo',
        'clock_out_photo',
        'clock_in_distance',
        'clock_out_distance',
        'status',
        'notes',
    ];

    protected $casts = [
        'date' => 'date',
        'clock_in_latitude' => 'decimal:8',
        'clock_in_longitude' => 'decimal:8',
        'clock_out_latitude' => 'decimal:8',
        'clock_out_longitude' => 'decimal:8',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function location()
    {
        return $this->belongsTo(Location::class);
    }
}
