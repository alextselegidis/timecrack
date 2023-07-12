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

    </div>

</div>
