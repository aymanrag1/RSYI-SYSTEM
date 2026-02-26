<?php

namespace App\Models\Students;

use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    protected $table      = 'wp_rsyi_sa_documents';
    public    $timestamps = false;

    protected $fillable = [
        'student_id', 'doc_type', 'file_path', 'file_name',
        'status', 'rejection_reason', 'reviewed_by', 'reviewed_at', 'created_at',
    ];

    protected $casts = [
        'reviewed_at' => 'datetime',
        'created_at'  => 'datetime',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id');
    }

    public function getDocTypeLabelAttribute(): string
    {
        return match ($this->doc_type) {
            'national_id'  => 'بطاقة شخصية',
            'certificate'  => 'شهادة دراسية',
            'photo'        => 'صورة شخصية',
            'medical'      => 'كشف طبي',
            'birth_cert'   => 'شهادة ميلاد',
            default        => $this->doc_type,
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'pending'  => 'بانتظار المراجعة',
            'approved' => 'مقبول',
            'rejected' => 'مرفوض',
            default    => $this->status,
        };
    }
}
