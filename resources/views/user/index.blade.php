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

                <form action="{{route('user.index')}}" class="mb-3">
                    @csrf
                    <div class="mb-3 d-flex justify-content-start align-items-center table-search-container">
                        <input type="text" id="q" name="q" class="form-control" value="{{$q}}"
                               placeholder="{{__('Search')}}" aria-label="{{__('Search')}}" autofocus>
                    </div>
                </form>

                <div class="table-responsive overflow-visible mb-3">
                    <table class="table table-sm small table-hover table-striped align-middle">
                        <thead>
                        <tr>
                            <th style="width: 30%">
                                {{__('Name')}}
                            </th>
                            <th style="width: 20%">
                                {{__('Created')}}
                            </th>
                            <th style="width: 20%">
                                {{__('Updated')}}
                            </th>
                            <th style="width: 30%">
                                {{__('Email')}}
                            </th>
                            <th style="width: 20%">
                                {{__('Phone')}}
                            </th>
                            <th style="width: 10%">
                                {{__('Role')}}
                            </th>
                            <th style="width: 20%">
                                {{__('Actions')}}
                            </th>
                        </tr>
                        </thead>
                        <tbody>
                        @if($users->isEmpty())
                            <x-no-records-found/>
                        @endif
                        @foreach($users as $user)
                            <tr>
                                <td>
                                    {{$user->full_name}}
                                </td>
                                <td>
                                    {{$user->created_at->format('d.m.Y H:i')}}
                                </td>
                                <td>
                                    {{$user->updated_at->format('d.m.Y H:i')}}
                                </td>
                                <td>
                                    {{$user->email}}
                                </td>
                                <td>
                                    {{$user->phone}}
                                </td>
                                <td>
                                    {{__(ucfirst($user->role))}}
                                </td>
                                <td class="text-end">
                                    @canany(['update', 'delete'], $user)
                                        <div class="btn-group">
                                            <a href="{{route('user.show', $user->id)}}"
                                               class="btn btn-outline-primary btn-sm">
                                                {{__('View')}}
                                            </a>
                                            <button type="button"
                                                    class="btn btn-outline-primary dropdown-toggle dropdown-toggle-split btn-sm"
                                                    data-bs-toggle="dropdown" aria-expanded="false">
                                                <span class="visually-hidden">{{__('Toggle Dropdown')}}</span>
                                            </button>
                                            <ul class="dropdown-menu">
                                                @can('update', $user)
                                                    <li>
                                                        <a class="dropdown-item"
                                                           href="{{route('user.edit', $user->id)}}">
                                                            {{__('Edit')}}
                                                        </a>
                                                    </li>
                                                @endcan
                                                @can('delete', $user)
                                                    <li>
                                                        <form action="{{route('user.destroy', $user->id)}}"
                                                              method="POST"
                                                              onsubmit="return confirm('{{__('Are you sure that you want to delete this record?')}}')">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="dropdown-item">
                                                                {{__('Delete')}}
                                                            </button>
                                                        </form>

                                                    </li>
                                                @endcan
                                            </ul>
                                        </div>
                                    @else
                                        N/A
                                    @endcanany


                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="text-center">
                    {{ $users->links() }}
                </div>

            </div>
        </div>
    </div>
</x-app-layout>
