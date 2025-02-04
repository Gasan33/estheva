<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\MessageResource;
use App\Models\Messages;
use App\Services\ApiResponse;
use Illuminate\Http\Request;

class MessagesController extends Controller
{
    /**
     * Display a listing of the messages.
     */
    public function index()
    {
        // Retrieve all messages with the related 'sender' and 'receiver' relationships
        $messages = Messages::with(['sender', 'receiver'])->get();

        // Return the messages as a resource collection
        return MessageResource::collection($messages);
    }

    public function userMessages(Request $request)
    {
        $messages = Messages::with(['sender', 'receiver'])
            ->where('sender_id', $request->sender_id)
            ->get();
        return $this->api()->success($messages, );
    }
    public function senderAndReceiverMessages(Request $request)
    {
        $messages = Messages::with(['sender', 'receiver'])
            ->where('sender_id', $request->sender_id)
            ->where('receiver_id', $request->receiver_id)
            ->get();
        return $this->api()->success($messages, );
    }

    /**
     * Store a newly created message in storage.
     */
    public function store(Request $request)
    {
        // Validate incoming request data
        $validated = $request->validate([
            'sender_id' => 'required|exists:users,id',
            'receiver_id' => 'required|exists:users,id',
            'message_text' => 'required|string|max:500',
            'is_read' => 'required|boolean',
        ]);

        // Create the new message
        $message = Messages::create($validated);

        // Return the newly created message as a resource
        return new MessageResource($message);
    }

    /**
     * Display the specified message.
     */
    public function show($id)
    {
        // Find the message by ID, including the related 'sender' and 'receiver' models
        $message = Messages::with(['sender', 'receiver'])->findOrFail($id);

        // Return the message as a resource
        return new MessageResource($message);
    }

    /**
     * Update the specified message in storage.
     */
    public function update(Request $request, $id)
    {
        // Validate incoming request data
        $validated = $request->validate([
            'sender_id' => 'sometimes|exists:users,id',
            'receiver_id' => 'sometimes|exists:users,id',
            'message_text' => 'sometimes|string|max:500',
            'is_read' => 'sometimes|boolean',
        ]);

        // Find the message by ID
        $message = Messages::findOrFail($id);

        // Update the message with the validated data
        $message->update($validated);

        // Return the updated message as a resource
        return new MessageResource($message);
    }

    /**
     * Remove the specified message from storage.
     */
    public function destroy($id)
    {
        // Find the message by ID
        $message = Messages::findOrFail($id);

        // Delete the message
        $message->delete();

        // Return a success message
        return response()->json(['message' => 'Message deleted successfully.']);
    }
}


