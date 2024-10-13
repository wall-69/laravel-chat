@extends('layouts.app')

@section('content')
    <x-chat-nav />

    <main class="flex-shrink-0">
        <div class="container pt-3 mb-5">
            <x-chat-create-form class="w-lg-50" />
        </div>
    </main>

    <x-footer />
@endsection
