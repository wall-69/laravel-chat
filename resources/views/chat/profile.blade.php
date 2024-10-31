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
                            <form class="px-5 py-4 bg-secondary rounded-4 d-flex flex-column gap-2"
                                enctype="multipart/form-data">
                                <h3
                                    class="text-center text-white fw-normal border-bottom border-divider pb-1 border-opacity-25">
                                    Settings
                                </h3>

                                {{-- Nickname --}}
                                <label for="nickname" class="text-start text-white fw-bold">nickname:</label>
                                <input type="nickname" name="nickname" class="py-2 px-1" value="{{ old('nickname') }}">
                                @error('nickname')
                                    <p class="text-warning fw-bold text-start">{{ $message }}</p>
                                @enderror

                                {{-- Profile picture --}}
                                <label for="profile_picture" class="text-start text-white fw-bold">profile picture:</label>
                                <input type="file" name="profile_picture" class="py-2 px-1 text-white"
                                    value="{{ old('profile_picture') }}">
                                @error('profile_picture')
                                    <p class="text-warning fw-bold text-start">{{ $message }}</p>
                                @enderror

                                <div class="mx-auto">
                                    <button type="submit"
                                        class="btn btn-accent rounded-0 text-white fw-bold fs-5 px-4 py-1 shadow">
                                        Save
                                    </button>
                                </div>
                            </form>
                        @endif
                    </div>
                    <div class="d-flex flex-column gap-5">
                        @if ($isMyProfile)
                            {{-- Column 2 --}}
                            {{-- Change email --}}
                            <form class="px-5 py-4 bg-secondary rounded-4 d-flex flex-column gap-2">
                                <h3
                                    class="text-center text-white fw-normal border-bottom border-divider pb-1 border-opacity-25">
                                    Change email
                                </h3>

                                {{-- New email --}}
                                <label for="email" class="text-start text-white fw-bold">new email:</label>
                                <input type="email" name="email" class="py-2 px-1" value="{{ old('email') }}">
                                @error('email')
                                    <p class="text-warning fw-bold text-start">{{ $message }}</p>
                                @enderror

                                {{-- Password --}}
                                <label for="password" class="text-start text-white fw-bold">password:</label>
                                <input type="password" name="password" class="py-2 px-1" value="{{ old('password') }}">
                                @error('password')
                                    <p class="text-warning fw-bold text-start">{{ $message }}</p>
                                @enderror

                                <div class="mx-auto">
                                    <button type="submit"
                                        class="btn btn-accent rounded-0 text-white fw-bold fs-5 px-4 py-1 shadow">
                                        Change
                                    </button>
                                </div>
                            </form>

                            {{-- Change password --}}
                            <form class="px-5 py-4 bg-secondary rounded-4 d-flex flex-column gap-2">
                                <h3
                                    class="text-center text-white fw-normal border-bottom border-divider pb-1 border-opacity-25">
                                    Change password
                                </h3>

                                {{-- New password --}}
                                <label for="new_password" class="text-start text-white fw-bold">new password:</label>
                                <input type="password" name="new_password" class="py-2 px-1" value="{{ old('new_password') }}">
                                @error('new_password')
                                    <p class="text-warning fw-bold text-start">{{ $message }}</p>
                                @enderror

                                {{-- Password --}}
                                <label for="password" class="text-start text-white fw-bold">current password:</label>
                                <input type="password" name="password" class="py-2 px-1" value="{{ old('password') }}">
                                @error('password')
                                    <p class="text-warning fw-bold text-start">{{ $message }}</p>
                                @enderror

                                <div class="mx-auto">
                                    <button type="submit"
                                        class="btn btn-accent rounded-0 text-white fw-bold fs-5 px-4 py-1 shadow">
                                        Change
                                    </button>
                                </div>
                            </form>
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
                                    <form method="POST" action="{{ route('chat.store') }}">
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
