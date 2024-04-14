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
