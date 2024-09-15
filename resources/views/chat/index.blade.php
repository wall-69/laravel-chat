@extends('layouts.app')

@section('content')
    <x-chat-nav />
    <main class="flex-shrink-0 h-100">
        <chat :current-chat="{{ $currentChat }}" :user-chats="{{ auth()->user()->userChats }}"></chat>
    </main>
    <x-footer />
@endsection
