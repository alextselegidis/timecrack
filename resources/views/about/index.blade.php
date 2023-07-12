<x-app-layout>
    <x-slot name="header">
        <h2>
            {{ __('about') }}
        </h2>
    </x-slot>

    <div class="container">
        <div class="row">
            <div class="col">
                <h6>
                    Timecrack
                </h6>

                <p>
                    Self hosted application for time tracking, allowing multiple users of a company to track their time easily.
                </p>

                <div class="card">
                    <div class="card-body">
                        {{config('app.version')}}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
