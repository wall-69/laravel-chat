@extends('layouts.app')

@section('content')
    <x-chat-nav />
    <main class="flex-shrink-0 h-75">
        <div class="container py-5 h-100">
            <div class="row h-100">
                <div class="col-3 h-100 rounded-3 bg-secondary bg-gradient shadow p-0 py-3"
                    style="overflow-x: hidden; overflow-y: scroll">
                    <h4 class="text-white text-center fw-light">Your chats</h4>

                    <div class="w-100 d-flex gap-2 bg-accent bg-gradient p-2">
                        <img src="img/chat/female_avatar.svg" alt="X's profile picture" width="65" height="65">
                        <div class="d-flex flex-column">
                            {{-- Profile Name --}}
                            <p class="m-0 text-white fw-bold">sarah45</p>
                            {{-- Last message --}}
                            <p class="m-0 text-white fw-light small overflow-hidden text-truncate">sarah45:
                                lmao xd
                                fr fr fr
                                fr LOOL
                                MAXDX
                                XDDXDD SLN
                                LDSLJ LKSJ KLDJSLK</p>
                        </div>
                    </div>
                </div>
                <div class="col-9 overflow-scroll">yay</div>
            </div>
        </div>
    </main>
    <x-footer />
@endsection
