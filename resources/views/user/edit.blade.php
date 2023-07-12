<x-app-layout>
    <x-slot name="pageTitle">
        {{ __('Edit User') }}
    </x-slot>

    <div class="container">
        <div class="row">
            <div class="col">

                <form action="{{route('user.update', $user->id)}}" method="POST">
                    @csrf
                    @method('PUT')

                    @include('user.partials.form')

                    <div class="d-md-flex justify-content-md-end form-actions">
                        <button type="button" onclick="history.back()" class="btn btn-outline-primary me-2">
                            {{ __('Back') }}
                        </button>

                        <button type="submit" class="btn btn-primary">
                            {{__('Update')}}
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</x-app-layout>