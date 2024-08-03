@extends('layouts.app')

@section('content')
    <x-chat-nav />
    <main class="flex-shrink-0 h-100">
        <div class="container py-5 h-100">
            <div class="row h-100 gx-5">
                <div class="col-3 h-100 rounded-3 bg-secondary bg-gradient shadow p-0 overflow-x-scroll overflow-y-scroll">
                    @foreach (auth()->user()->all() as $user)
                        <x-chat-tab type="unread" :user="$user" />
                    @endforeach
                </div>
                <div class="col-9 mh-100">
                    <x-chat :user="auth()->user()" />
                </div>
            </div>
        </div>
    </main>
    <x-footer />
@endsection
