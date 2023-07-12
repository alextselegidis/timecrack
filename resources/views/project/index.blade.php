<x-app-layout>
    <x-slot name="pageTitle">
        {{ __('Projects') }}
    </x-slot>

    <x-slot name="header">
        @can('create', \App\Models\Project::class)
            <a href="{{route('project.create')}}" class="btn btn-primary">
                {{__('Create')}}
            </a>
        @endcan
    </x-slot>

    <div class="container">
        <div class="row">
            <div class="col">

                <form action="{{route('project.index')}}" class="mb-3">
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
                                {{__('Actions')}}
                            </th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($projects as $project)
                            <tr>
                                <td>
                                    {{$project->name}}
                                </td>
                                <td>
                                    {{$project->created_at->format('d.m.Y H:i')}}
                                </td>
                                <td>
                                    {{$project->updated_at->format('d.m.Y H:i')}}
                                </td>
                                <td class="text-end">
                                    @canany(['update', 'delete'], $project)
                                        <div class="btn-group">
                                            <a href="{{route('project.show', $project->id)}}"
                                               class="btn btn-outline-primary btn-sm">
                                                {{__('View')}}
                                            </a>
                                            <button type="button"
                                                    class="btn btn-outline-primary dropdown-toggle dropdown-toggle-split btn-sm"
                                                    data-bs-toggle="dropdown" data-bs-boundary="document"
                                                    aria-expanded="false">
                                                <span class="visually-hidden">{{__('Toggle Dropdown')}}</span>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end">
                                                @can('update', $project)
                                                    <li>
                                                        <a class="dropdown-item"
                                                           href="{{route('project.edit', $project->id)}}">
                                                            {{__('Edit')}}
                                                        </a>
                                                    </li>
                                                @endcan
                                                @can('delete', $project)
                                                    <li>
                                                        <form action="{{route('project.destroy', $project->id)}}"
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
                    {{ $projects->links() }}
                </div>

            </div>
        </div>
    </div>
</x-app-layout>
