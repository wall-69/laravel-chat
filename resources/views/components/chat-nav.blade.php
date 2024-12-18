<header class="w-100 bg-secondary">
    <nav class="container-md d-flex py-3 d-flex justify-content-between">
        <span class="h2 fw-light text-white my-auto">LaraChat</span>

        {{-- DESKTOP LINKS --}}
        <ul class="d-none list-unstyled d-md-flex my-auto gap-5">
            <li>
                <a href="{{ route('chats.index') }}" class="text-white fs-5 {{ request()->is('chat') ? 'fw-bold' : '' }}">
                    Chat
                </a>
            </li>
            <li>
                <a href="{{ route('chats.channels') }}"
                    class="text-white fs-5 {{ request()->is('channels') ? 'fw-bold' : '' }}">
                    Channels
                </a>
            </li>
            {{-- My profile and Log out buttons --}}
            @auth
                <li>
                    @php
                        $userNickname = auth()->user()->nickname;
                    @endphp
                    <a href="{{ route('users.show', ['nickname' => $userNickname]) }}"
                        class="text-white fs-5 {{ request()->is('profile/' . $userNickname) ? 'fw-bold' : '' }}">
                        My Profile
                    </a>
                </li>
                <li>
                    <form action="{{ route('users.logout') }}" method="POST">
                        @method('POST')
                        @csrf

                        <button class="bg-transparent border-0 text-white fs-5">Log Out</button>
                    </form>
                </li>
            @endauth
        </ul>

        {{-- MOBILE DROPDOWN --}}
        <div class="dropdown d-flex d-md-none align-items-center justify-content-center">
            <button class="bg-transparent border-0" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="bx bx-menu bx-md text-white"></i>
            </button>

            <ul class="dropdown-menu">
                <li>
                    <a href="{{ route('chats.index') }}"
                        class="dropdown-item fs-5 {{ request()->is('chat') ? 'fw-bold' : '' }}">
                        Chat
                    </a>
                </li>
                <li>
                    <a href="{{ route('chats.channels') }}"
                        class="dropdown-item fs-5 {{ request()->is('channels') ? 'fw-bold' : '' }}">
                        Channels
                    </a>
                </li>
                {{-- My profile and Log out buttons --}}
                @auth
                    <li>
                        @php
                            $userNickname = auth()->user()->nickname;
                        @endphp
                        <a href="{{ route('users.show', ['nickname' => $userNickname]) }}"
                            class="dropdown-item fs-5 {{ request()->is('profile/' . $userNickname) ? 'fw-bold' : '' }}">
                            My Profile
                        </a>
                    </li>
                    <li>
                        <form action="{{ route('users.logout') }}" method="POST">
                            @method('POST')
                            @csrf

                            <button class="dropdown-item bg-transparent border-0 fs-5">Log Out</button>
                        </form>
                    </li>
                @endauth
            </ul>
        </div>
    </nav>
</header>
