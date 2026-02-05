<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Question extends Model
{
    protected $fillable = [
        'category_id','prompt','a','b','c','d','correct','explanation','difficulty'
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }
}
