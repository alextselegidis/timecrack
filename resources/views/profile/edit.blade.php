<x-app-layout>
    <x-slot name="header">
        <h2>
            {{ __('Profile') }}
        </h2>
    </x-slot>

    <div class="container">
        <div class="row">
            <div class="col">
                <div>
                    <div>
                        <div>
                            @include('profile.partials.update-profile-information-form')
                        </div>
                    </div>

                    <div>
                        <div>
                            @include('profile.partials.update-password-form')
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


</x-app-layout>
