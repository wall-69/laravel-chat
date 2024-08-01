@extends('layouts.app')

@section('content')
    <x-chat-nav />
    <main class="flex-shrink-0 h-75">
        <div class="container py-5 h-100">
            <div class="row h-100">
                <div class="col-3 h-100 rounded-3 bg-secondary bg-gradient shadow p-0"
                    style="overflow-x: hidden; overflow-y: scroll">
                    {{-- <h4 class="text-white text-center fw-light">Your chats</h4> --}}

                    @foreach (auth()->user()->all() as $user)
                        <x-chat-tab type="unread" :user="$user" />
                    @endforeach
                </div>
                <div class="col-9 overflow-scroll">yay</div>
            </div>
        </div>
    </main>
    <x-footer />
@endsection
