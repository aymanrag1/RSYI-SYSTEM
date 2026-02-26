<?php

namespace App\Models\HR;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Employee extends Model
{
    protected $table      = 'wp_rsyi_hr_employees';
    public    $timestamps = false;

    protected $fillable = [
        'emp_number', 'user_id', 'first_name', 'last_name',
        'dept_id', 'job_title_id', 'hire_date', 'national_id',
        'phone', 'email', 'status', 'gender', 'birth_date',
        'address', 'created_at', 'updated_at',
    ];

    protected $casts = [
        'hire_date'  => 'date',
        'birth_date' => 'date',
    ];

    // ─── Relations ───────────────────────────────────────────────────────────

    public function department()
    {
        return $this->belongsTo(Department::class, 'dept_id');
    }

    public function jobTitle()
    {
        return $this->belongsTo(JobTitle::class, 'job_title_id');
    }

    public function leaves()
    {
        return $this->hasMany(Leave::class, 'employee_id');
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class, 'employee_id');
    }

    public function violations()
    {
        return $this->hasMany(Violation::class, 'employee_id');
    }

    public function overtimes()
    {
        return $this->hasMany(Overtime::class, 'employee_id');
    }

    // ─── Scopes ──────────────────────────────────────────────────────────────

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 'active');
    }

    public function scopeByDepartment(Builder $query, int $deptId): Builder
    {
        return $query->where('dept_id', $deptId);
    }

    // ─── Accessors ───────────────────────────────────────────────────────────

    public function getFullNameAttribute(): string
    {
        return trim("{$this->first_name} {$this->last_name}");
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'active'   => 'فعّال',
            'inactive' => 'غير فعّال',
            'resigned' => 'مستقيل',
            default    => $this->status,
        };
    }

    public function getStatusClassAttribute(): string
    {
        return match ($this->status) {
            'active'   => 'status-active',
            'inactive' => 'status-inactive',
            default    => 'status-inactive',
        };
    }
}
