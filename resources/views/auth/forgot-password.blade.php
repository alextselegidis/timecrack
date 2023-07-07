<x-guest-layout>
    <p class="text-muted">
        {{ __('forgotYourPasswordMessage') }}
    </p>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')"/>

    <form method="POST" action="{{ route('password.email') }}">
        @csrf

        <!-- Email Address -->
        <div class="mb-3">
            <x-input-label for="email" :value="__('email')"/>
            <x-text-input id="email" type="email" name="email" :value="old('email')" required autofocus/>
            <x-input-error :messages="$errors->get('email')" class="mt-2"/>
        </div>

        <div class="d-flex justify-content-between align-items-center">
            <a class="btn btn-link px-0" href="{{ route('login') }}">
                {{ __('backToLogIn') }}
            </a>

            <x-primary-button>
                {{ __('emailPasswordResetLink') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
