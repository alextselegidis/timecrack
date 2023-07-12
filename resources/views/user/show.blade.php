<x-app-layout>
    <x-slot name="pageTitle">
        {{ __('User') . ': ' . $user->full_name }}
    </x-slot>

    <x-slot name="header">
        <div class="d-flex justify-content-start align-items-center">
            <a href="{{route('user.index')}}"
               class="btn btn-outline-primary btn-sm me-2">
                {{__('Back')}}
            </a>

            <form action="{{route('user.destroy', $user->id)}}" method="POST"
                  onsubmit="return confirm('{{__('Are you sure that you want to delete this record?')}}')" class="me-2">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-outline-primary btn-sm">
                    {{__('Delete')}}
                </button>
            </form>

            <a href="{{route('user.edit', $user->id)}}"
               class="btn btn-primary btn-sm">
                {{__('Edit')}}
            </a>
        </div>
    </x-slot>

    <div class="container">
        <div class="row">
            <div class="col">

                <p>
                    <strong>{{__('Email')}}</strong>: <a href="mailto:{{$user->email}}">{{$user->email}}</a>
                </p>

                <p>
                    <strong>{{__('Phone')}}</strong>:
                    @if($user->phone)
                        <a href="tel:{{$user->phone}}">{{$user->phone}}</a>
                    @else
                        N/A
                    @endif
                </p>

                <p>
                    <strong>{{__('Phone (Alt)')}}</strong>: {{$user->full_address ?: 'N/A'}}
                </p>

                <p>
                    <strong>{{__('Address')}}</strong>: {{$user->full_address ?: 'N/A'}}
                </p>

                <p>
                    <strong>{{__('Gender')}}</strong>: {{$user->gender ? __(ucfirst($user->gender)) : 'N/A'}}
                </p>

                <p>
                    <strong>{{__('Role')}}</strong>: {{__(ucfirst($user->role))}}
                </p>

                {{-- TODO: Display a table with the latest tasks of this user. --}}

            </div>
        </div>
    </div>
</x-app-layout>
