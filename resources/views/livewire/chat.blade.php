<div class="gap-2 m-0 p-2 bg-chat rounded-3 shadow h-100">
    @isset($userChat)
        <div class="container-lg py-3 d-flex flex-column h-100">
            {{-- Chat info --}}
            <div class="container-lg border-bottom border-divider border-opacity-25 pb-2 d-flex justify-content-between">
                <div class="d-flex align-items-center gap-2">
                    <img src="{{ asset($userChat->picture) }}" alt="X's profile picture" class="bg-white rounded-circle"
                        height="45" width="45">
                    <p class="m-0 text-white fw-bold">
                        {{ $userChat->name }}
                    </p>
                </div>
                <button class="border-0 bg-transparent text-white ms-auto">
                    <i class="bx bx-dots-horizontal-rounded bx-md"></i>
                </button>
            </div>

            {{-- Chat --}}
            <div id="chatContainer" class="d-flex flex-column py-2 overflow-y-scroll px-3 mt-auto">
                @foreach ($userChat->chat->messages()->with('user')->get() as $message)
                    @if ($message->user_id == auth()->user()->id)
                        <x-chat-sent-message :message="$message->content" />
                    @else
                        <x-chat-received-message :user="$message->user" :message="$message->content" />
                    @endif
                @endforeach
            </div>

            {{-- Input --}}
            <form class="input-group shadow rounded-5" wire:submit="sendMessage">
                @method('POST')
                @csrf

                <input type="text" wire:model="sendMessageForm.message"
                    class="form-control py-2 border border-start-0 border-divider border-opacity-25 bg-primary text-white rounded-start-4"
                    required>

                <button type="submit"
                    class="btn btn-accent text-white fw-bold border border-divider border-opacity-25 rounded-end-4 px-4">
                    Send
                </button>
            </form>
        </div>
    @else
        <div class="d-flex h-100 align-items-center justify-content-center">
            <h3 class="text-center text-white">You don't have any chats. <br>
                Explore channels here: <a href="#" class="text-decoration-underline text-accent">Channels</a>!
            </h3>
        </div>
    @endisset
</div>


<script type="module">
    // Echo
    function joinPrivateChannel(channelName) {
        Echo.private(channelName).listen("MessageSent", (e) => {
            Livewire.dispatch("messageSent");
        });
    }

    function switchChannels(oldChannel, newChannel) {
        Echo.leave(oldChannel);
        joinPrivateChannel(newChannel);
    }

    // Chat 
    const chatContainer = document.getElementById("chatContainer");

    function scrollToBottom() {
        chatContainer.scrollTop = chatContainer.scrollHeight;
    }

    document.addEventListener('livewire:init', function() {
        Livewire.on("switchChannels", (e) => {
            switchChannels(e.oldChannel, e.newChannel);
        });

        Livewire.on("scrollToBottom", (e) => {
            scrollToBottom();
        });
    });


    // Initial scroll
    scrollToBottom();

    // Initial echo join
    joinPrivateChannel("chats." + {{ $userChat->chat_id }});
</script>
