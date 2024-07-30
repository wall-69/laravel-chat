@extends('layouts.app')

@section('content')
    <main class="flex-shrink-0">
        <div class="container pt-3 mb-5">
            <h1 class="text-center"><a href="{{ route('index') }}">LaraChat</a></h1>
            <h2 class="fw-light text-body-secondary text-center">talk, share, connect</h2>
            <x-login-form class="w-lg-50" />
        </div>
    </main>
    <x-footer />
@endsection
