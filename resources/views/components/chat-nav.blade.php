<header class="w-100 bg-secondary">
    <nav class="container d-flex py-3 d-flex justify-content-between">
        <span class="h2 fw-light text-white my-auto">LaraChat</span>

        <ul class="d-none list-unstyled d-md-flex my-auto gap-5">
            <li>
                <a href="{{ route('chat.index') }}" class="text-white fs-5 {{ request()->is('chat') ? 'fw-bold' : '' }}">
                    Chat
                </a>
            </li>
            <li>
                <a href="#" class="text-white fs-5 {{ request()->is('channels') ? 'fw-bold' : '' }}">
                    Channels
                </a>
            </li>
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

        <button class="d-flex align-items-center justify-content-center d-md-none bg-transparent border-0">
            <i class="bx bx-menu bx-lg text-white"></i>
        </button>
    </nav>
</header>
