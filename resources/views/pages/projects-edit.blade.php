{{--
/* ----------------------------------------------------------------------------
 * Timecrack - Time Tracking Application
 *
 * @package     Timecrack
 * @author      A.Tselegidis <alextselegidis@gmail.com>
 * @copyright   Copyright (c) Alex Tselegidis
 * @license     https://opensource.org/licenses/GPL-3.0 - GPLv3
 * @link        https://github.com/alextselegidis/timecrack
 * ---------------------------------------------------------------------------- */
--}}
@extends('layouts.main-layout')
@section('pageTitle')
    {{ $project->name }}
@endsection
@section('breadcrumbs')
    @include('shared.breadcrumb', ['breadcrumbs' => [
        ['label' => __('setup'), 'url' => route('setup.projects')],
        ['label' => __('projects'), 'url' => session('projects_list_url', route('setup.projects'))],
        ['label' => $project->name]
    ]])
@endsection
@section('navActions')
    <a href="#" class="nav-link me-lg-3" data-bs-toggle="modal" data-bs-target="#create-modal">
        <i class="bi bi-plus-square me-2"></i>
        {{ __('add') }}
    </a>
    <form action="{{ route('setup.projects.destroy', $project->id) }}"
          method="POST"
          onsubmit="return confirm('{{ __('delete_record_prompt') }}')">
        @csrf
        @method('DELETE')
        <button type="submit" class="nav-link">
            <i class="bi bi-trash me-2"></i>
            {{ __('delete') }}
        </button>
    </form>
@endsection
@section('content')
    <div class="d-flex flex-column flex-lg-row gap-4">
        <!-- Edit Sidebar -->
        <div class="flex-shrink-0" style="min-width: 180px;">
            @include('shared.edit-sidebar', ['items' => [
                ['label' => __('details'), 'route' => 'setup.projects.edit', 'params' => ['project' => $project->id], 'icon' => 'file-text']
            ]])
        </div>
        <!-- Main Content -->
        <div class="flex-grow-1">
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-body p-4">
                    <form action="{{ route('setup.projects.update', ['project' => $project->id]) }}" method="POST" id="edit-form">
                        @csrf
                        @method('PUT')
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="mb-3">
                                    <label for="name" class="form-label text-dark small fw-medium">
                                        <span class="text-danger">*</span> {{ __('name') }}
                                    </label>
                                    <input type="text" id="name" name="name" class="form-control" required
                                           value="{{ old('name', $project?->name ?? null) }}">
                                    @error('name')
                                    <span class="form-text text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="mb-3">
                                    <label for="description" class="form-label text-dark small fw-medium">
                                        {{ __('description') }}
                                    </label>
                                    <textarea id="description" name="description" class="form-control" rows="3">{{ old('description', $project?->description ?? null) }}</textarea>
                                    @error('description')
                                    <span class="form-text text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="mb-3">
                                    <label for="color" class="form-label text-dark small fw-medium">
                                        {{ __('color') }}
                                    </label>
                                    <input type="color" id="color" name="color" class="form-control form-control-color"
                                           value="{{ old('color', $project?->color ?? '#0d6efd') }}">
                                    @error('color')
                                    <span class="form-text text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="mb-3">
                                    <label class="form-label text-dark small fw-medium">{{ __('users') }}</label>
                                    <div class="border rounded p-3" style="max-height: 250px; overflow-y: auto;">
                                        @forelse($users as $user)
                                            <div class="form-check">
                                                <input type="checkbox" name="users[]" value="{{ $user->id }}" class="form-check-input" id="user-{{ $user->id }}"
                                                       @if(in_array($user->id, old('users', $selectedUserIds))) checked @endif>
                                                <label class="form-check-label" for="user-{{ $user->id }}">
                                                    {{ $user->name }} <span class="text-muted small">({{ $user->email }})</span>
                                                </label>
                                            </div>
                                        @empty
                                            <p class="text-muted mb-0">{{ __('no_records_found') }}</p>
                                        @endforelse
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <!-- Card Footer with Save Button -->
                <div class="card-footer bg-body-secondary border-top text-end py-3 px-4">
                    <button type="submit" form="edit-form" class="btn btn-dark">
                        {{ __('save') }}
                    </button>
                </div>
            </div>
        </div>
    </div>
    @include('modals.create-modal', ['route' => route('setup.projects.store')])
@endsection
