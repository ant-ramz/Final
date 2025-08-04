<?php

namespace App\Http\Services;

use App\Models\Unit;
use InvalidArgumentException;

class UnitConverter
{
    
    public function toBase(float|int|string $amount, Unit $unit): float
    {
        return $this->normalize($amount, $unit);
    }

  
    public function normalize(float|int|string $amount, Unit $unit): float
    {
        $baseFactor = $this->getBaseConversionFactor($unit);
        return (float)$amount * $baseFactor;
    }

    
    public function fromBase(float|int|string $baseAmount, Unit $unit): float
    {
        $baseFactor = $this->getBaseConversionFactor($unit);
        if ($baseFactor == 0) {
            throw new InvalidArgumentException("Unit conversion factor is zero for {$unit->abbreviation}");
        }
        return (float)$baseAmount / $baseFactor;
    }

  
    protected function getBaseConversionFactor(Unit $unit): float
    {
        
        if (is_null($unit->base_unit_id)) {
            return (float)$unit->conversion_factor_to_base;
        }

        $factor = (float)$unit->conversion_factor_to_base;
        $base = $unit->baseUnit;
        while ($base) {
            if (!is_null($base->conversion_factor_to_base) && $base->base_unit_id !== null) {
                $factor *= (float)$base->conversion_factor_to_base;
                $base = $base->baseUnit;
            } else {
                break;
            }
        }
        return $factor;
    }
}
