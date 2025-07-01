<x-mail::message>
    # Welcome to {{ config('app.name') }}!

    Here are your login credentials:

    **Email:** {{ $user->email }}
    **Password:** {{ $password }}

    Please log in and change your password immediately for security reasons.

    <x-mail::button :url="route('login')">
        Login to Your Account
    </x-mail::button>

    Thanks,<br>
    {{ config('app.name') }} Team
</x-mail::message>
