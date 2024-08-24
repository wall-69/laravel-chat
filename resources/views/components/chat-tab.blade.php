@props(['type' => 'unread', 'userChat'])
{{-- REPLACE div WITH SOMETHING --}}
<div role="button" id="chat-tab-{{ $userChat->id }}"
    class="d-flex gap-2 m-0 bg-{{ $type }}-chat-tab p-2 border-bottom border-divider text-decoration-none">
    {{-- Profile/channel Picture --}}
    <img src="{{ asset($userChat->picture) }}" alt="X's profile picture" class="bg-white rounded-circle" width="65"
        height="65">

    <div class="d-flex flex-column mw-0">
        {{-- Profile Name --}}
        <p class="m-0 text-white fw-bold">{{ $userChat->name }}</p>
        {{-- Last message --}}
        <p class="m-0 text-white fw-light small text-truncate">
            @php
                $lastMessage = $userChat->chat->lastMessage;
            @endphp

            @isset($lastMessage)
                {{ $lastMessage->user->nickname }}: {{ $lastMessage->content }}
            @else
                No messages sent yet.
            @endisset
        </p>
    </div>
</div>

@push('scripts')
    <script>
        document.querySelector("#chat-tab-{{ $userChat->id }}").addEventListener("click", function(event) {
            event.preventDefault();

            Echo.leave("chats.{{ $userChat->chat_id }}");

            fetch("{{ route('chat.get', ['id' => $userChat->id]) }}")
                .then(response => response.json())
                .then(data => {
                    document.querySelector("#chat").innerHTML = data.html;

                    const chatContainer = document.getElementById("chatContainer");

                    function scrollToBottom() {
                        chatContainer.scrollTop = chatContainer.scrollHeight;
                    }

                    scrollToBottom();

                    const parser = new DOMParser();

                    function addMessage(html) {
                        let dom = parser.parseFromString(html, "text/html");
                        chatContainer.appendChild(dom.querySelector("div"));

                        scrollToBottom();
                    }
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
                    Echo.private("chats.{{ $userChat->chat_id }}").listen("MessageSent", (e) => {
                        if (e.senderId == {{ auth()->user()->id }}) {
                            addMessage(e.htmlSent);
                        } else {
                            addMessage(e.htmlReceived);
                        }
                    });
                }).catch(error => console.error("Error fetching chat: " + error));
        });
    </script>
@endpush
