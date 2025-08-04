<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Unit;

class UnitSeeder extends Seeder
{
    public function run(): void
    {
        
        $gram = Unit::create([
            'name' => 'Gram',
            'abbreviation' => 'g',
            'type' => 'mass',
            'conversion_factor_to_base' => 1,
            'base_unit_id' => null,
        ]);

        Unit::create([
            'name' => 'Kilogram',
            'abbreviation' => 'kg',
            'type' => 'mass',
            'conversion_factor_to_base' => 1000,
            'base_unit_id' => $gram->id,
        ]);

        Unit::create([
            'name' => 'Ounce',
            'abbreviation' => 'oz',
            'type' => 'mass',
            'conversion_factor_to_base' => 28.3495,
            'base_unit_id' => $gram->id,
        ]);

        
        $milliliter = Unit::create([
            'name' => 'Milliliter',
            'abbreviation' => 'ml',
            'type' => 'volume',
            'conversion_factor_to_base' => 1,
            'base_unit_id' => null,
        ]);

        Unit::create([
            'name' => 'Liter',
            'abbreviation' => 'l',
            'type' => 'volume',
            'conversion_factor_to_base' => 1000,
            'base_unit_id' => $milliliter->id,
        ]);

        
        Unit::create([
            'name' => 'Piece',
            'abbreviation' => 'pc',
            'type' => 'count',
            'conversion_factor_to_base' => 1,
            'base_unit_id' => null,
        ]);
    }
}
