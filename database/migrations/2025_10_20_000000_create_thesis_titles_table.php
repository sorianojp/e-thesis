<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
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
            $table->text('abstract')->nullable();
            $table->string('abstract_pdf_path')->nullable();
            $table->string('endorsement_pdf_path')->nullable();
            $table->decimal('grade', 5, 2)->nullable();
            $table->string('verification_token', 64)->nullable();
            $table->string('panel_chairman')->nullable();
            $table->string('panelist_one')->nullable();
            $table->string('panelist_two')->nullable();
            $table->date('defense_date')->nullable();
            $table->timestamps();
        });

        Schema::table('theses', function (Blueprint $table) {
            $table->foreignId('thesis_title_id')
                ->nullable()
                ->after('id')
                ->constrained('thesis_titles')
                ->cascadeOnDelete();
        });

        DB::transaction(function () {
            $existing = DB::table('theses')->select('*')->orderBy('id')->get();

            foreach ($existing as $thesis) {
                $titleId = DB::table('thesis_titles')->insertGetId([
                    'user_id' => $thesis->user_id,
                    'course_id' => $thesis->course_id,
                    'adviser_id' => $thesis->adviser_id,
                    'title' => $thesis->title,
                    'abstract' => property_exists($thesis, 'abstract') ? $thesis->abstract : null,
                    'abstract_pdf_path' => $thesis->abstract_pdf_path ?? null,
                    'endorsement_pdf_path' => $thesis->endorsement_pdf_path ?? null,
                    'grade' => $thesis->grade ?? null,
                    'verification_token' => $thesis->verification_token ?? null,
                    'panel_chairman' => $thesis->panel_chairman ?? null,
                    'panelist_one' => $thesis->panelist_one ?? null,
                    'panelist_two' => $thesis->panelist_two ?? null,
                    'defense_date' => $thesis->defense_date ?? null,
                    'created_at' => $thesis->created_at,
                    'updated_at' => $thesis->updated_at,
                ]);

                DB::table('theses')
                    ->where('id', $thesis->id)
                    ->update(['thesis_title_id' => $titleId]);
            }
        });

        Schema::table('theses', function (Blueprint $table) {
            if (Schema::hasColumn('theses', 'user_id')) {
                $table->dropConstrainedForeignId('user_id');
            }
            if (Schema::hasColumn('theses', 'course_id')) {
                $table->dropConstrainedForeignId('course_id');
            }
            if (Schema::hasColumn('theses', 'adviser_id')) {
                $table->dropConstrainedForeignId('adviser_id');
            }
            if (Schema::hasColumn('theses', 'title')) {
                $table->dropColumn('title');
            }
            if (Schema::hasColumn('theses', 'adviser')) {
                $table->dropColumn('adviser');
            }
            if (Schema::hasColumn('theses', 'abstract')) {
                $table->dropColumn('abstract');
            }
            if (Schema::hasColumn('theses', 'abstract_pdf_path')) {
                $table->dropColumn('abstract_pdf_path');
            }
            if (Schema::hasColumn('theses', 'endorsement_pdf_path')) {
                $table->dropColumn('endorsement_pdf_path');
            }
            if (Schema::hasColumn('theses', 'grade')) {
                $table->dropColumn('grade');
            }
            if (Schema::hasColumn('theses', 'verification_token')) {
                $table->dropColumn('verification_token');
            }
            foreach (['panel_chairman', 'panelist_one', 'panelist_two'] as $panelColumn) {
                if (Schema::hasColumn('theses', $panelColumn)) {
                    $table->dropColumn($panelColumn);
                }
            }
            if (Schema::hasColumn('theses', 'defense_date')) {
                $table->dropColumn('defense_date');
            }
        });
    }

    public function down(): void
    {
        Schema::table('theses', function (Blueprint $table) {
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('course_id')->constrained()->cascadeOnDelete();
            $table->foreignId('adviser_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('title');
            $table->string('adviser');
            $table->text('abstract')->nullable();
            $table->string('abstract_pdf_path')->nullable();
            $table->string('endorsement_pdf_path');
            $table->decimal('grade', 5, 2)->nullable();
            $table->string('verification_token', 64)->nullable();
            $table->string('panel_chairman')->nullable();
            $table->string('panelist_one')->nullable();
            $table->string('panelist_two')->nullable();
            $table->date('defense_date')->nullable();
        });

        Schema::table('theses', function (Blueprint $table) {
            if (Schema::hasColumn('theses', 'thesis_title_id')) {
                $table->dropConstrainedForeignId('thesis_title_id');
            }
        });

        Schema::dropIfExists('thesis_titles');
    }
};
