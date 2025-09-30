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
        Schema::create('theses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('course_id')->constrained()->cascadeOnDelete();
            $table->foreignId('adviser_id')->nullable()->constrained('users')->nullOnDelete();
            $table->integer('version');
            $table->string('title');
            $table->string('adviser');
            $table->string('thesis_pdf_path'); // storage path
            $table->string('endorsement_pdf_path'); // storage path
            $table->enum('status', ['pending', 'approved', 'rejected', 'passed'])->default('pending');
            $table->decimal('grade', 5, 2)->nullable();
            $table->text('admin_remarks')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('theses');
    }
};
