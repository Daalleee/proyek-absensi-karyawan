<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    protected $fillable = [
        'user_id',
        'employee_id',
        'phone',
        'department',
        'position',
        'join_date',
        'photo',
        'nik',
        'gender',
        'birth_date',
        'birth_place',
        'address',
        'emergency_contact',
        'emergency_phone',
        'assigned_location_id',
    ];

    protected $casts = [
        'join_date' => 'date',
        'birth_date' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    public function assignedLocation()
    {
        return $this->belongsTo(Location::class, 'assigned_location_id');
    }

    public function leaves()
    {
        return $this->hasMany(Leave::class);
    }

    public function getPhotoUrlAttribute()
    {
        if ($this->photo) {
            return asset('storage/' . $this->photo);
        }
        return asset('images/default-avatar.png');
    }
}
