<section>
    <header>
        <h2>
            {{ __('Profile Information') }}
        </h2>

        <p>
            {{ __("Update your account's profile information and email address.") }}
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}">
        @csrf
        @method('patch')

        <div>
            <x-input-label for="first-name" :value="__('First Name')"/>
            <x-text-input id="first-name" name="first_name" type="text" :value="old('first_name', $user->first_name)" required autofocus
                          autocomplete="first_name"/>
            <x-input-error class="mt-2" :messages="$errors->get('first_name')"/>
        </div>

        <div>
            <x-input-label for="last-name" :value="__('Last Name')"/>
            <x-text-input id="last-name" name="last_name" type="text" :value="old('last_name', $user->last_name)" required autofocus
                          autocomplete="last_name"/>
            <x-input-error class="mt-2" :messages="$errors->get('last_name')"/>
        </div>

        <div>
            <x-input-label for="email" :value="__('Email')"/>
            <x-text-input id="email" name="email" type="email" :value="old('email', $user->email)" required
                          autocomplete="username"/>
            <x-input-error class="mt-2" :messages="$errors->get('email')"/>

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div>
                    <p>
                        {{ __('Your email address is unverified.') }}

                        <button form="send-verification">
                            {{ __('Click here to re-send the verification email.') }}
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p>
                            {{ __('A new verification link has been sent to your email address.') }}
                        </p>
                    @endif
                </div>
            @endif
        </div>

        <div>
            <x-primary-button>{{ __('Save') }}</x-primary-button>

            @if (session('status') === 'profile-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                >{{ __('Saved.') }}</p>
            @endif
        </div>
    </form>
</section>
