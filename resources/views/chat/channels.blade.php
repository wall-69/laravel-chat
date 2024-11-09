@extends('layouts.app')

@section('content')
    <x-chat-nav />

    <main>
        <div class="d-flex flex-column flex-md-row px-4 py-3 gap-1 justify-content-between">
            {{-- Search --}}
            <form method="GET" action="{{ route('chat.channels') }}" class="d-flex flex-column">
                <label class="fw-bold">Search for channels</label>
                <input type="search" name="search" value="{{ request()->input('search') }}"
                    placeholder="{{ collect(['Laravel', 'VueJS', 'Gaming', 'Music', 'Programming', 'Web dev'])->random() }}">
            </form>

            {{-- Create new channel --}}
            <a href="{{ route('chat.create') }}" class="bg-primary px-2 py-2 text-center my-auto text-white rounded-3">
                Create new channel
            </a>
        </div>

        <div class="d-flex flex-row flex-wrap gap-3 gap-md-5 px-4 mb-3">
            {{-- Channels list --}}
            @if ($channels->count() > 0)
                @foreach ($channels as $channel)
                    @if (auth()->user()->userChatBans->doesntContain('chat_id', $channel->id))
                        <article class="d-flex justify-content-between bg-primary p-2 rounded-3"
                            style="width: 256px; height: 104px;">
                            <div class="d-flex flex-column align-items-start justify-content-between">
                                <h2 class="text-white fw-bold fs-6" style="height: 2.4rem; overflow: hidden;">
                                    {{ $channel->name }}
                                </h2>

                                @if (auth()->user()->userChats->doesntContain('chat_id', $channel->id))
                                    <form method="POST" action="{{ route('chat.join', $channel->id) }}">
                                        @method('POST')
                                        @csrf

                                        <button type="submit"
                                            class="text-white btn-link bg-accent px-3 py-2 fw-bold rounded-3 border-0">Join</button>
                                    </form>
                                @else
                                    <a href="{{ route('chat.index') }}"
                                        class="text-white bg-accent px-3 py-2 fw-bold rounded-3">
                                        Chat
                                    </a>
                                @endif
                            </div>

                            <img src="{{ asset($channel->picture) }}" alt="{{ $channel->name }} channel picture"
                                width="60" height="60" class="bg-white rounded-circle align-self-center">
                        </article>
                    @endif
                @endforeach
            @else
                {{-- No channels found --}}
                @if (request()->filled('search'))
                    <p>No <strong>public</strong> channels with such name exist!</p>
                @else
                    {{-- No channels exist --}}
                    <p>There are no channels created yet.</p>
                @endif
            @endif
        </div>

        <div class="px-4">
            {{ $channels->withQueryString()->links() }}
        </div>
    </main>

    <x-footer />
@endsection
