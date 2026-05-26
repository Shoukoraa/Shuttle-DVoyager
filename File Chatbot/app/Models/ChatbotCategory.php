<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ChatbotCategory extends Model
{
    protected $fillable = ['name', 'icon', 'color', 'sort_order'];

    public function problems(): HasMany
    {
        return $this->hasMany(ChatbotProblem::class, 'category_id')->orderBy('sort_order');
    }
}
