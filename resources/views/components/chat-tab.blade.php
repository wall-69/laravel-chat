{{-- namiesto user musi byt chat, chat bude mat users list & chat name --}}
@props(['type' => 'unread', 'user', 'lastMesasge'])

<div class="d-flex gap-2 m-0 bg-{{ $type }}-chat-tab p-2 border-bottom border-divider">
    {{-- Profile/channel Picture --}}
    <img src="img/chat/female_avatar.svg" alt="X's profile picture" class="bg-white rounded-5" width="65" height="65">

    <div class="d-flex flex-column mw-0">
        {{-- Profile Name --}}
        <p class="m-0 text-white fw-bold">{{ $user->nickname }}</p>
        {{-- Last message --}}
        <p class="m-0 text-white fw-light small text-truncate">sarah45:
            lmao xd fr fr fr
            fr LOOL
            MAXDX
            XDDXDD SLN
            LDSLJ LKSJ KLDJSLK
        </p>
    </div>
</div>
