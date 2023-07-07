<x-app-layout>
    <x-slot name="header">
        <h2 class="text-muted">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="row">
        <div class="col">
            {{ __("You're logged in!") }}
        </div>
    </div>
</x-app-layout>
