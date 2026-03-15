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
        Schema::create('consumable_types', function (Blueprint $table) {
            $table->id();
            // Link to the Category
            $table->foreignId('category_id')->constrained()->onDelete('restrict');

            $table->string('name'); // e.g., "HP 30A"
            $table->string('description')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('consumable_types');
    }
};
