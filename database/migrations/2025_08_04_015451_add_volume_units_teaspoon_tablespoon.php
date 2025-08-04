<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        
        $mlUnits = DB::table('units')->whereRaw('LOWER(abbreviation) = ?', ['ml'])->get();

        if ($mlUnits->count() > 1) {
          
            $keeper = $mlUnits->firstWhere('base_unit_id', null) 
                ?? $mlUnits->sortBy('created_at')->first();

            $keeperId = $keeper->id;

            foreach ($mlUnits as $unit) {
                if ($unit->id === $keeperId) {
                    continue;
                }
                
                DB::table('units')->where('base_unit_id', $unit->id)->update([
                    'base_unit_id' => $keeperId,
                ]);
                
                DB::table('units')->where('id', $unit->id)->delete();
            }
        }

      =
        $ml = DB::table('units')->where('abbreviation', 'ml')->first();
        if (! $ml) {
            $mlId = DB::table('units')->insertGetId([
                'name' => 'Milliliter',
                'abbreviation' => 'ml',
                'type' => 'volume',
                'conversion_factor_to_base' => 1.0,
                'base_unit_id' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } else {
            $mlId = $ml->id;
            
            if (! is_null($ml->base_unit_id)) {
                DB::table('units')->where('id', $mlId)->update(['base_unit_id' => null]);
            }
        }

        if (! DB::table('units')->where('abbreviation', 'tsp')->exists()) {
            DB::table('units')->insert([
                'name' => 'Teaspoon',
                'abbreviation' => 'tsp',
                'ty
