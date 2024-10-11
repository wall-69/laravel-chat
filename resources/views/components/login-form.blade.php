<form
    {{ $attributes->merge([
        'class' =>
            'container rounded-5 d-flex flex-column align-items-center gap-3 px-5 py-3 bg-secondary text-white text-center',
    ]) }}
    method="POST" action="{{ route('users.login') }}">
    @method('POST')
    @csrf

    <h3 class="py-3 fw-light">Login to LaraChat</h3>

    {{-- Email --}}
    <div class="w-100 d-flex flex-column">
        <label for="email" class="text-start fw-bold">email:</label>
        <input type="email" name="email" class="py-2 px-1" value="{{ old('email') }}">
        @error('email')
            <p class="text-warning fw-bold text-start">{{ $message }}</p>
        @enderror
    </div>

    {{-- Password --}}
    <div class="w-100 d-flex flex-column">
        <label for="password" class="text-start fw-bold">password:</label>
        <input type="password" name="password" class="py-2 px-1" value="{{ old('password') }}">
        @error('password')
            <p class="text-warning fw-bold text-start">{{ $message }}</p>
        @enderror
    </div>

    <button type="submit" class="btn btn-accent rounded-0 text-white fw-bold fs-5 px-4 py-1 shadow">Login</button>

    <p class="mt-auto">
        Don't have an account yet? <a href="{{ route('register') }}" class="fw-bold text-white">Register here.</a>
    </p>
</form>
