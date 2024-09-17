@extends('layouts.app')

@section('content')
    <x-chat-nav />
    <main class="">
        <chat :current-user="{{ auth()->user() }}" :current-chat="{{ $currentChat }}"
            :user-chats="{{ auth()->user()->userChats }}"></chat>
    </main>
    <x-footer />
@endsection
