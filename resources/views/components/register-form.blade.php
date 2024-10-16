<form
    {{ $attributes->merge([
        'class' =>
            'container rounded-5 d-flex flex-column align-items-center gap-3 px-5 py-3 bg-secondary text-white text-center',
    ]) }}
    method="POST" action="{{ route('users.store') }}" enctype="multipart/form-data">
    @method('POST')
    @csrf

    <h3 class="py-3 fw-light">Register to LaraChat</h3>

    {{-- Nickname --}}
    <div class="w-100 d-flex flex-column">
        <label for="nickname" class="text-start fw-bold">nickname:</label>
        <input type="text" name="nickname" class="py-2 px-1" value="{{ old('nickname') }}">
        @error('nickname')
            <p class="text-warning fw-bold text-start">{{ $message }}</p>
        @enderror
    </div>

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

    {{-- Profile picture --}}
    <div class="w-100 d-flex flex-column">
        <label for="profile_picture" class="text-start fw-bold">profile picture:</label>
        <input type="file" name="profile_picture" class="py-2 px-1 text-white" value="{{ old('profile_picture') }}"
            accept="image/*">
        @error('profile_picture')
            <p class="text-warning fw-bold text-start">{{ $message }}</p>
        @enderror
    </div>

    <button type="submit" class="btn btn-accent rounded-0 text-white fw-bold fs-5 px-4 py-1 shadow">Register</button>

    <p class="mt-auto">
        Already have an account? <a href="{{ route('login') }}" class="fw-bold text-white">Login here.</a>
    </p>
</form>
