<?php

namespace App\Http\Controllers;

use App\Models\Chat;
use App\Models\User;
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

        $this->notificationService->chat(Chat::find($request->chat_id), User::find($request->user_id)->nickname . " has been banned.");
        UserChatBan::create($data);

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
