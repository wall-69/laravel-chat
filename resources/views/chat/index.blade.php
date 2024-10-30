@extends('layouts.app')

@section('content')
    <x-chat-nav />

    <main>
        <chat :current-user="{{ auth()->user() }}" :chat-order="{{ json_encode($chatOrder) }}"></chat>
    </main>

    <x-footer />
@endsection
