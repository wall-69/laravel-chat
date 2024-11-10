<form method="POST" action="{{ route('users.update', ['user' => auth()->user()->id]) }}"
    class="px-5 py-4 bg-secondary rounded-4 d-flex flex-column gap-2">
    @csrf
    @method('PATCH')

    <h3 class="text-center text-white fw-normal border-bottom border-divider pb-1 border-opacity-25">
        Change password
    </h3>

    {{-- New password --}}
    <label for="new_password" class="text-start text-white fw-bold">new password:</label>
    <input type="password" name="new_password" class="py-2 px-1" value="{{ old('new_password') }}">
    @error('new_password')
        <p class="text-warning fw-bold text-start">{{ $message }}</p>
    @enderror

    {{-- Password --}}
    <label for="password" class="text-start text-white fw-bold">current password:</label>
    <input type="password" name="password" class="py-2 px-1" value="{{ old('password') }}">
    @error('change_password')
        <p class="text-warning fw-bold text-start">{{ $message }}</p>
    @enderror

    <div class="mx-auto">
        <button type="submit" class="btn btn-accent rounded-0 text-white fw-bold fs-5 px-4 py-1 shadow">
            Change
        </button>
    </div>
</form>
