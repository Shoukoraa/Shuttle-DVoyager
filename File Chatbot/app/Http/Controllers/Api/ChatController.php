<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\ChatbotService;
use App\Models\ChatSession;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ChatController extends Controller
{
    public function __construct(protected ChatbotService $chatService) {}

    /**
     * Get chatbot categories for the guided flow.
     */
    public function getCategories(): JsonResponse
    {
        $categories = $this->chatService->getCategories();
        return response()->json($categories);
    }

    /**
     * Get problems/FAQ under a specific category.
     */
    public function getProblems(Request $request): JsonResponse
    {
        $request->validate([
            'category_id' => 'required|exists:chatbot_categories,id'
        ]);

        $problems = $this->chatService->getProblems($request->category_id);
        return response()->json($problems);
    }

    /**
     * Connect a user to a live admin (creates an active database session).
     */
    public function connectAdmin(Request $request): JsonResponse
    {
        $request->validate([
            'last_category_id' => 'nullable|exists:chatbot_categories,id',
            'last_problem_id' => 'nullable|exists:chatbot_problems,id'
        ]);

        $session = $this->chatService->startActiveSession(
            $request->user()->id,
            $request->last_category_id,
            $request->last_problem_id
        );

        return response()->json([
            'session' => $session,
            'messages' => $session->messages()->orderBy('created_at')->get()
        ]);
    }

    /**
     * Send a live message during an active session (user <-> admin).
     */
    public function sendMessage(Request $request, string $sessionId): JsonResponse
    {
        $request->validate([
            'message' => 'required|string|max:1000'
        ]);

        $senderType = $request->user()->role === 'admin' ? 'admin' : 'user';

        $message = $this->chatService->sendLiveMessage(
            $sessionId,
            $senderType,
            $request->user()->id,
            $request->message
        );

        return response()->json($message);
    }

    /**
     * Fetch message history for a specific active session.
     */
    public function getHistory(string $sessionId): JsonResponse
    {
        $session = ChatSession::findOrFail($sessionId);
        
        return response()->json([
            'session' => $session,
            'messages' => $session->messages()->orderBy('created_at')->get()
        ]);
    }

    /**
     * Resolve/close an active chat session.
     */
    public function resolve(string $sessionId): JsonResponse
    {
        $session = $this->chatService->resolveSession($sessionId);
        
        return response()->json([
            'message' => 'Chat session resolved successfully.',
            'session' => $session
        ]);
    }
}
