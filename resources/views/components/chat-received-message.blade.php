@props(['userId', 'message'])

<div class="me-auto bg-primary rounded-4 my-1 received-message-shadow d-flex align-items-center gap-2 px-2 py-1">
    <img src="{{ asset(App\Models\User::find($userId)->profile_picture) }}" alt="" width="35" height="35"
        class="bg-white rounded-circle align-self-start">
    <p class="m-0 text-white">
        {{ $message }}
    </p>
</div>
