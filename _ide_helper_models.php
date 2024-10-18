<?php

// @formatter:off
// phpcs:ignoreFile
/**
 * A helper file for your Eloquent Models
 * Copy the phpDocs from this file to the correct Model,
 * And remove them from this file, to prevent double declarations.
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 */


namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property string $name
 * @property int $is_private
 * @property \Illuminate\Support\Carbon|null $last_message
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\ChatAdmin|null $chatAdmin
 * @property-read \App\Models\Message|null $lastMessage
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Message> $messages
 * @property-read int|null $messages_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\UserChat> $userChats
 * @property-read int|null $user_chats_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User> $users
 * @property-read int|null $users_count
 * @method static \Illuminate\Database\Eloquent\Builder|Chat newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Chat newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Chat query()
 * @method static \Illuminate\Database\Eloquent\Builder|Chat whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Chat whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Chat whereIsPrivate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Chat whereLastMessage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Chat whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Chat whereUpdatedAt($value)
 */
	class Chat extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int $chat_id
 * @property int $user_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Chat $chat
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|ChatAdmin newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ChatAdmin newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ChatAdmin query()
 * @method static \Illuminate\Database\Eloquent\Builder|ChatAdmin whereChatId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ChatAdmin whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ChatAdmin whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ChatAdmin whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ChatAdmin whereUserId($value)
 */
	class ChatAdmin extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int|null $user_id
 * @property int $chat_id
 * @property string $content
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Chat $chat
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder|Message newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Message newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Message query()
 * @method static \Illuminate\Database\Eloquent\Builder|Message whereChatId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Message whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Message whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Message whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Message whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Message whereUserId($value)
 */
	class Message extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property string $nickname
 * @property string $email
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property mixed $password
 * @property string $profile_picture
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Message> $messages
 * @property-read int|null $messages_count
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\UserBlock> $userBlocks
 * @property-read int|null $user_blocks_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\UserChat> $userChats
 * @property-read int|null $user_chats_count
 * @method static \Database\Factories\UserFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|User query()
 * @method static \Illuminate\Database\Eloquent\Builder|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereNickname($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereProfilePicture($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereUpdatedAt($value)
 */
	class User extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int $user_id
 * @property int $blocked_user_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User $blockedUser
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|UserBlock newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|UserBlock newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|UserBlock query()
 * @method static \Illuminate\Database\Eloquent\Builder|UserBlock whereBlockedUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserBlock whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserBlock whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserBlock whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserBlock whereUserId($value)
 */
	class UserBlock extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int $user_id
 * @property int $chat_id
 * @property string $name
 * @property string $picture
 * @property \Illuminate\Support\Carbon|null $last_read
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Chat $chat
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|UserChat newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|UserChat newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|UserChat query()
 * @method static \Illuminate\Database\Eloquent\Builder|UserChat whereChatId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserChat whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserChat whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserChat whereLastRead($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserChat whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserChat wherePicture($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserChat whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserChat whereUserId($value)
 */
	class UserChat extends \Eloquent {}
}

