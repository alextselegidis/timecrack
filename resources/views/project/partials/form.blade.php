@php($project ??= new stdClass)

<div class="row">
    <div class="col-md-6 offset-md-3">

        <div class="mb-3">
            <label for="name" class="form-label">
                {{ __('Name') }}
                <span class="text-danger">*</span>
            </label>
            <input type="text" id="name" name="name" class="form-control" required
                   value="{{ old('name', $project->name ?? '') }}">
            @error('name')
            <span class="form-text text-danger">{{ $message }}</span>
            @enderror
        </div>

        <div class="mb-3">
            <label for="description" class="form-label">
                {{ __('Description') }}
            </label>
            <textarea id="description" name="description" class="form-control" rows="5">
                {{ old('description', $project->description ?? '') }}
            </textarea>
            @error('description')
            <span class="form-text text-danger">{{ $message }}</span>
            @enderror
        </div>

        <div class="mb-3">
            <label class="form-label">
                {{ __('Users') }}
            </label>

            @if($userOptions->isEmpty())
                <x-no-records-found/>
            @endif

            @foreach($userOptions as $userOption)
                <div class="form-check form-switch mb-2">
                    <input class="form-check-input" type="checkbox" role="switch"
                           id="project-user-{{$userOption->value}}" name="users[]" value="{{$userOption->value}}"
                           @if($project->users->contains($userOption->value)) checked @endif>
                    <label class="form-check-label small" for="project-user-{{$userOption->value}}">
                        {{$userOption->label}}
                    </label>
                </div>
            @endforeach

        </div>

    </div>

</div>
