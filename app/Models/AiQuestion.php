<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AiQuestion extends Model
{
    protected $fillable = [
        'user_id','category_id','source_attempt_id',
        'prompt','a','b','c','d','correct','explanation','difficulty',
        'generator'
    ];

    public function user(): BelongsTo { return $this->belongsTo(User::class); }
    public function category(): BelongsTo { return $this->belongsTo(Category::class); }
    public function sourceAttempt(): BelongsTo { return $this->belongsTo(Attempt::class, 'source_attempt_id'); }
}
