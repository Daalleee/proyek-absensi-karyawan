<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Employee extends Model
{
    protected $table = 'employees';
    
    protected $fillable = [
        'employee_code',
        'first_name',
        'last_name',
        'email',
        'phone',
        'position',
        'department',
        'face_image_path',
        'hire_date',
        'status',
        'role_id',
    ];
    
    protected $casts = [
        'hire_date' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
    
    /**
     * Get the role that belongs to this employee.
     */
    public function role(): BelongsTo
    {
        return $this->belongsTo(EmployeeRole::class, 'role_id');
    }
    
    /**
     * Get the user that belongs to this employee.
     */
    public function user(): HasOne
    {
        return $this->hasOne(User::class, 'employee_id');
    }
    
    /**
     * Get the attendances for this employee.
     */
    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class, 'employee_id');
    }
    
    /**
     * Get the work locations created by this employee.
     */
    public function workLocations(): HasMany
    {
        return $this->hasMany(WorkLocation::class, 'created_by');
    }
    
    /**
     * Get the full name attribute.
     */
    public function getFullNameAttribute(): string
    {
        return $this->first_name . ' ' . $this->last_name;
    }
}
