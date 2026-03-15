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
        Schema::create('consumable_type_printer', function (Blueprint $table) {
            $table->id();
            $table->foreignId('printer_id')->constrained()->onDelete('cascade');
            $table->foreignId('consumable_type_id')->constrained()->onDelete('cascade');

            // Prevent duplicate links between the exact same printer and type
            $table->unique(['printer_id', 'consumable_type_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('consumable_type_printer');
    }
};
