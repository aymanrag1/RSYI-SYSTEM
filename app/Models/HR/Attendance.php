<?php

namespace App\Models\HR;

use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    protected $table      = 'wp_rsyi_hr_attendance';
    public    $timestamps = false;

    protected $fillable = [
        'employee_id', 'date', 'check_in', 'check_out',
        'status', 'notes', 'recorded_by',
    ];

    protected $casts = [
        'date'      => 'date',
        'check_in'  => 'datetime',
        'check_out' => 'datetime',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'present' => 'حاضر',
            'absent'  => 'غائب',
            'late'    => 'متأخر',
            'leave'   => 'إجازة',
            default   => $this->status,
        };
    }

    public function getStatusClassAttribute(): string
    {
        return match ($this->status) {
            'present' => 'status-active',
            'absent'  => 'status-rejected',
            'late'    => 'status-pending',
            default   => 'status-inactive',
        };
    }
}
