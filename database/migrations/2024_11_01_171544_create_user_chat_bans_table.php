<?php

use App\Models\Chat;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('user_chat_bans', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(User::class)->constrained()->onDelete("cascade");
            $table->foreignIdFor(Chat::class)->constrained()->onDelete("cascade");
            $table->timestamps();
        });

        DB::unprepared('
            CREATE TRIGGER remove_user_chat_on_ban
            AFTER INSERT ON user_chat_bans
            FOR EACH ROW
            BEGIN
                DELETE FROM user_chats
                WHERE user_id = NEW.user_id AND chat_id = NEW.chat_id;
            END;
        ');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_chat_bans');
    }
};
