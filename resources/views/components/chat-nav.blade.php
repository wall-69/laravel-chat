<header class="w-100 bg-secondary">
    <nav class="container d-flex py-3 d-flex justify-content-between">
        <span class="h2 fw-light text-white my-auto">LaraChat</span>
        <ul class="list-unstyled d-flex my-auto gap-5">
            <li><a href="{{ route('chat.index') }}" class="text-white fs-5 fw-bold">Chat</a></li>
            <li><a href="#" class="text-white fs-5">Channels</a></li>
            @auth
                <li><a href="{{ route('users.show', ['nickname' => auth()->user()->nickname]) }}" class="text-white fs-5">My
                        Profile</a>
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
    </nav>
</header>
