<?php

namespace App\Models\Students;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Student extends Model
{
    protected $table      = 'wp_rsyi_sa_students';
    public    $timestamps = false;

    protected $fillable = [
        'file_number', 'first_name', 'last_name', 'national_id',
        'birth_date', 'gender', 'phone', 'email', 'address',
        'cohort_id', 'enrollment_date', 'status', 'user_id',
        'created_at', 'updated_at',
    ];

    protected $casts = [
        'birth_date'       => 'date',
        'enrollment_date'  => 'date',
        'created_at'       => 'datetime',
    ];

    // ─── Relations ───────────────────────────────────────────────────────────

    public function cohort()
    {
        return $this->belongsTo(Cohort::class, 'cohort_id');
    }

    public function documents()
    {
        return $this->hasMany(Document::class, 'student_id');
    }

    public function exitPermits()
    {
        return $this->hasMany(ExitPermit::class, 'student_id');
    }

    public function overnightPermits()
    {
        return $this->hasMany(OvernightPermit::class, 'student_id');
    }

    public function violations()
    {
        return $this->hasMany(BehaviorViolation::class, 'student_id');
    }

    // ─── Scopes ──────────────────────────────────────────────────────────────

    public function scopeActive(Builder $q): Builder
    {
        return $q->where('status', 'active');
    }

    public function scopeByCohort(Builder $q, int $cohortId): Builder
    {
        return $q->where('cohort_id', $cohortId);
    }

    // ─── Accessors ───────────────────────────────────────────────────────────

    public function getFullNameAttribute(): string
    {
        return trim("{$this->first_name} {$this->last_name}");
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'active'    => 'نشط',
            'suspended' => 'موقوف',
            'expelled'  => 'مفصول',
            'graduated' => 'خريج',
            'withdrawn' => 'منسحب',
            default     => $this->status,
        };
    }

    public function getStatusClassAttribute(): string
    {
        return match ($this->status) {
            'active'    => 'status-active',
            'suspended' => 'status-pending',
            'expelled'  => 'status-rejected',
            default     => 'status-inactive',
        };
    }

    public function getPendingDocumentsCountAttribute(): int
    {
        return $this->documents()->where('status', 'pending')->count();
    }
}
