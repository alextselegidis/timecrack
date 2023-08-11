@php($task ??= new stdClass)

<div class="row">
    <div class="col-md-6 offset-md-3">

        <div class="mb-3">
            <label for="project-id" class="form-label">
                {{ __('Project') }}
                <span class="text-danger">*</span>
            </label>
            <select type="text" id="project-id" name="project_id" class="form-control" required>
                <option value=""></option>
                @foreach($projectOptions as $projectOption)
                    <option
                        value="{{$projectOption->value}}" {{ old('project_id', $task->project_id ?? NULL) === $projectOption->value ? 'selected' : '' }}>
                        {{$projectOption->label}}
                    </option>
                @endforeach
            </select>
            @error('project_id')
            <span class="form-text text-danger">{{ $message }}</span>
            @enderror
        </div>

        <div class="mb-3">
            <label for="user-id" class="form-label">
                {{ __('User') }}
                <span class="text-danger">*</span>
            </label>
            <select type="text" id="user-id" name="user_id" class="form-control" required>
                <option value=""></option>
                @foreach($userOptions as $userOption)
                    <option
                        value="{{$userOption->value}}" {{ old('user_id', $task->user_id ?? NULL) === $userOption->value ? 'selected' : '' }}>
                        {{$userOption->label}}
                    </option>
                @endforeach
            </select>
            @error('user_id')
            <span class="form-text text-danger">{{ $message }}</span>
            @enderror
        </div>

        <div class="mb-3">
            <label for="summary" class="form-label">
                {{ __('Summary') }}
                <span class="text-danger">*</span>
            </label>
            <input type="text" id="summary" name="summary" class="form-control" required
                   value="{{ old('summary', $task->summary ?? '') }}">
            @error('summary')
            <span class="form-text text-danger">{{ $message }}</span>
            @enderror
        </div>

        <div class="mb-3">
            <label for="started-at" class="form-label">
                {{ __('Started') }}
                <span class="text-danger">*</span>
            </label>
            <input type="datetime-local" id="started-at" name="started_at" class="form-control" required
                   value="{{ old('started_at', $task->started_at ?? '') }}">
            @error('started_at')
            <span class="form-text text-danger">{{ $message }}</span>
            @enderror
        </div>

        <div class="mb-3">
            <label for="ended-at" class="form-label">
                {{ __('Ended') }}
                <span class="text-danger">*</span>
            </label>
            <input type="datetime-local" id="ended-at" name="ended_at" class="form-control" required
                   value="{{ old('ended_at', $task->ended_at ?? '') }}">
            @error('ended_at')
            <span class="form-text text-danger">{{ $message }}</span>
            @enderror
        </div>

    </div>

</div>
