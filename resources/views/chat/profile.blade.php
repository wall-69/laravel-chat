@extends('layouts.app')

@section('content')
    <x-chat-nav />
    <main class="flex-shrink-0">
        <div class="py-5">
            {{-- Check, if the user is not null, if not, show error message --}}
            @isset($user)
                @php
                    $isMyProfile = auth()->check() && auth()->user()->nickname == $user->nickname;

                    $messageCount = $user->messages()->count();
                    $userChatCount = $user->userChats()->count();
                @endphp
                <div class="d-flex flex-md-row flex-column container gap-5 justify-content-center">
                    {{-- Column 1 --}}
                    <div class="d-flex flex-column gap-5">
                        {{-- Profile card --}}
                        <div class="px-3 py-2 bg-primary rounded-4 shadow d-flex gap-2">
                            <img src="{{ asset($user->profile_picture) }}" alt="X's profile picture"
                                class="bg-white rounded-circle" height="80" width="80">
                            <div class="text-white">
                                <p class="fs-4 fw-bold m-0">{{ $user->nickname }}</p>
                                <p class="m-0 fw-light">joined {{ $user->created_at->format('m.d.Y') }}</p>
                                <p class="m-0 fw-light">
                                    sent
                                    {{ $messageCount }} {{ Str::plural('message', $messageCount) }}
                                    and is in
                                    {{ $userChatCount }} {{ Str::plural('chat', $userChatCount) }}
                                </p>
                            </div>
                        </div>

                        @if ($isMyProfile)
                            {{-- Settings --}}
                            <x-change-settings-form></x-change-settings-form>
                        @endif
                    </div>
                    <div class="d-flex flex-column gap-5">
                        @if ($isMyProfile)
                            {{-- Column 2 --}}
                            {{-- Change email --}}
                            <x-change-email-form></x-change-email-form>

                            {{-- Change password --}}
                            <x-change-password-form></x-change-password-form>
                        @else
                            <div class="px-3 py-2 bg-secondary rounded-4 shadow d-flex flex-column gap-2">
                                {{--
                                    Show Start chat button to:
                                    - guest users
                                    - users that dont have a UserChat with this user AND none of these 2 users block each other
                                --}}
                                @if (
                                    !auth()->check() ||
                                        (auth()->user()->userChats->doesntContain('name', $user->nickname) &&
                                            auth()->user()->userBlocks->doesntContain('blocked_user_id', $user->id) &&
                                            $user->userBlocks->doesntContain('blocked_user_id', auth()->user()->id)))
                                    <form method="POST" action="{{ route('chats.store') }}">
                                        @method('POST')
                                        @csrf

                                        @auth
                                            <input type="hidden" name="users[]" value="{{ auth()->user()->id }}">
                                            <input type="hidden" name="users[]" value="{{ $user->id }}">
                                        @endauth

                                        <button type="submit"
                                            class="border-0 bg-transparent d-flex align-items-center gap-2 text-white">
                                            <i class="bx bx-user-plus bx-md"></i>
                                            <span class="fs-5 fw-bold">
                                                Start chat
                                            </span>
                                        </button>
                                    </form>
                                @endif

                                @auth
                                    @php
                                        $userBlock = auth()
                                            ->user()
                                            ->userBlocks()
                                            ->where('blocked_user_id', $user->id)
                                            ->first();
                                    @endphp

                                    @if ($userBlock)
                                        {{-- IF user is blocked --}}
                                        <form method="POST" action="{{ route('userBlocks.destroy', $userBlock) }}">
                                            @method('DELETE')
                                            @csrf

                                            <button type="submit" class="border-0 bg-transparent d-flex align-items-center gap-2">
                                                <i class="bx bx-user-check bx-md"></i>
                                                <span class="fs-5 fw-bold">
                                                    Unblock
                                                </span>
                                            </button>
                                        </form>
                                    @else
                                        {{-- ELSE user isnt blocked --}}
                                        <form method="POST" action="{{ route('userBlocks.store') }}">
                                            @method('POST')
                                            @csrf

                                            <input type="hidden" name="blocked_user" value="{{ $user->id }}">

                                            <button type="submit" class="border-0 bg-transparent d-flex align-items-center gap-2">
                                                <i class="bx bx-user-x bx-md"></i>
                                                <span class="fs-5 fw-bold">
                                                    Block
                                                </span>
                                            </button>
                                        </form>
                                    @endif
                                @endauth
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @else
            <h3>This user does not exist!</h3>
        @endisset
        </div>
    </main>
    <x-footer />
@endsection
