<?php

namespace App\Models\Warehouse;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $table      = 'wp_rsyi_wh_categories';
    public    $timestamps = false;
    protected $fillable   = ['name', 'description'];

    public function products()
    {
        return $this->hasMany(Product::class, 'category_id');
    }
}
