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
            $table->unsignedInteger('plagiarism_score')->nullable()->after('status');
            $table->json('plagiarism_report')->nullable()->after('plagiarism_score');
            $table->timestamp('plagiarism_checked_at')->nullable()->after('plagiarism_report');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('theses', function (Blueprint $table) {
            $table->dropColumn(['plagiarism_score', 'plagiarism_report', 'plagiarism_checked_at']);
        });
    }
};
