<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('units', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('abbreviation');
            $table->enum('type', ['mass', 'volume', 'count']);
            $table->decimal('conversion_factor_to_base', 20, 8);
            $table->unsignedBigInteger('base_unit_id')->nullable();
            $table->timestamps();

            $table->foreign('base_unit_id')->references('id')->on('units')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('units');
    }
};
