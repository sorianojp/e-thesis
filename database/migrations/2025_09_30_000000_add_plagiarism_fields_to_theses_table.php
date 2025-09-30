<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('theses', function (Blueprint $table) {
            $table->string('plagiarism_scan_id')->nullable()->after('endorsement_pdf_path');
            $table->string('plagiarism_status')->default('pending')->after('plagiarism_scan_id');
            $table->decimal('plagiarism_score', 5, 2)->nullable()->after('plagiarism_status');
            $table->json('plagiarism_report')->nullable()->after('plagiarism_score');
            $table->timestamp('plagiarism_checked_at')->nullable()->after('plagiarism_report');

            $table->index('plagiarism_scan_id');
            $table->index('plagiarism_status');
        });
    }

    public function down(): void
    {
        Schema::table('theses', function (Blueprint $table) {
            $table->dropIndex(['plagiarism_scan_id']);
            $table->dropIndex(['plagiarism_status']);

            $table->dropColumn([
                'plagiarism_scan_id',
                'plagiarism_status',
                'plagiarism_score',
                'plagiarism_report',
                'plagiarism_checked_at',
            ]);
        });
    }
};
