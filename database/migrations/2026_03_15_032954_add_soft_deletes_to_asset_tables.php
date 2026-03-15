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
        Schema::table('departments', function (Blueprint $table) {
            $table->softDeletes();
        });
        Schema::table('printer_locations', function (Blueprint $table) {
            $table->softDeletes();
        });
        Schema::table('printers', function (Blueprint $table) {
            $table->softDeletes();
        });
        Schema::table('inventories', function (Blueprint $table) {
            $table->softDeletes();
        });
        Schema::table('consumable_types', function (Blueprint $table) {
            $table->softDeletes();
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('departments', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
        Schema::table('printer_locations', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
        Schema::table('printers', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
        Schema::table('inventories', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
        Schema::table('consumable_types', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
    }
};
