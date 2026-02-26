<?php

namespace App\Models\Warehouse;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Product extends Model
{
    protected $table      = 'wp_rsyi_wh_products';
    public    $timestamps = false;

    protected $fillable = [
        'code', 'name', 'name_en', 'category_id', 'unit',
        'current_qty', 'min_qty', 'status', 'description',
        'created_at', 'updated_at',
    ];

    protected $casts = [
        'current_qty' => 'decimal:2',
        'min_qty'     => 'decimal:2',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class, 'product_id');
    }

    // ─── Scopes ──────────────────────────────────────────────────────────────

    public function scopeLowStock(Builder $q): Builder
    {
        return $q->whereColumn('current_qty', '<=', 'min_qty');
    }

    public function scopeOutOfStock(Builder $q): Builder
    {
        return $q->where('current_qty', '<=', 0);
    }

    // ─── Accessors ───────────────────────────────────────────────────────────

    public function getStockStatusAttribute(): string
    {
        if ($this->current_qty <= 0) {
            return 'out';
        }
        if ($this->current_qty <= $this->min_qty) {
            return 'low';
        }
        return 'ok';
    }

    public function getStockStatusLabelAttribute(): string
    {
        return match ($this->stock_status) {
            'out' => 'نفد المخزون',
            'low' => 'منخفض',
            'ok'  => 'متوفر',
        };
    }

    public function getStockStatusClassAttribute(): string
    {
        return match ($this->stock_status) {
            'out' => 'badge-danger',
            'low' => 'badge-warning',
            'ok'  => 'badge-success',
        };
    }
}
