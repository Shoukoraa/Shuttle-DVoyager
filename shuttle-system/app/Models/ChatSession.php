<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ChatSession extends Model
{
    use HasUuids;

    protected $fillable = [
        'id',
        'user_id',
        'admin_id',
        'status',
        'last_category_id',
        'last_problem_id'
    ];

    protected $casts = [
        'id' => 'string',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function admin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    public function messages(): HasMany
    {
        return $this->hasMany(ChatMessage::class, 'chat_session_id');
    }

    public function lastCategory(): BelongsTo
    {
        return $this->belongsTo(ChatbotCategory::class, 'last_category_id');
    }

    public function lastProblem(): BelongsTo
    {
        return $this->belongsTo(ChatbotProblem::class, 'last_problem_id');
    }
}
