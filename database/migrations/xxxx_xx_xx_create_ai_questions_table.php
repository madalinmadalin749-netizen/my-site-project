<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('ai_questions', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('category_id')->constrained()->cascadeOnDelete();
            $table->foreignId('source_attempt_id')->nullable()->constrained('attempts')->nullOnDelete();

            $table->text('prompt');
            $table->string('a');
            $table->string('b');
            $table->string('c')->nullable();
            $table->string('d')->nullable();

            $table->char('correct', 1);
            $table->text('explanation')->nullable();
            $table->unsignedTinyInteger('difficulty')->default(1);

            $table->string('generator')->default('mock'); // "mock" acum, "openai" mai târziu
            $table->timestamps();

            $table->index(['user_id','category_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ai_questions');
    }
};
