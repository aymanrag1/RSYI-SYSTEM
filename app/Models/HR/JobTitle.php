<?php

namespace App\Models\HR;

use Illuminate\Database\Eloquent\Model;

class JobTitle extends Model
{
    protected $table      = 'wp_rsyi_hr_job_titles';
    public    $timestamps = false;

    protected $fillable = ['name', 'name_en', 'dept_id', 'description'];

    public function department()
    {
        return $this->belongsTo(Department::class, 'dept_id');
    }

    public function employees()
    {
        return $this->hasMany(Employee::class, 'job_title_id');
    }
}
