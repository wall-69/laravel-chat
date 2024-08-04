@extends('layouts.app')

@section('content')
    <main class="flex-shrink-0">
        <div class="container pt-3">
            <h1>LaraChat</h1>
            <h2 class="fw-light text-body-secondary">talk, share, connect</h2>
            <div class="row row-md g-0 g-lg-5 align-items-center">
                <div class="col py-5 align-items-start">
                    @auth
                        <div
                            class="ms-lg-0 w-md-75 h-100 container rounded-5 d-flex flex-column align-items-center gap-3 px-5 py-3 bg-secondary text-white text-center">
                            <h3 class="py-3 fw-light">You are already logged in!</h3>
                            <a href="{{ route('chat.index') }}">
                                <button type="submit" class="btn btn-accent rounded-0 text-white fw-bold fs-5 px-4 py-1 shadow">
                                    Go to LaraChat
                                </button>
                            </a>
                        </div>
                    @else
                        <x-login-form class="ms-lg-0 w-md-75" />
                    @endauth
                </div>
                <div class="col d-lg-block d-none">
                    <img src="/img/chatting.svg" alt="Image of a mobile phone on which a chat is opened." class="img-fluid">
                </div>
            </div>
        </div>
    </main>
    <x-footer />
@endsection
