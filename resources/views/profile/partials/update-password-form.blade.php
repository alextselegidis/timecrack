<section>
    <header>
        <h2>
            {{ __('Update Password') }}
        </h2>

        <p>
            {{ __('Ensure your account is using a long, random password to stay secure.') }}
        </p>
    </header>

    <form method="post" action="{{ route('password.update') }}">
        @csrf
        @method('put')

        <div>
            <x-input-label for="current_password" :value="__('Current Password')"/>
            <x-text-input id="current_password" name="current_password" type="password"
                          autocomplete="current-password"/>
            <x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-2"/>
        </div>

        <div>
            <x-input-label for="password" :value="__('New Password')"/>
            <x-text-input id="password" name="password" type="password" autocomplete="new-password"/>
            <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-2"/>
        </div>

        <div>
            <x-input-label for="password_confirmation" :value="__('Confirm Password')"/>
            <x-text-input id="password_confirmation" name="password_confirmation" type="password"
                          autocomplete="new-password"/>
            <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" class="mt-2"/>
        </div>

        <div>
            <x-primary-button>{{ __('Save') }}</x-primary-button>

            @if (session('status') === 'password-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                >
                    {{ __('Saved.') }}
                </p>
            @endif
        </div>
    </form>
</section>
