@extends('layouts.app')

@section('content')
    <x-chat-nav />
    <main class="flex-shrink-0 h-100">
        <div class="px-2 px-lg-0 container-lg py-5 h-100">
            <div class="row h-100 gx-0 gx-lg-5">
                <div
                    class="d-none d-lg-block col-3 h-100 rounded-3 bg-secondary bg-gradient shadow p-0 overflow-x-scroll overflow-y-scroll">
                    @foreach (auth()->user()->userChats as $userChat)
                        <x-chat-tab type="unread" :userChat="$userChat" />
                    @endforeach
                </div>
                <div class="col-12 col-lg-9 mh-100">
                    <x-chat :userChat="$currentChat" />
                </div>
            </div>
        </div>
    </main>
    <x-footer />
@endsection
