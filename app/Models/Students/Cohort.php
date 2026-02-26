<?php

namespace App\Models\Students;

use Illuminate\Database\Eloquent\Model;

class Cohort extends Model
{
    protected $table      = 'wp_rsyi_sa_cohorts';
    public    $timestamps = false;

    protected $fillable = ['name', 'start_date', 'end_date', 'status', 'description'];

    protected $casts = [
        'start_date' => 'date',
        'end_date'   => 'date',
    ];

    public function students()
    {
        return $this->hasMany(Student::class, 'cohort_id');
    }

    public function getActiveStudentsCountAttribute(): int
    {
        return $this->students()->where('status', 'active')->count();
    }
}
