<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Unit extends Model
{
    protected $fillable = [
        'name',
        'abbreviation',
        'type',
        'conversion_factor_to_base',
        'base_unit_id',
    ];

    protected $casts = [
        'conversion_factor_to_base' => 'float',
    ];

    public function baseUnit()
    {
        return $this->belongsTo(self::class, 'base_unit_id');
    }

    public function derivedUnits(): HasMany
    {
        return $this->hasMany(self::class, 'base_unit_id');
    }
}
