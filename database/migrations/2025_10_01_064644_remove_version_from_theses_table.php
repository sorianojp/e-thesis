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
        if (Schema::hasColumn('theses', 'version')) {
            Schema::table('theses', function (Blueprint $table) {
                $table->dropColumn('version');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasColumn('theses', 'version')) {
            Schema::table('theses', function (Blueprint $table) {
                $table->integer('version')->default(1);
            });
        }
    }
};
