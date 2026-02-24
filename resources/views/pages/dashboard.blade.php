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
    {{__('Dashboard')}}
@endsection
@section('breadcrumbs')
    @include('shared.breadcrumb', ['breadcrumbs' => [
        ['label' => __('Dashboard')]
    ]])
@endsection
@section('content')
    {{-- ACTIVE TIMER --}}
    @if($user->isTracking())
        @php $activeTracking = $user->activeTracking; @endphp
        <div class="card border-primary mb-4 shadow-sm">
            <div class="card-header bg-primary text-white d-flex align-items-center">
                <i class="bi bi-stopwatch me-2 fs-4"></i>
                <span class="fs-5 fw-bold">{{ __('Active Timer') }}</span>
                <span class="ms-auto badge bg-light text-primary fs-6" id="timer-display">
                    00:00:00
                </span>
            </div>
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-md-4 mb-3 mb-md-0">
                        <h5 class="mb-1">
                            <span class="badge" style="background-color: {{ $activeTracking->project->color ?? '#6c757d' }}">
                                {{ $activeTracking->project->name ?? __('Unknown Project') }}
                            </span>
                        </h5>
                        <small class="text-muted">
                            {{ __('Started at') }}: {{ $activeTracking->started_at->format('H:i:s') }}
                        </small>
                    </div>
                    <div class="col-md-5 mb-3 mb-md-0">
                        <form action="{{ route('timer.message') }}" method="POST" class="d-flex gap-2">
                            @csrf
                            <input type="text" name="message" class="form-control" placeholder="{{ __('What are you working on?') }}" value="{{ $activeTracking->message }}">
                            <button type="submit" class="btn btn-outline-secondary" title="{{ __('Save Message') }}">
                                <i class="bi bi-check"></i>
                            </button>
                        </form>
                    </div>
                    <div class="col-md-3 text-md-end">
                        <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#stop-timer-modal" title="{{ __('Stop') }}">
                            <i class="bi bi-stop-fill me-1"></i>
                            {{ __('Stop') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- PROJECTS --}}
    <h5 class="mb-3">
        <i class="bi bi-folder me-2"></i>
        {{ __('Projects') }}
    </h5>

    @if($projects->count())
        @php
            $pinnedProjects = $projects->filter(fn($p) => in_array($p->id, $pinnedIds));
            $unpinnedProjects = $projects->filter(fn($p) => !in_array($p->id, $pinnedIds));
            $visibleUnpinned = $unpinnedProjects->take(4 - $pinnedProjects->count());
            $hiddenProjects = $unpinnedProjects->skip(4 - $pinnedProjects->count());
            $showMore = $hiddenProjects->count() > 0;
        @endphp

        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 row-cols-xl-4 g-4 mb-4">
            {{-- Pinned Projects --}}
            @foreach($pinnedProjects as $project)
                <div class="col">
                    <div class="card h-100 shadow-sm card-hover-move" style="border-left: 4px solid {{ $project->color ?? '#0d6efd' }};">
                        <div class="card-body d-flex flex-column">
                            <div class="d-flex justify-content-between align-items-start">
                                <h5 class="card-title mb-0">{{ $project->name }}</h5>
                                <form action="{{ route('projects.toggle-pin', $project) }}" method="POST" class="ms-2">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-link p-0 text-warning" title="{{ __('Unpin') }}">
                                        <i class="bi bi-pin-fill"></i>
                                    </button>
                                </form>
                            </div>
                            @if($project->description)
                                <p class="card-text text-muted small flex-grow-1 mt-2">{{ Str::limit($project->description, 100) }}</p>
                            @endif
                            <div class="mt-auto">
                                @if($user->isTracking() && $user->activeTracking->project_id === $project->id)
                                    <span class="badge bg-primary">
                                        <i class="bi bi-stopwatch me-1"></i>
                                        {{ __('Currently Tracking') }}
                                    </span>
                                @elseif(!$user->isTracking())
                                    <form action="{{ route('timer.start', $project) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn btn-outline-primary btn-sm">
                                            <i class="bi bi-play-fill me-1"></i>
                                            {{ __('Start Timer') }}
                                        </button>
                                    </form>
                                @else
                                    <span class="text-muted small">{{ __('Stop current timer to start') }}</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach

            {{-- Visible Unpinned Projects --}}
            @foreach($visibleUnpinned as $project)
                <div class="col">
                    <div class="card h-100 shadow-sm card-hover-move" style="border-left: 4px solid {{ $project->color ?? '#0d6efd' }};">
                        <div class="card-body d-flex flex-column">
                            <div class="d-flex justify-content-between align-items-start">
                                <h5 class="card-title mb-0">{{ $project->name }}</h5>
                                <form action="{{ route('projects.toggle-pin', $project) }}" method="POST" class="ms-2">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-link p-0 text-muted" title="{{ __('Pin') }}">
                                        <i class="bi bi-pin"></i>
                                    </button>
                                </form>
                            </div>
                            @if($project->description)
                                <p class="card-text text-muted small flex-grow-1 mt-2">{{ Str::limit($project->description, 100) }}</p>
                            @endif
                            <div class="mt-auto">
                                @if($user->isTracking() && $user->activeTracking->project_id === $project->id)
                                    <span class="badge bg-primary">
                                        <i class="bi bi-stopwatch me-1"></i>
                                        {{ __('Currently Tracking') }}
                                    </span>
                                @elseif(!$user->isTracking())
                                    <form action="{{ route('timer.start', $project) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn btn-outline-primary btn-sm">
                                            <i class="bi bi-play-fill me-1"></i>
                                            {{ __('Start Timer') }}
                                        </button>
                                    </form>
                                @else
                                    <span class="text-muted small">{{ __('Stop current timer to start') }}</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Collapsible Hidden Projects --}}
        @if($showMore)
            <div class="mb-4">
                <button class="btn btn-outline-secondary btn-sm" type="button" data-bs-toggle="collapse" data-bs-target="#more-projects" aria-expanded="false">
                    <i class="bi bi-chevron-down me-1"></i>
                    {{ __('Show more') }} ({{ $hiddenProjects->count() }})
                </button>
                <div class="collapse mt-3" id="more-projects">
                    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 row-cols-xl-4 g-4">
                        @foreach($hiddenProjects as $project)
                            <div class="col">
                                <div class="card h-100 shadow-sm card-hover-move" style="border-left: 4px solid {{ $project->color ?? '#0d6efd' }};">
                                    <div class="card-body d-flex flex-column">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <h5 class="card-title mb-0">{{ $project->name }}</h5>
                                            <form action="{{ route('projects.toggle-pin', $project) }}" method="POST" class="ms-2">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-link p-0 text-muted" title="{{ __('Pin') }}">
                                                    <i class="bi bi-pin"></i>
                                                </button>
                                            </form>
                                        </div>
                                        @if($project->description)
                                            <p class="card-text text-muted small flex-grow-1 mt-2">{{ Str::limit($project->description, 100) }}</p>
                                        @endif
                                        <div class="mt-auto">
                                            @if($user->isTracking() && $user->activeTracking->project_id === $project->id)
                                                <span class="badge bg-primary">
                                                    <i class="bi bi-stopwatch me-1"></i>
                                                    {{ __('Currently Tracking') }}
                                                </span>
                                            @elseif(!$user->isTracking())
                                                <form action="{{ route('timer.start', $project) }}" method="POST">
                                                    @csrf
                                                    <button type="submit" class="btn btn-outline-primary btn-sm">
                                                        <i class="bi bi-play-fill me-1"></i>
                                                        {{ __('Start Timer') }}
                                                    </button>
                                                </form>
                                            @else
                                                <span class="text-muted small">{{ __('Stop current timer to start') }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif
    @else
        <div class="text-center my-5 py-5">
            <div class="mb-4">
                <i class="bi bi-folder-x display-1 text-muted"></i>
            </div>
            <h4 class="text-muted">{{ __('No projects available') }}</h4>
            <p class="text-muted">{{ __('Contact your administrator to get access to projects.') }}</p>
        </div>
    @endif

    {{-- RECENT TRACKINGS --}}
    @if($trackings->count())
        <h5 class="mb-3 mt-5">
            <i class="bi bi-clock-history me-2"></i>
            {{ __('Recent Trackings') }}
        </h5>

        <div class="card border-0 shadow-sm rounded-3">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-sm table-striped table-hover align-middle mb-0">
                        <thead class="table-dark">
                            <tr>
                                <th class="border-0 ps-4">{{ __('project') }}</th>
                                <th class="border-0">{{ __('started') }}</th>
                                <th class="border-0">{{ __('duration') }}</th>
                                <th class="border-0">{{ __('billable') }}</th>
                                <th class="border-0">{{ __('non_billable') }}</th>
                                <th class="border-0 pe-4">{{ __('message') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($trackings as $tracking)
                                <tr>
                                    <td class="border-0 ps-4">
                                        <span class="badge" style="background-color: {{ $tracking->project->color ?? '#6c757d' }}">
                                            {{ $tracking->project->name ?? __('unknown') }}
                                        </span>
                                    </td>
                                    <td class="border-0">{{ $tracking->started_at->format('d/m/Y H:i') }}</td>
                                    <td class="border-0" data-bs-toggle="tooltip" data-bs-title="{{ $tracking->duration_decimal }}">{{ $tracking->duration }}</td>
                                    <td class="border-0" @if($tracking->billable_hours !== null) data-bs-toggle="tooltip" data-bs-title="{{ number_format($tracking->billable_hours, 2) }}h" @endif>
                                        @if($tracking->billable_hours !== null)
                                            @php
                                                $bh = $tracking->billable_hours;
                                                $bhHours = (int) floor($bh);
                                                $bhMinutes = (int) round(($bh - $bhHours) * 60);
                                            @endphp
                                            {{ $bhHours }}h {{ $bhMinutes }}m
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td class="border-0" @php $nbh = ($tracking->duration_seconds / 3600) - ($tracking->billable_hours ?? 0); @endphp @if($nbh > 0) data-bs-toggle="tooltip" data-bs-title="{{ number_format($nbh, 2) }}h" @endif>
                                        @if($nbh > 0)
                                            @php
                                                $nbhHours = (int) floor($nbh);
                                                $nbhMinutes = (int) round(($nbh - $nbhHours) * 60);
                                            @endphp
                                            {{ $nbhHours }}h {{ $nbhMinutes }}m
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td class="border-0 pe-4" @if($tracking->message && strlen($tracking->message) > 30) data-bs-toggle="tooltip" data-bs-title="{{ e($tracking->message) }}" @endif>
                                        {{ Str::limit($tracking->message, 30) ?: '-' }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="text-center mt-4">
            <a href="{{ route('trackings') }}" class="btn btn-outline-primary">
                {{ __('View All History') }}
            </a>
        </div>
    @endif

    {{-- STOP TIMER MODAL --}}
    @if($user->isTracking())
        @php $activeTracking = $user->activeTracking; @endphp
        <div class="modal fade" id="stop-timer-modal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form action="{{ route('timer.stop') }}" method="POST">
                        @csrf
                        <div class="modal-header">
                            <h5 class="modal-title">{{ __('Stop Timer') }}</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <p>{{ __('You are about to stop the timer for:') }} <span class="badge" style="background-color: {{ $activeTracking->project->color ?? '#6c757d' }}">{{ $activeTracking->project->name ?? __('Unknown') }}</span></p>
                            <div class="mb-3">
                                <label for="stop-message" class="form-label">{{ __('Message (optional)') }}</label>
                                <textarea name="message" id="stop-message" class="form-control" rows="3" placeholder="{{ __('What did you work on?') }}">{{ $activeTracking->message }}</textarea>
                            </div>
                            <div class="mb-3">
                                <label for="billable_hours" class="form-label">{{ __('billable_hours') }}</label>
                                <input type="number" name="billable_hours" id="billable_hours" class="form-control" step="0.01" min="0" placeholder="0.00">
                                <small class="form-text text-muted">{{ __('Defaults to elapsed duration if left empty') }}</small>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                            <button type="submit" class="btn btn-danger">
                                <i class="bi bi-stop-fill me-1"></i>
                                {{ __('Stop Timer') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
@endsection
@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize Bootstrap tooltips
            const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
            const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl));
        });
    </script>
    @if($user->isTracking())
        @php $activeTracking = $user->activeTracking; @endphp
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const startedAt = new Date('{{ $activeTracking->started_at->toIso8601String() }}');
                const timerDisplay = document.getElementById('timer-display');
                const billableHoursInput = document.getElementById('billable_hours');

                function getElapsedHours() {
                    const now = new Date();
                    let elapsed = Math.max(0, (now - startedAt) / 1000);
                    return (elapsed / 3600).toFixed(2);
                }

                function updateTimer() {
                    const now = new Date();
                    let elapsed = Math.floor((now - startedAt) / 1000);
                    if (elapsed < 0) elapsed = 0;

                    const hours = Math.floor(elapsed / 3600);
                    const minutes = Math.floor((elapsed % 3600) / 60);
                    const seconds = elapsed % 60;

                    timerDisplay.textContent =
                        String(hours).padStart(2, '0') + ':' +
                        String(minutes).padStart(2, '0') + ':' +
                        String(seconds).padStart(2, '0');
                }

                updateTimer();
                setInterval(updateTimer, 1000);

                // Set default billable hours when modal opens
                const stopModal = document.getElementById('stop-timer-modal');
                if (stopModal) {
                    stopModal.addEventListener('show.bs.modal', function() {
                        billableHoursInput.value = getElapsedHours();
                    });
                }
            });
        </script>
    @endif
@endsection
