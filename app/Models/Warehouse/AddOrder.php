<?php

namespace App\Models\Warehouse;

use Illuminate\Database\Eloquent\Model;

class AddOrder extends Model
{
    protected $table      = 'wp_rsyi_wh_add_orders';
    public    $timestamps = false;

    protected $fillable = [
        'order_number', 'supplier_id', 'recorded_by',
        'notes', 'created_at',
    ];

    protected $casts = ['created_at' => 'datetime'];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id');
    }

    public function items()
    {
        return $this->hasMany(AddOrderItem::class, 'order_id');
    }
}
