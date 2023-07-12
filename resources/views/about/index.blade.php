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

                <div class="card">
                    <div class="card-body">
                        <strong>
                        {{__('Application Version')}}
                        </strong>:
                        {{config('app.version')}}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
