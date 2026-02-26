<?php

namespace App\Models\HR;

use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    protected $table      = 'wp_rsyi_hr_departments';
    public    $timestamps = false;

    protected $fillable = ['name', 'name_en', 'head_employee_id', 'status', 'description'];

    public function employees()
    {
        return $this->hasMany(Employee::class, 'dept_id');
    }

    public function head()
    {
        return $this->belongsTo(Employee::class, 'head_employee_id');
    }

    public function jobTitles()
    {
        return $this->hasMany(JobTitle::class, 'dept_id');
    }

    public function getEmployeesCountAttribute(): int
    {
        return $this->employees()->where('status', 'active')->count();
    }
}
