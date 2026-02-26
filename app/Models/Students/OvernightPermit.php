<?php

namespace App\Models\Students;

use Illuminate\Database\Eloquent\Model;

class OvernightPermit extends Model
{
    protected $table      = 'wp_rsyi_sa_overnight_permits';
    public    $timestamps = false;

    protected $fillable = [
        'student_id', 'start_date', 'end_date', 'destination',
        'guardian_name', 'guardian_phone', 'purpose',
        'status', 'step', 'approved_by', 'created_at',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date'   => 'date',
        'created_at' => 'datetime',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id');
    }
}
