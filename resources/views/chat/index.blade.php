@extends('layouts.app')

@section('content')
    <x-chat-nav />
    <main class="">
        <chat :current-user="{{ auth()->user() }}" :chat-order="{{ json_encode($chatOrder) }}"
            :user-chats="{{ auth()->user()->userChats()->with('chat')->get() }}"></chat>
    </main>
    <x-footer />
@endsection
