@extends('layouts.app')

@section('content')
    <x-chat-nav />

    <main>
        <div class="d-flex flex-column flex-md-row px-4 py-3 gap-1 justify-content-between">
            <form method="GET" action="{{ route('chat.channels') }}" class="d-flex flex-column">
                <label class="fw-bold">Search for channels</label>
                <input type="search" name="search" value="{{ request()->input('search') }}"
                    placeholder="{{ collect(['Laravel', 'VueJS', 'Gaming', 'Music', 'Programming', 'Web dev'])->random() }}">
            </form>

            <a href="{{ route('chat.create') }}" class="bg-primary px-2 py-2 text-center my-auto text-white rounded-3">
                Create new channel
            </a>
        </div>

        @if ($channels->count() > 0)
            @foreach ($channels as $channel)
                @if (auth()->user()->userChatBans->doesntContain('chat_id', $channel->id))
                    <div class="d-flex flex-column">
                        {{ $channel->name }}

                        @if (auth()->user()->userChats->doesntContain('chat_id', $channel->id))
                            <form method="POST" action="{{ route('chat.join', $channel->id) }}">
                                @method('POST')
                                @csrf

                                <button type="submit">Join</button>
                            </form>
                        @else
                            <p>you are already in this channel!</p>
                        @endif
                    </div>
                @endif
            @endforeach
        @else
            @if (request()->filled('search'))
                <p>No <strong>public</strong> channels with such name exist!</p>
            @else
                <p>There are no channels created yet.</p>
            @endif
        @endif
    </main>

    <x-footer />
@endsection
