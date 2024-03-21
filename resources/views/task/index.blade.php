<x-app-layout>
    <x-slot name="pageTitle">
        {{ __('Tasks') }}
    </x-slot>

    <x-slot name="header">
        @can('create', \App\Models\Project::class)
            <a href="{{route('task.create')}}" class="btn btn-primary">
                {{__('Create')}}
            </a>
        @endcan
    </x-slot>

    <div class="container">
        <div class="row">
            <div class="col">

                <form action="{{route('task.index')}}" class="mb-3">
                    @csrf
                    <div class="mb-3 d-flex justify-content-start align-items-center table-search-container">
                        <input type="text" id="q" name="q" class="form-control" value="{{$q}}"
                               placeholder="{{__('Search')}}" aria-label="{{__('Search')}}" autofocus>
                    </div>
                </form>

                <div class="table-responsive overflow-visible mb-3">
                    <table class="table table-sm small table-hover table-striped align-middle" style="table-layout: fixed">
                        <thead>
                        <tr>
                            <th style="width: 30%">
                                {{__('Project')}}
                            </th>
                            <th style="width: 30%">
                                {{__('User')}}
                            </th>
                            <th style="width: 20%">
                                {{__('Created')}}
                            </th>
                            <th style="width: 20%">
                                {{__('Updated')}}
                            </th>
                            <th style="width: 50%">
                                {{__('Summary')}}
                            </th>
                            <th style="width: 15%">
                                {{__('E-ID')}}
                            </th>
                            <th style="width: 20%">
                                {{__('Started')}}
                            </th>
                            <th style="width: 20%">
                                {{__('Ended')}}
                            </th>
                            <th style="width: 20%">
                                {{__('Duration')}}
                            </th>
                            <th style="width: 15%">
                                {{__('Billable')}}
                            </th>
                            <th style="width: 20%">
                                <!-- -->
                            </th>
                        </tr>
                        </thead>
                        <tbody>
                        @if($tasks->isEmpty())
                            <x-no-records-found/>
                        @endif

                        @foreach($tasks as $task)
                            <tr>
                                <td>
                                    {{$task->project->name}}
                                </td>
                                <td>
                                    {{$task->user->full_name}}
                                </td>
                                <td>
                                    {{$task->created_at->format('d.m.Y H:i')}}
                                </td>
                                <td>
                                    {{$task->updated_at->format('d.m.Y H:i')}}
                                </td>
                                <td>
                                    {{$task->summary}}
                                </td>
                                <td>
                                    {{$task->external_id}}
                                </td>
                                <td>
                                    {{$task->started_at->format('d.m.Y H:i')}}
                                </td>
                                <td>
                                    {{$task->ended_at->format('d.m.Y H:i')}}
                                </td>
                                <td>
                                    {{ $task->duration }}
                                </td>
                                <td>
                                    {{ __($task->is_billable ? 'Yes' : 'No') }}
                                </td>
                                <td class="text-end">
                                    @canany(['update', 'delete'], $task)
                                        <div class="btn-group">
                                            <a href="{{route('task.show', $task->id)}}"
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
                                                @can('update', $task)
                                                    <li>
                                                        <a class="dropdown-item"
                                                           href="{{route('task.edit', $task->id)}}">
                                                            {{__('Edit')}}
                                                        </a>
                                                    </li>
                                                @endcan
                                                @can('delete', $task)
                                                    <li>
                                                        <form action="{{route('task.destroy', $task->id)}}"
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
                    {{ $tasks->links() }}
                </div>

            </div>
        </div>
    </div>
</x-app-layout>
