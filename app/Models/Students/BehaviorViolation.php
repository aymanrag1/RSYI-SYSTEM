<?php

namespace App\Models\Students;

use Illuminate\Database\Eloquent\Model;

class BehaviorViolation extends Model
{
    protected $table      = 'wp_rsyi_sa_violations';
    public    $timestamps = false;

    protected $fillable = [
        'student_id', 'violation_type', 'points', 'violation_date',
        'description', 'penalty', 'recorded_by', 'created_at',
    ];

    protected $casts = [
        'violation_date' => 'date',
        'created_at'     => 'datetime',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id');
    }
}
