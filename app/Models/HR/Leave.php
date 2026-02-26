<?php

namespace App\Models\HR;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Leave extends Model
{
    protected $table      = 'wp_rsyi_hr_leaves';
    public    $timestamps = false;

    protected $fillable = [
        'employee_id', 'leave_type', 'start_date', 'end_date',
        'days', 'reason', 'status', 'approved_by', 'approved_at',
        'rejection_reason', 'created_at',
    ];

    protected $casts = [
        'start_date'  => 'date',
        'end_date'    => 'date',
        'approved_at' => 'datetime',
        'created_at'  => 'datetime',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    public function scopePending(Builder $q): Builder
    {
        return $q->where('status', 'pending');
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

    public function getStatusClassAttribute(): string
    {
        return match ($this->status) {
            'pending'  => 'badge-warning',
            'approved' => 'badge-success',
            'rejected' => 'badge-danger',
            default    => 'badge-secondary',
        };
    }

    public function getTypeLabel(): string
    {
        return match ($this->leave_type) {
            'annual'    => 'إجازة سنوية',
            'sick'      => 'إجازة مرضية',
            'emergency' => 'إجازة طارئة',
            'unpaid'    => 'إجازة بدون راتب',
            default     => $this->leave_type,
        };
    }
}
