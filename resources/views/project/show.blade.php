<x-app-layout>
    <x-slot name="pageTitle">
        {{ __('Project') . ': ' . $project->name }}
    </x-slot>

    <x-slot name="header">
        <div class="d-flex justify-content-start align-items-center">
            <a href="{{route('project.index')}}"
               class="btn btn-outline-primary btn-sm me-2">
                {{__('Back')}}
            </a>

            <form action="{{route('project.destroy', $project->id)}}" method="POST"
                  onsubmit="return confirm('{{__('Are you sure that you want to delete this record?')}}')" class="me-2">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-outline-primary btn-sm">
                    {{__('Delete')}}
                </button>
            </form>

            <a href="{{route('project.edit', $project->id)}}"
               class="btn btn-primary btn-sm">
                {{__('Edit')}}
            </a>
        </div>
    </x-slot>

    <div class="container">
        <div class="row">
            <div class="col">

                <p>
                    <strong>{{__('Description')}}</strong>: {{$project->description ?? 'N/A'}}
                </p>

                {{-- TODO: Display a table with the latest tasks of this project. --}}

            </div>
        </div>
    </div>
</x-app-layout>
