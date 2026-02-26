<?php

namespace App\Models\Warehouse;

use Illuminate\Database\Eloquent\Model;

class WithdrawalOrder extends Model
{
    protected $table      = 'wp_rsyi_wh_withdrawal_orders';
    public    $timestamps = false;

    protected $fillable = [
        'order_number', 'dept_id', 'purpose',
        'recorded_by', 'notes', 'created_at',
    ];

    protected $casts = ['created_at' => 'datetime'];

    public function items()
    {
        return $this->hasMany(WithdrawalOrderItem::class, 'order_id');
    }
}
