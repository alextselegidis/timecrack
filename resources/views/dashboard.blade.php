<x-app-layout>
    <x-slot name="pageTitle">
            {{ __('Dashboard') }}
    </x-slot>

    <div class="container">
    <div class="row">
        <div class="col">
            {{ __("You're logged in!") }}

{{--      todo: render the tracking form      --}}
        </div>
    </div>
    </div>
</x-app-layout>
