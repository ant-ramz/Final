<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Http\Services\UnitConverter;

class InventoryItem extends Model
{
    protected $fillable = [
        'name',
        'default_unit_id',
        'quantity_in_base',
    ];

    protected $casts = [
        'quantity_in_base' => 'float',
    ];

    public function defaultUnit()
    {
        return $this->belongsTo(Unit::class, 'default_unit_id');
    }

    public function productComponents()
    {
        return $this->hasMany(ProductComponent::class, 'inventory_item_id');
    }

    public function getDisplayQuantityAttribute(): float
    {
        if (!$this->defaultUnit) {
            return 0.0;
        }

        $converter = new UnitConverter();
        return $converter->fromBase($this->quantity_in_base, $this->defaultUnit);
    }
}
