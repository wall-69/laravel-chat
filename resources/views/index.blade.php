@extends('layouts/app')

@section('content')
    <main class="flex-shrink-0">
        <div class="container pt-3">
            <h1>LaraChat</h1>
            <h2 class="fw-light text-body-secondary">talk, share, connect</h2>
            <div class="row row-md g-0 g-lg-5 align-items-end">
                <div class="col py-5 align-items-start">
                    <form
                        class="container w-md-75 h-100 ms-xxl-0 rounded-5 d-flex flex-column align-items-center gap-3 px-5 py-3 bg-secondary text-white text-center">
                        <h3 class="py-3 fw-light">Login to LaraChat</h3>

                        <div class="w-100 d-flex flex-column">
                            <label for="email" class="text-start fw-bold">email:</label>
                            <input type="text" name="email" class="py-2 px-1">
                        </div>

                        <div class="w-100 d-flex flex-column">
                            <label for="password" class="text-start fw-bold">password:</label>
                            <input type="password" name="password" class="py-2 px-1">
                        </div>

                        <button type="submit"
                            class="btn btn-accent rounded-0 text-white fw-bold fs-5 px-4 py-1 shadow">Login</button>

                        <p class="mt-auto">
                            Don't have an account yet? <a href="#" class="fw-bold text-white">Register here.</a>
                        </p>
                    </form>
                </div>
                <div class="col d-xxl-block d-none">
                    <img src="img/chatting.svg" alt="Image of a mobile phone on which a chat is opened." class="img-fluid">
                </div>
            </div>
        </div>
    </main>
    <x-footer />
@endsection
