<?php

namespace App\Http\Controllers;

use App\Events\ChatAdminChange;
use App\Models\Chat;
use App\Models\ChatAdmin;
use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Http\Request;

class ChatAdminController extends Controller
{
    protected NotificationService $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Changes the admin for the specified Chat from the data from the request. Also sends a notification message in the chat & broadcasts ChatAdminChange event for real time JS state update.
     */
    public function changeAdmin(Request $request, Chat $chat)
    {
        // Check, if the chat is channel
        if ($chat->isDM()) {
            abort(400, "ChatAdmin can only be set in channels, not DMs.");
        }

        $request->validate(["new_user_id" => "required|exists:users,id"]);

        // Check, if the current ChatAdmin user_id is the same as the new one
        $currentAdmin = ChatAdmin::whereUserId(auth()->user()->id)->firstOrFail();
        if ($currentAdmin->user_id == $request->new_user_id) {
            return response()->json(["message" => "This user is already a ChatAdmin."]);
        }

        // Delete the current ChatAdmin
        $currentAdmin->delete();

        // Create new ChatAdmin
        $chatAdmin = ChatAdmin::create(["user_id" => $request->new_user_id, "chat_id" => $chat->id]);

        // Send notification to the Chat
        $this->notificationService->chat($chat, User::find($request->new_user_id)->nickname . " is now the admin of this channel.");

        // Broadcast ChatAdminChange event
        event(new ChatAdminChange($chatAdmin));

        return response()->json(["message" => "New admin was set successfully."]);
    }
}
