<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('thesis_titles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('course_id')->constrained()->cascadeOnDelete();
            $table->foreignId('adviser_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('title');
            $table->string('abstract_pdf_path')->nullable();
            $table->string('endorsement_pdf_path')->nullable();
            $table->string('verification_token', 64)->nullable();
            $table->string('panel_chairman')->nullable();
            $table->string('panelist_one')->nullable();
            $table->string('panelist_two')->nullable();
            $table->date('defense_date')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('thesis_titles');
    }
};
