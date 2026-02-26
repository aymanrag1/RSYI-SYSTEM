<?php

namespace App\Models\HR;

use Illuminate\Database\Eloquent\Model;

class Violation extends Model
{
    protected $table      = 'wp_rsyi_hr_violations';
    public    $timestamps = false;

    protected $fillable = [
        'employee_id', 'violation_type', 'violation_date',
        'description', 'penalty', 'recorded_by', 'created_at',
    ];

    protected $casts = [
        'violation_date' => 'date',
        'created_at'     => 'datetime',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }
}
