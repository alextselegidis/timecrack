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
    {{ __('tracking') }} #{{ $tracking->id }}
@endsection
@section('breadcrumbs')
    @include('shared.breadcrumb', ['breadcrumbs' => [
        ['label' => __('history'), 'url' => session('trackings_list_url', route('trackings'))],
        ['label' => __('tracking') . ' #' . $tracking->id]
    ]])
@endsection
@section('navActions')
    <a href="#" class="nav-link me-lg-3" data-bs-toggle="modal" data-bs-target="#create-modal">
        <i class="bi bi-plus-square me-2"></i>
        {{ __('add') }}
    </a>
    <form action="{{ route('trackings.destroy', $tracking->id) }}"
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
                ['label' => __('details'), 'route' => 'trackings.edit', 'params' => ['tracking' => $tracking->id], 'icon' => 'file-text']
            ]])
        </div>
        <!-- Main Content -->
        <div class="flex-grow-1">
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-body p-4">
                    <form action="{{ route('trackings.update', ['tracking' => $tracking->id]) }}" method="POST" id="edit-form">
                        @csrf
                        @method('PUT')
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="mb-3">
                                    <label for="project_id" class="form-label text-dark small fw-medium">
                                        {{ __('project') }} <span class="text-danger">*</span>
                                    </label>
                                    <select name="project_id" id="project_id" class="form-select" required>
                                        <option value="">{{ __('select') }}...</option>
                                        @foreach($projects as $project)
                                            <option value="{{ $project->id }}" @if(old('project_id', $tracking->project_id) == $project->id) selected @endif>
                                                {{ $project->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('project_id')
                                    <span class="form-text text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="mb-3">
                                    <label for="user_id" class="form-label text-dark small fw-medium">
                                        {{ __('user') }} <span class="text-danger">*</span>
                                    </label>
                                    <select name="user_id" id="user_id" class="form-select" required>
                                        <option value="">{{ __('select') }}...</option>
                                        @foreach($users as $user)
                                            <option value="{{ $user->id }}" @if(old('user_id', $tracking->user_id) == $user->id) selected @endif>
                                                {{ $user->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('user_id')
                                    <span class="form-text text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="mb-3">
                                    <label for="message" class="form-label text-dark small fw-medium">
                                        {{ __('message') }}
                                    </label>
                                    <textarea id="message" name="message" class="form-control" rows="3">{{ old('message', $tracking?->message ?? null) }}</textarea>
                                    @error('message')
                                    <span class="form-text text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="mb-3">
                                    <label for="started_at" class="form-label text-dark small fw-medium">
                                        {{ __('started_at') }} <span class="text-danger">*</span>
                                    </label>
                                    <input type="datetime-local" id="started_at" name="started_at" class="form-control" required
                                           value="{{ old('started_at', $tracking?->started_at?->format('Y-m-d\TH:i')) }}">
                                    @error('started_at')
                                    <span class="form-text text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="mb-3">
                                    <label for="ended_at" class="form-label text-dark small fw-medium">
                                        {{ __('ended_at') }} <span class="text-danger">*</span>
                                    </label>
                                    <input type="datetime-local" id="ended_at" name="ended_at" class="form-control" required
                                           value="{{ old('ended_at', $tracking?->ended_at?->format('Y-m-d\TH:i')) }}">
                                    @error('ended_at')
                                    <span class="form-text text-danger">{{ $message }}</span>
                                    @enderror
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
    @include('modals.create-modal', ['route' => route('trackings.store')])
@endsection
