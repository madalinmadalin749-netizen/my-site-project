<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('attempt_answers', function (Blueprint $table) {
            // facem question_id nullable ca să poată fi AI row
            $table->foreignId('question_id')->nullable()->change();

            // adăugăm ai_question_id
            $table->foreignId('ai_question_id')
                ->nullable()
                ->after('question_id')
                ->constrained('ai_questions')
                ->nullOnDelete();

            $table->index(['attempt_id','ai_question_id']);
        });
    }

    public function down(): void
    {
        Schema::table('attempt_answers', function (Blueprint $table) {
            $table->dropConstrainedForeignId('ai_question_id');
            $table->dropIndex(['attempt_id','ai_question_id']);

            // revenim (dacă vrei strict)
            $table->foreignId('question_id')->nullable(false)->change();
        });
    }
};
