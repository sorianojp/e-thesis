<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('theses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('thesis_title_id')->nullable()->constrained('thesis_titles')->cascadeOnDelete();
            $table->string('chapter_label');
            $table->string('thesis_pdf_path');
            $table->enum('status', ['pending', 'approved', 'rejected', 'passed'])->default('pending');
            $table->unsignedInteger('plagiarism_score')->nullable();
            $table->json('plagiarism_report')->nullable();
            $table->timestamp('plagiarism_checked_at')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique(['thesis_title_id', 'chapter_label']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('theses');
    }
};
