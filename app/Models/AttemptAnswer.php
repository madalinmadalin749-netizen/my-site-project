<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\AiQuestion;

class AttemptAnswer extends Model
{
    protected $fillable = ['attempt_id','question_id','selected','is_correct'];

    protected $casts = [
        'is_correct' => 'boolean',
    ];

    public function question(): BelongsTo
    {
        return $this->belongsTo(Question::class);
    }

public function aiQuestion()
{
    return $this->belongsTo(AiQuestion::class, 'ai_question_id');
}

// helper: întrebarea fie clasică fie AI
public function getAnyQuestionAttribute()
{
    return $this->question ?: $this->aiQuestion;
}
}
