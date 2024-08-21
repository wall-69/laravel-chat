@props(['type' => 'unread', 'userChat'])

<a href="{{ route('chat.show', ['id' => $userChat->id]) }}"
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
</a>
