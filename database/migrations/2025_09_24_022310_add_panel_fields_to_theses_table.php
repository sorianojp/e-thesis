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
        Schema::table('theses', function (Blueprint $table) {
            $table->string('panel_chairman')->nullable();
            $table->string('panelist_one')->nullable();
            $table->string('panelist_two')->nullable();
            $table->date('defense_date')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('theses', function (Blueprint $table) {
            $table->dropColumn([
                'panel_chairman',
                'panelist_one',
                'panelist_two',
                'defense_date',
            ]);
        });
    }
};
