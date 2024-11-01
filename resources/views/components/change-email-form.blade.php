<form class="px-5 py-4 bg-secondary rounded-4 d-flex flex-column gap-2">
    <h3 class="text-center text-white fw-normal border-bottom border-divider pb-1 border-opacity-25">
        Change email
    </h3>

    {{-- New email --}}
    <label for="email" class="text-start text-white fw-bold">new email:</label>
    <input type="email" name="email" class="py-2 px-1" value="{{ old('email') }}">
    @error('email')
        <p class="text-warning fw-bold text-start">{{ $message }}</p>
    @enderror

    {{-- Password --}}
    <label for="password" class="text-start text-white fw-bold">password:</label>
    <input type="password" name="password" class="py-2 px-1" value="{{ old('password') }}">
    @error('password')
        <p class="text-warning fw-bold text-start">{{ $message }}</p>
    @enderror

    <div class="mx-auto">
        <button type="submit" class="btn btn-accent rounded-0 text-white fw-bold fs-5 px-4 py-1 shadow">
            Change
        </button>
    </div>
</form>
