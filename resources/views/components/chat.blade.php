@props(['userChat'])

<div class="gap-2 m-0 p-2 bg-chat rounded-3 shadow h-100">
    @isset($userChat)
        <div class="container py-3 d-flex flex-column h-100">
            {{-- Chat info --}}
            <div class="container border-bottom border-divider border-opacity-25 pb-2 d-flex justify-content-between">
                <div class="d-flex align-items-center gap-2">
                    <img src="{{ asset($userChat->picture) }}" alt="X's profile picture" class="bg-white rounded-circle"
                        height="45" width="45">
                    <p class="m-0 text-white fw-bold">
                        {{ $userChat->name }}
                    </p>
                </div>
                <button class="border-0 bg-transparent text-white">
                    <i class="bx bx-dots-horizontal-rounded bx-md"></i>
                </button>
            </div>

            {{-- Chat --}}
            <div id="chatContainer" class="d-flex flex-column py-2 overflow-y-scroll px-3 mt-auto">
                @foreach ($userChat->chat->messages as $message)
                    @if ($message->user_id == auth()->user()->id)
                        <x-chat-sent-message :message="$message->content" />
                    @else
                        <x-chat-received-message :userId="$message->user_id" :message="$message->content" />
                    @endif
                @endforeach
            </div>

            {{-- Input --}}
            {{-- action="{{ route('message.create', ['chatId' => $userChat->chat_id]) }}" method="POST" --}}
            <form id="messageForm" class="input-group shadow rounded-5">
                @method('POST')
                @csrf

                <input type="text" name="message"
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
    const chatContainer = document.getElementById("chatContainer");

    function scrollToBottom() {
        chatContainer.scrollTop = chatContainer.scrollHeight;
    }

    scrollToBottom();

    @isset($userChat)
        const parser = new DOMParser();

        function addSentMessage(html) {
            let dom = parser.parseFromString(html, "text/html");
            chatContainer.appendChild(dom.querySelector("div"));

            scrollToBottom();
        }

        document.addEventListener('DOMContentLoaded', function() {
            const messageForm = document.getElementById('messageForm');
            messageForm.addEventListener('submit', function(e) {
                e.preventDefault();

                const formData = new FormData(messageForm);
                const csrfToken = document.querySelector('input[name="_token"]').value;

                fetch('{{ route('message.create', ['chatId' => $userChat->chat_id]) }}', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json',
                        },
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            console.log('Message sent successfully!');
                            messageForm.reset();
                        } else {
                            console.error('An error occurred: ' + (data.message ||
                                'Unknown error'));
                        }
                    })
                    .catch(error => {
                        console.error('An error occurred: ' + error.message);
                    });
            });

            // Broadcasts
            Echo.channel("chats.{{ $userChat->chat_id }}").listen("MessageSent", (e) => {
                addSentMessage(e.html);
            });

        });
    @endisset
</script>
