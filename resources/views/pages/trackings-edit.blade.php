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
@php $isNew = !$tracking->exists; @endphp
@extends('layouts.main-layout')
@section('pageTitle')
    @if($isNew)
        {{ __('create') }} {{ __('tracking') }}
    @else
        {{ __('tracking') }} #{{ $tracking->id }}
    @endif
@endsection
@section('breadcrumbs')
    @include('shared.breadcrumb', ['breadcrumbs' => [
        ['label' => __('history'), 'url' => route('trackings')],
        ['label' => $isNew ? __('create') : __('tracking') . ' #' . $tracking->id]
    ]])
@endsection
@section('navActions')
    @unless($isNew)
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
    @endunless
@endsection
@section('content')
    <div class="d-flex flex-column flex-lg-row gap-4">
        @unless($isNew)
        <!-- Edit Sidebar -->
        <div class="flex-shrink-0" style="min-width: 180px;">
            @include('shared.edit-sidebar', ['items' => [
                ['label' => __('details'), 'route' => 'trackings.edit', 'params' => ['tracking' => $tracking->id], 'icon' => 'file-text']
            ]])
        </div>
        @endunless
        <!-- Main Content -->
        <div class="flex-grow-1">
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-body p-4">
                    <form action="{{ $isNew ? route('trackings.store') : route('trackings.update', ['tracking' => $tracking->id]) }}" method="POST" id="edit-form">
                        @csrf
                        @unless($isNew)
                            @method('PUT')
                        @endunless
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
                                <div class="mb-3">
                                    <label for="billable_hours" class="form-label text-dark small fw-medium">
                                        {{ __('billable_hours') }}
                                    </label>
                                    <div class="input-group">
                                        <input type="number" id="billable_hours" name="billable_hours" class="form-control"
                                               step="0.01" min="0"
                                               value="{{ old('billable_hours', $tracking?->billable_hours) }}"
                                               placeholder="0.00">
                                        <button type="button" class="btn btn-outline-secondary" id="reset-billable-hours" title="{{ __('Reset to elapsed duration') }}">
                                            <i class="bi bi-arrow-counterclockwise"></i>
                                        </button>
                                    </div>
                                    @error('billable_hours')
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
@endsection
@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const startedAt = document.getElementById('started_at');
            const endedAt = document.getElementById('ended_at');
            const billableHours = document.getElementById('billable_hours');
            const resetBtn = document.getElementById('reset-billable-hours');
            let manuallyEdited = false;

            billableHours.addEventListener('input', function () {
                manuallyEdited = true;
            });

            function calculateBillableHours() {
                if (!startedAt.value || !endedAt.value) return;
                const start = new Date(startedAt.value);
                const end = new Date(endedAt.value);
                const diffMs = end - start;
                if (diffMs > 0) {
                    billableHours.value = (Math.floor(diffMs / 36000) / 100).toFixed(2);
                }
            }

            function autoCalculate() {
                if (manuallyEdited) return;
                calculateBillableHours();
            }

            startedAt.addEventListener('change', autoCalculate);
            endedAt.addEventListener('change', autoCalculate);

            resetBtn.addEventListener('click', function () {
                manuallyEdited = false;
                calculateBillableHours();
            });
        });
    </script>
@endsection
