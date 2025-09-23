<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('theses', function (Blueprint $table) {
            if (!Schema::hasColumn('theses', 'plagiarism_scan_id')) {
                $table->string('plagiarism_scan_id')->nullable()->after('status');
            }

            if (!Schema::hasColumn('theses', 'plagiarism_status')) {
                $table->string('plagiarism_status')->nullable()->after('plagiarism_scan_id');
            }

            if (!Schema::hasColumn('theses', 'plagiarism_score')) {
                $table->decimal('plagiarism_score', 5, 2)->nullable()->after('plagiarism_status');
            }

            if (!Schema::hasColumn('theses', 'thesis_hash')) {
                $table->string('thesis_hash', 64)->nullable()->after('plagiarism_score');
            }
        });
    }

    public function down(): void
    {
        Schema::table('theses', function (Blueprint $table) {
            if (Schema::hasColumn('theses', 'thesis_hash')) {
                $table->dropColumn('thesis_hash');
            }

            if (Schema::hasColumn('theses', 'plagiarism_score')) {
                $table->dropColumn('plagiarism_score');
            }

            if (Schema::hasColumn('theses', 'plagiarism_status')) {
                $table->dropColumn('plagiarism_status');
            }

            if (Schema::hasColumn('theses', 'plagiarism_scan_id')) {
                $table->dropColumn('plagiarism_scan_id');
            }
        });
    }
};
