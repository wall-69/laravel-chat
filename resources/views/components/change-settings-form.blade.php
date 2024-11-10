<form method="POST" action="{{ route('users.update', ['user' => auth()->user()->id]) }}"
    class="px-5 py-4 bg-secondary rounded-4 d-flex flex-column gap-2" enctype="multipart/form-data">
    @csrf
    @method('PATCH')
    <h3 class="text-center text-white fw-normal border-bottom border-divider pb-1 border-opacity-25">
        Settings
    </h3>

    {{-- Nickname --}}
    <label for="nickname" class="text-start text-white fw-bold">nickname:</label>
    <input type="nickname" name="nickname" class="py-2 px-1" value="{{ old('nickname') }}">
    @error('nickname')
        <p class="text-warning fw-bold text-start">{{ $message }}</p>
    @enderror

    {{-- Profile picture --}}
    <label for="profile_picture" class="text-start text-white fw-bold">profile picture:</label>
    <input type="file" name="profile_picture" class="py-2 px-1 text-white" value="{{ old('profile_picture') }}">
    @error('profile_picture')
        <p class="text-warning fw-bold text-start">{{ $message }}</p>
    @enderror

    <div class="mx-auto">
        <button type="submit" class="btn btn-accent rounded-0 text-white fw-bold fs-5 px-4 py-1 shadow">
            Save
        </button>
    </div>
</form>
