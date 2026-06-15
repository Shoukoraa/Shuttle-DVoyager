<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Message;
use App\Events\DriverCustomerMessageSent;

class ChatController extends Controller
{
    public function getMessages(Request $request, $schedule_id, $customer_id = null)
    {
        // If customer_id is null, it means it's a customer requesting their own chat
        $user = $request->user();
        if (!$customer_id && $user && $user->role->name === 'customer') {
            $customer = \App\Models\Customer::where('user_id', $user->id)->first();
            if ($customer) {
                $customer_id = $customer->id;
            }
        }

        $messages = Message::where('schedule_id', $schedule_id)
            ->where('customer_id', $customer_id)
            ->orderBy('created_at', 'asc')
            ->get();
            
        return response()->json($messages);
    }

    public function sendMessage(Request $request)
    {
        $validated = $request->validate([
            'schedule_id' => 'required|exists:schedules,id',
            'customer_id' => 'required|exists:customers,id',
            'message' => 'required|string',
            'sender_type' => 'required|in:driver,customer'
        ]);

        $message = Message::create($validated);

        // Broadcast real-time message via Reverb
        broadcast(new DriverCustomerMessageSent($message))->toOthers();

        return response()->json($message);
    }
}
