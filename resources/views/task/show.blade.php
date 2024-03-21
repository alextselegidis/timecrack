<x-app-layout>
    <x-slot name="pageTitle">
        {{ __('Task') . ': ' . $task->summary }}
    </x-slot>

    <x-slot name="header">
        <div class="d-flex justify-content-start align-items-center">
            <a href="{{route('task.index')}}"
               class="btn btn-outline-primary btn-sm me-2">
                {{__('Back')}}
            </a>

            <form action="{{route('task.destroy', $task->id)}}" method="POST"
                  onsubmit="return confirm('{{__('Are you sure that you want to delete this record?')}}')" class="me-2">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-outline-primary btn-sm">
                    {{__('Delete')}}
                </button>
            </form>

            <a href="{{route('task.edit', $task->id)}}"
               class="btn btn-primary btn-sm">
                {{__('Edit')}}
            </a>
        </div>
    </x-slot>

    <div class="container">
        <div class="row">
            <div class="col">
                <p>
                    <strong>{{__('Project')}}</strong>: {{$task->project->name ?? 'N/A'}}
                </p>

                <p>
                    <strong>{{__('User')}}</strong>: {{$task->user->full_name ?? 'N/A'}}
                </p>

                <p>
                    <strong>{{__('External ID')}}</strong>: {{$task->external_id ?? 'N/A'}}
                </p>

                <p>
                    <strong>{{__('Started')}}</strong>: {{$task->started_at->format('d.m.Y H:i') ?? 'N/A'}}
                </p>

                <p>
                    <strong>{{__('Ended')}}</strong>: {{$task->started_at->format('d.m.Y H:i') ?? 'N/A'}}
                </p>

                <p>
                    <strong>{{__('Billable')}}</strong>: {{ __($task->is_billable ? 'Yes' : 'No') ?? 'N/A'}}
                </p>

                {{-- TODO: Display a table with the latest tasks of this task. --}}

            </div>
        </div>
    </div>
</x-app-layout>
