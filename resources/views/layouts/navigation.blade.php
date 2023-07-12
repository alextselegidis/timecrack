<div class="bg-body-tertiary">
    <div class="container">
        <div class="row">
            <div class="col">
                <nav class="navbar navbar-expand-lg bg-body-tertiary">
                    <a class="navbar-brand" href="{{route('dashboard')}}">Timecrack</a>

                    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#top-nav"
                            aria-controls="top-nav" aria-expanded="false" aria-label="Toggle navigation">
                        <span class="navbar-toggler-icon"></span>
                    </button>

                    <div class="collapse navbar-collapse ms-md-4" id="top-nav">
                        <ul class="navbar-nav me-auto mb-2 mb-lg-0 w-100">
                            <li class="nav-item">
                                <a class="nav-link me-md-3" href="{{route('dashboard')}}">
                                    {{__('Home')}}
                                </a>
                            </li>
                            @can('viewAny', \App\Models\Task::class)
                                <li class="nav-item">
                                    <a class="nav-link me-md-3" href="{{route('task.index')}}">
                                        {{__('Tasks')}}
                                    </a>
                                </li>
                            @endcan
                            @can('viewAny', \App\Models\Project::class)
                                <li class="nav-item">
                                    <a class="nav-link me-md-3" href="{{route('project.index')}}">
                                        {{__('Projects')}}
                                    </a>
                                </li>
                            @endcan
                            @can('viewAny', \App\Models\User::class)
                                <li class="nav-item">
                                    <a class="nav-link me-md-3" href="{{route('user.index')}}">
                                        {{__('Users')}}
                                    </a>
                                </li>
                            @endcan
                            <li class="nav-item dropdown ms-md-auto">
                                <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown"
                                   aria-expanded="false">
                                    {{ Auth::user()->full_name }}
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    {{-- TODO: Enable settings once they are ready. --}}
                                    {{-- <li hidden> --}}
                                    {{--     <a class="dropdown-item" href="{{route('setting.index')}}"> --}}
                                    {{--         {{__('Setting')}} --}}
                                    {{--     </a> --}}
                                    {{-- </li> --}}
                                    <li>
                                        <a class="dropdown-item" href="{{route('profile.edit')}}">
                                            {{__('Account')}}
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="{{route('about.index')}}">
                                            {{__('About')}}
                                        </a>
                                    </li>
                                    <li>
                                        <form action="{{route('logout')}}" method="POST">
                                            @csrf
                                            <button type="submit" class="dropdown-item" href="{{route('logout')}}">
                                                {{__('Log out')}}
                                            </button>
                                        </form>
                                    </li>
                                </ul>
                            </li>
                        </ul>
                    </div>
                </nav>
            </div>
        </div>
    </div>
</div>


