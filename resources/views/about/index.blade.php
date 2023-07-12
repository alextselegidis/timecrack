<x-app-layout>
    <x-slot name="pageTitle">
        {{ __('About') }}
    </x-slot>

    <div class="container">
        <div class="row">
            <div class="col">
                <p>
                    Self hosted application for time tracking, allowing multiple users of a company to track their time
                    easily.
                </p>

                <div class="card mb-3">
                    <div class="card-body">
                        <strong>
                        {{__('Application Version')}}
                        </strong>:
                        {{config('app.version')}}
                    </div>
                </div>

                <h5>
                    {{__('Useful Links')}}
                </h5>

                <p>
                    <a href="https://timecrack.org" target="_blank">
                        https://timecrack.org
                    </a>
                </p>

                <p>
                    <a href="https://github.com/alextselegidis/timecrack" target="_blank">
                        https://github.com/alextselegidis/timecrack
                    </a>
                </p>
            </div>
        </div>
    </div>
</x-app-layout>
