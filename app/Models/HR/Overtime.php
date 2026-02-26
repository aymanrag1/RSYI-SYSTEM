<?php

namespace App\Models\HR;

use Illuminate\Database\Eloquent\Model;

class Overtime extends Model
{
    protected $table      = 'wp_rsyi_hr_overtime';
    public    $timestamps = false;

    protected $fillable = [
        'employee_id', 'overtime_date', 'hours',
        'reason', 'status', 'approved_by', 'created_at',
    ];

    protected $casts = [
        'overtime_date' => 'date',
        'created_at'    => 'datetime',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }
}
