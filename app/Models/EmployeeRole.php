<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EmployeeRole extends Model
{
    protected $table = 'employee_roles';
    
    protected $fillable = [
        'name',
        'description'
    ];
    
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
    
    /**
     * Get the employees for this role.
     */
    public function employees(): HasMany
    {
        return $this->hasMany(Employee::class, 'role_id');
    }
}
