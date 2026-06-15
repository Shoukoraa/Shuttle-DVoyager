<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChatbotProblem extends Model
{
    protected $fillable = ['category_id', 'title', 'solution_text', 'additional_solution', 'sort_order'];

    public function category(): BelongsTo
    {
        return $this->belongsTo(ChatbotCategory::class, 'category_id');
    }
}
