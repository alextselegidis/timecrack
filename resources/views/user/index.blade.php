<x-app-layout>
    <x-slot name="pageTitle">
        {{ __('Users') }}
    </x-slot>

    <x-slot name="header">
        @can('create', \App\Models\User::class)
            <a href="{{route('user.create')}}" class="btn btn-primary">
                {{__('Create')}}
            </a>
        @endcan
    </x-slot>

    <div class="container">
        <div class="row">
            <div class="col">
                {{ $dataTable->table() }}
            </div>
        </div>
    </div>

    <x-slot name="scripts">
        {{ $dataTable->scripts() }}
    </x-slot>
</x-app-layout>
