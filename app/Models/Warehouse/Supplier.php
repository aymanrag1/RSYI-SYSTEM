<?php

namespace App\Models\Warehouse;

use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    protected $table      = 'wp_rsyi_wh_suppliers';
    public    $timestamps = false;

    protected $fillable = [
        'name', 'contact_person', 'phone', 'email',
        'address', 'tax_number', 'status', 'notes',
    ];

    public function addOrders()
    {
        return $this->hasMany(AddOrder::class, 'supplier_id');
    }
}
