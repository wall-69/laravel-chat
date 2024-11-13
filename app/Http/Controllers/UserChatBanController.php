<?php

namespace App\Http\Controllers;

use App\Events\UserBannedFromChat;
use App\Events\UserChatDeleted;
use App\Models\Chat;
use App\Models\User;
use App\Models\UserChat;
use App\Models\UserChatBan;
use App\Services\NotificationService;
use Illuminate\Http\Request;

class UserChatBanController extends Controller
{
    protected NotificationService $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Stores the UserChatBan and also sends a notification to the chat.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            "user_id" => "required:exists:users",
            "chat_id" => "required:exists:chats"
        ]);

        // Get the User
        $user = User::find($request->user_id);

        // Get the UserChat or abort, then delete it
        $userChat = UserChat::where("user_id", $user->id)->where("chat_id", $request->chat_id)->firstOrFail();
        // Broadcast the UserChatDeleted event
        event(new UserChatDeleted($userChat));
        $userChat->delete();

        $this->notificationService->chat(Chat::find($request->chat_id), User::find($request->user_id)->nickname . " has been banned.");
        $userChatBan = UserChatBan::create($data);

        // Broadcast the UserBannedFromChat event
        event(new UserBannedFromChat($request->chat_id, $userChatBan));

        return response()->json(["message" => "User was successfully banned."]);
    }

    /**
     * Deletes the UserChatBan and also sends a notification to the chat.
     */
    public function destroy(UserChatBan $userChatBan)
    {
        $this->notificationService->chat($userChatBan->chat, $userChatBan->user->nickname . " has been unbanned.");
        $userChatBan->delete();

        return response()->json(["message" => "User was successfully unbanned."]);
    }
}
