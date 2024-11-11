<?php

namespace App\Http\Controllers;

use App\Events\UserChatDeleted;
use App\Models\Chat;
use App\Models\User;
use App\Models\UserBlock;
use App\Models\UserChat;
use App\Services\NotificationService;
use Illuminate\Http\Request;

class UserBlockController extends Controller
{
    protected NotificationService $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Stores the UserBlock to database.
     */
    public function store(Request $request)
    {
        // Validate
        $request->validate([
            "blocked_user" => "required"
        ]);

        // Get the blocking user id and blocked user id
        $userId = auth()->user()->id;
        $blockedUserId = $request->blocked_user;

        // Check, if there is already existing UserBlock
        if (UserBlock::where("user_id", $userId)->where("blocked_user_id", $blockedUserId)->exists()) {
            abort(400, "User is already blocked.");
        } else {
            // Create the UserBlock
            UserBlock::create([
                "user_id" => $userId,
                "blocked_user_id" => $blockedUserId
            ]);

            // Find the Chat of these 2 users (eloquent magic)
            $chat = Chat::whereType("dm")
                // Check for the blocking user
                ->whereHas("userChats", function ($query) use ($userId) {
                    $query->where("user_id", $userId);
                })
                ->whereHas("userChats", function ($query) use ($blockedUserId) {
                    $query->where("user_id", $blockedUserId);
                })->first();

            // Remove the UserChat of the user that is blocking, if one exists
            if ($chat) {
                $userChat = $chat->userChats()->where("user_id", $userId)->first();
                if ($userChat) {
                    // Broadcast the UserChatDeleted event (if for instance, the user has the chat opened on another tab)
                    event(new UserChatDeleted($userChat));
                    $userChat->delete();
                }

                /** @var \App\Models\Chat|null $chat */
                $this->notificationService->chat(
                    $chat,
                    User::find($userId)->nickname . " blocked " . User::find($blockedUserId)->nickname . "."
                );
            }

            return back();
        }
    }

    /**
     * Deletes the UserBlock.
     */
    public function destroy(UserBlock $userBlock)
    {
        $userBlock->delete();

        return back();
    }
}
