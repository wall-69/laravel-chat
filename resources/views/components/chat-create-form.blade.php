<form
    {{ $attributes->merge([
        'class' =>
            'container rounded-5 d-flex flex-column align-items-center gap-3 px-5 py-3 bg-secondary text-white text-center',
    ]) }}
    method="POST" action="{{ route('chats.store') }}" enctype="multipart/form-data">
    @method('POST')
    @csrf

    <div>
        <h3 class="py-3 fw-light mb-0">Create a new channel</h3>
        <p>
            A channel is a place for people to talk, share and connect. <br>
            After creating this channel, you will become an admin.
        </p>
    </div>

    {{-- Users --}}
    <input type="hidden" name="users[]" value="{{ auth()->user()->id }}">

    {{-- Name --}}
    <div class="w-100 d-flex flex-column">
        <label for="name" class="text-start fw-bold">name:</label>
        <input type="text" name="name" class="py-2 px-1" value="{{ old('name') }}">
        @error('name')
            <p class="text-warning fw-bold text-start">{{ $message }}</p>
        @enderror
    </div>

    {{-- Is private --}}
    <div class="w-100 d-flex flex-column">
        <label for="is_private" class="text-start fw-bold">visibility:</label>
        <select name="is_private" class="py-2 px-1">
            <option value="0" @selected(old('is_private') == 0)>Public</option>
            <option value="1" @selected(old('is_private') == 1)>Private</option>
        </select>
        @error('is_private')
            <p class="text-warning fw-bold text-start">{{ $message }}</p>
        @enderror
    </div>

    {{-- Chat picture --}}
    <div class="w-100 d-flex flex-column">
        <label for="chat_picture" class="text-start fw-bold">chat picture:</label>
        <input type="file" name="chat_picture" class="py-2 px-1 text-white" value="{{ old('chat_picture') }}"
            accept="image/*">
        @error('chat_picture')
            <p class="text-warning fw-bold text-start">{{ $message }}</p>
        @enderror
    </div>

    <button type="submit" class="btn btn-accent rounded-0 text-white fw-bold fs-5 px-4 py-1 shadow">Create</button>
</form>
