<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WorkLocation extends Model
{
    protected $table = 'work_locations';
    
    protected $fillable = [
        'name',
        'description',
        'latitude',
        'longitude',
        'radius',
        'date',
        'start_time',
        'end_time',
        'status',
        'created_by',
    ];
    
    protected $casts = [
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'date' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
    
    /**
     * Get the employee that created this work location.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'created_by');
    }
    
    /**
     * Get the attendances for this work location.
     */
    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class, 'work_location_id');
    }
}
