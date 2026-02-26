<?php

namespace App\Models\Students;

use Illuminate\Database\Eloquent\Model;

class ExitPermit extends Model
{
    protected $table      = 'wp_rsyi_sa_exit_permits';
    public    $timestamps = false;

    protected $fillable = [
        'student_id', 'exit_date', 'return_date', 'destination',
        'purpose', 'status', 'step', 'approved_by', 'created_at',
    ];

    protected $casts = [
        'exit_date'   => 'datetime',
        'return_date' => 'datetime',
        'created_at'  => 'datetime',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id');
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'pending'  => 'معلق',
            'approved' => 'موافق عليه',
            'rejected' => 'مرفوض',
            default    => $this->status,
        };
    }
}
