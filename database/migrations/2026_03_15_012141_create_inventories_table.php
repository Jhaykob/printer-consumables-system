<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('inventories', function (Blueprint $table) {
            $table->id();

            $table->foreignId('consumable_type_id')->constrained()->onDelete('restrict');
            // color_id is nullable because items like Drums don't have colors
            $table->foreignId('color_id')->nullable()->constrained()->onDelete('restrict');

            $table->integer('stock_level')->default(0);
            $table->integer('threshold')->default(5);

            $table->timestamps();

            // Ensure we don't accidentally create duplicates of the exact same type and color
            $table->unique(['consumable_type_id', 'color_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventories');
    }
};
