<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('thesis_title_members', function (Blueprint $table) {
            $table->id();
            $table->foreignId('thesis_title_id')
                ->constrained()
                ->cascadeOnDelete();
            $table->foreignId('student_id')
                ->constrained('users')
                ->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['thesis_title_id', 'student_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('thesis_title_members');
    }
};
