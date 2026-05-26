<?php

namespace App\Services;

use App\Models\ChatSession;
use App\Models\ChatMessage;
use App\Models\ChatbotCategory;
use App\Models\ChatbotProblem;
use App\Events\MessageSent;
use App\Events\SessionStatusChanged;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Collection;

class ChatbotService
{
    /**
     * Get all chatbot categories.
     */
    public function getCategories(): Collection
    {
        return ChatbotCategory::orderBy('sort_order')->get();
    }

    /**
     * Get all problems for a specific category.
     */
    public function getProblems(int $categoryId): Collection
    {
        return ChatbotProblem::where('category_id', $categoryId)
            ->orderBy('sort_order')
            ->get();
    }

    /**
     * Start a live chat session (connect user to admin).
     * This is the point where we transition from frontend bot to database-backed live chat.
     */
    public function startActiveSession(int $userId, ?int $lastCategoryId = null, ?int $lastProblemId = null): ChatSession
    {
        // 1. Resolve any previous active/bot sessions for this user
        ChatSession::where('user_id', $userId)
            ->whereIn('status', ['bot', 'active'])
            ->update(['status' => 'resolved']);

        // 2. Create new active chat session
        $session = ChatSession::create([
            'id' => (string) Str::uuid(),
            'user_id' => $userId,
            'status' => 'active',
            'last_category_id' => $lastCategoryId,
            'last_problem_id' => $lastProblemId
        ]);

        // 3. Log the initial transition message from user
        ChatMessage::create([
            'chat_session_id' => $session->id,
            'sender_type' => 'user',
            'sender_id' => $userId,
            'message_type' => 'text',
            'message_content' => 'Hubungkan ke Admin (Butuh bantuan langsung)'
        ]);

        // 4. Log the bot system acknowledgment
        ChatMessage::create([
            'chat_session_id' => $session->id,
            'sender_type' => 'bot',
            'message_type' => 'text',
            'message_content' => 'Menghubungkan ke Admin CS kami. Mohon tunggu sebentar, Admin akan segera membalas pesan Anda... 👤'
        ]);

        // 5. Broadcast to the admin dashboard via Reverb
        broadcast(new SessionStatusChanged($session))->toOthers();

        return $session->load(['messages', 'user']);
    }

    /**
     * Send a live message during an active session (user <-> admin).
     */
    public function sendLiveMessage(string $sessionId, string $senderType, int $senderId, string $content): ChatMessage
    {
        $session = ChatSession::findOrFail($sessionId);

        $message = ChatMessage::create([
            'chat_session_id' => $session->id,
            'sender_type' => $senderType,
            'sender_id' => $senderId,
            'message_type' => 'text',
            'message_content' => $content
        ]);

        // Broadcast realtime message to participants via Reverb
        broadcast(new MessageSent($message))->toOthers();

        return $message;
    }

    /**
     * Resolve/close an active session.
     */
    public function resolveSession(string $sessionId): ChatSession
    {
        $session = ChatSession::findOrFail($sessionId);
        $session->update(['status' => 'resolved']);

        // Optional bot final message
        ChatMessage::create([
            'chat_session_id' => $session->id,
            'sender_type' => 'bot',
            'message_type' => 'text',
            'message_content' => 'Sesi chat telah diselesaikan oleh Admin. Terima kasih telah menghubungi Customer Service kami!'
        ]);

        return $session;
    }
}
