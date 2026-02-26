<?php

namespace App\Models\Warehouse;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class PurchaseRequest extends Model
{
    protected $table      = 'wp_rsyi_wh_purchase_requests';
    public    $timestamps = false;

    protected $fillable = [
        'request_number', 'dept_id', 'requested_by',
        'status', 'notes', 'estimated_cost', 'created_at', 'updated_at',
    ];

    protected $casts = [
        'estimated_cost' => 'decimal:2',
        'created_at'     => 'datetime',
        'updated_at'     => 'datetime',
    ];

    public function items()
    {
        return $this->hasMany(PurchaseRequestItem::class, 'request_id');
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
            'ordered'  => 'تم الطلب',
            'received' => 'تم الاستلام',
            'rejected' => 'مرفوض',
            default    => $this->status,
        };
    }

    public function getStatusClassAttribute(): string
    {
        return match ($this->status) {
            'pending'  => 'badge-warning',
            'approved' => 'badge-primary',
            'ordered'  => 'badge-info',
            'received' => 'badge-success',
            'rejected' => 'badge-danger',
            default    => 'badge-secondary',
        };
    }
}
