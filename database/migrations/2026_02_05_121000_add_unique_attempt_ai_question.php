<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('attempt_answers', function (Blueprint $table) {
            // Pentru întrebări AI: evităm duplicatele pe același attempt
            $table->unique(['attempt_id', 'ai_question_id'], 'attempt_ai_unique');
        });
    }

    public function down(): void
    {
        Schema::table('attempt_answers', function (Blueprint $table) {
            $table->dropUnique('attempt_ai_unique');
        });
    }
};
