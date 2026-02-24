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
    {{ __('history') }}
@endsection

@section('breadcrumbs')
    @include('shared.breadcrumb', ['breadcrumbs' => [
        ['label' => __('history')]
    ]])
@endsection

@section('navActions')
    @if($isAdmin)
        <a href="{{ route('trackings.create') }}" class="nav-link">
            <i class="bi bi-plus-square me-2"></i>
            {{ __('add') }}
        </a>
    @endif
@endsection

@section('content')
    <div class="d-flex flex-column flex-lg-row gap-4">
        <!-- Main Content -->
        <div class="flex-grow-1">
            <!-- Search and Filters -->
            <form action="{{ route('trackings') }}" method="GET" class="mb-4">
                <div class="row g-2 align-items-end">
                    <div class="col-auto">
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0">
                                <i class="bi bi-search text-muted"></i>
                            </span>
                            <input type="text" id="q" name="q" class="form-control bg-light border-start-0"
                                   value="{{ $q }}"
                                   placeholder="{{ __('search') }}..." style="width: 200px;">
                        </div>
                    </div>
                    <div class="col-auto">
                        <label for="date_from" class="form-label small text-muted mb-1">{{ __('from') }}</label>
                        <input type="date" id="date_from" name="date_from" class="form-control bg-light"
                               value="{{ $dateFrom }}">
                    </div>
                    <div class="col-auto">
                        <label for="date_to" class="form-label small text-muted mb-1">{{ __('to') }}</label>
                        <input type="date" id="date_to" name="date_to" class="form-control bg-light"
                               value="{{ $dateTo }}">
                    </div>
                    <div class="col-auto">
                        <button type="submit" class="btn btn-dark">
                            <i class="bi bi-funnel"></i>
                            {{ __('filter') }}
                        </button>
                    </div>
                    @if($q || $dateFrom || $dateTo || !empty($userIds))
                        <div class="col-auto">
                            <a href="{{ route('trackings') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-x-lg"></i>
                                {{ __('clear') }}
                            </a>
                        </div>
                    @endif
                    @if($isAdmin)
                        <div class="col-auto">
                            <button type="button" class="btn btn-outline-dark" data-bs-toggle="modal" data-bs-target="#user-filter-modal">
                                <i class="bi bi-people me-1"></i>
                                {{ __('users') }}
                                @if(!empty($userIds))
                                    <span class="badge bg-primary ms-1">{{ count($userIds) }}</span>
                                @endif
                            </button>
                            @foreach($userIds as $userId)
                                <input type="hidden" name="user_ids[]" value="{{ $userId }}">
                            @endforeach
                        </div>
                    @endif
                    <div class="col-auto ms-auto">
                        <a href="{{ route('trackings.export', request()->query()) }}" class="btn btn-outline-dark">
                            <i class="bi bi-download me-1"></i>
                            {{ __('export_csv') }}
                        </a>
                    </div>
                </div>
            </form>
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-body p-0">
                    <!-- Table -->
                    <div class="table-responsive" style="overflow: visible;">
                        <table class="table table-striped table-hover align-middle mb-0">
                            <thead class="table-dark">
                                <tr>
                                    @php
                                        $currentSort = request('sort', 'started_at');
                                        $currentDirection = request('direction', 'desc');
                                        $queryParams = request()->except(['sort', 'direction']);
                                    @endphp
                                    <th class="border-0 ps-4">
                                        <a href="{{ route('trackings', array_merge($queryParams, ['sort' => 'project', 'direction' => $currentSort === 'project' && $currentDirection === 'asc' ? 'desc' : 'asc'])) }}" class="text-decoration-none text-white">
                                            {{ __('project') }}
                                            @if($currentSort === 'project')
                                                <i class="bi bi-chevron-{{ $currentDirection === 'asc' ? 'up' : 'down' }} ms-1"></i>
                                            @endif
                                        </a>
                                    </th>
                                    @if($isAdmin)
                                        <th class="border-0">
                                            <a href="{{ route('trackings', array_merge($queryParams, ['sort' => 'user', 'direction' => $currentSort === 'user' && $currentDirection === 'asc' ? 'desc' : 'asc'])) }}" class="text-decoration-none text-white">
                                                {{ __('user') }}
                                                @if($currentSort === 'user')
                                                    <i class="bi bi-chevron-{{ $currentDirection === 'asc' ? 'up' : 'down' }} ms-1"></i>
                                                @endif
                                            </a>
                                        </th>
                                    @endif
                                    <th class="border-0">
                                        <a href="{{ route('trackings', array_merge($queryParams, ['sort' => 'started_at', 'direction' => $currentSort === 'started_at' && $currentDirection === 'asc' ? 'desc' : 'asc'])) }}" class="text-decoration-none text-white">
                                            {{ __('started') }}
                                            @if($currentSort === 'started_at')
                                                <i class="bi bi-chevron-{{ $currentDirection === 'asc' ? 'up' : 'down' }} ms-1"></i>
                                            @endif
                                        </a>
                                    </th>
                                    <th class="border-0">
                                        <a href="{{ route('trackings', array_merge($queryParams, ['sort' => 'ended_at', 'direction' => $currentSort === 'ended_at' && $currentDirection === 'asc' ? 'desc' : 'asc'])) }}" class="text-decoration-none text-white">
                                            {{ __('ended') }}
                                            @if($currentSort === 'ended_at')
                                                <i class="bi bi-chevron-{{ $currentDirection === 'asc' ? 'up' : 'down' }} ms-1"></i>
                                            @endif
                                        </a>
                                    </th>
                                    <th class="border-0">{{ __('duration') }}</th>
                                    <th class="border-0">{{ __('billable_hours') }}</th>
                                    <th class="border-0">{{ __('message') }}</th>
                                    @if($isAdmin)
                                        <th class="border-0 pe-4 text-end" style="width: 100px;"></th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($trackings as $tracking)
                                    <tr data-tracking-id="{{ $tracking->id }}" @if($isAdmin) onclick="window.location='{{ route('trackings.edit', $tracking->id) }}'" style="cursor: pointer;" @endif>
                                        <td class="border-0 ps-4" onclick="event.stopPropagation();">
                                            @if($tracking->project)
                                                <a href="{{ route('setup.projects.edit', $tracking->project->id) }}" class="text-decoration-none">
                                                    <span class="badge" style="background-color: {{ $tracking->project->color ?? '#6c757d' }}">
                                                        {{ $tracking->project->name }}
                                                    </span>
                                                </a>
                                            @else
                                                <span class="badge bg-secondary">{{ __('unknown') }}</span>
                                            @endif
                                        </td>
                                        @if($isAdmin)
                                            <td class="border-0" onclick="event.stopPropagation();">
                                                @if($tracking->user)
                                                    <a href="{{ route('setup.users.edit', $tracking->user->id) }}" class="text-decoration-none">
                                                        {{ $tracking->user->name }}
                                                    </a>
                                                @else
                                                    {{ __('unknown') }}
                                                @endif
                                            </td>
                                        @endif
                                        <td class="border-0">{{ $tracking->started_at->format('M d, Y H:i') }}</td>
                                        <td class="border-0">{{ $tracking->ended_at->format('M d, Y H:i') }}</td>
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
                                        <td class="border-0">
                                            @if($tracking->message)
                                                {{ Str::limit($tracking->message, 30) }}
                                                <button type="button" class="btn btn-sm btn-link p-0 ms-1 copy-message-btn"
                                                        data-message="{{ e($tracking->message) }}"
                                                        data-tracking-id="{{ $tracking->id }}"
                                                        title="{{ __('copy_to_clipboard') }}"
                                                        onclick="event.stopPropagation();">
                                                    <i class="bi bi-clipboard"></i>
                                                </button>
                                            @else
                                                -
                                            @endif
                                        </td>
                                        @if($isAdmin)
                                            <td class="border-0 pe-4 text-end">
                                                <div class="dropdown" onclick="event.stopPropagation();">
                                                    <button class="btn btn-sm btn-dark dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                                        {{ __('actions') }}
                                                    </button>
                                                    <ul class="dropdown-menu dropdown-menu-end">
                                                        <li>
                                                            <a href="{{ route('trackings.edit', ['tracking' => $tracking->id]) }}" class="dropdown-item">
                                                                <i class="bi bi-pencil me-2"></i>{{ __('edit') }}
                                                            </a>
                                                        </li>
                                                        <li>
                                                            <form action="{{ route('trackings.destroy', $tracking->id) }}"
                                                                  method="POST"
                                                                  onsubmit="return confirm('{{ __('delete_record_prompt') }}')">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="dropdown-item text-danger">
                                                                    <i class="bi bi-trash me-2"></i>{{ __('delete') }}
                                                                </button>
                                                            </form>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </td>
                                        @endif
                                    </tr>
                                @endforeach
                                @if($trackings->isEmpty())
                                    <tr>
                                        <td colspan="{{ $isAdmin ? 8 : 6 }}" class="border-0 text-center text-muted py-5">
                                            <i class="bi bi-inbox display-4 d-block mb-3"></i>
                                            {{ __('no_records_found') }}
                                        </td>
                                    </tr>
                                @endif
                            </tbody>
                            @if($trackings->isNotEmpty())
                                <tfoot class="table-light">
                                    <tr>
                                        <td class="border-0 ps-4 fw-bold" colspan="{{ $isAdmin ? 4 : 3 }}">{{ __('total') }} ({{ $trackings->total() }} {{ __('records') }})</td>
                                        <td class="border-0 fw-bold" data-bs-toggle="tooltip" data-bs-title="{{ number_format($totalDurationSeconds / 3600, 2) }}h">
                                            @php
                                                $totalHours = $totalDurationSeconds / 3600;
                                                $totalDurH = (int) floor($totalHours);
                                                $totalDurM = (int) round(($totalHours - $totalDurH) * 60);
                                            @endphp
                                            {{ $totalDurH }}h {{ $totalDurM }}m
                                        </td>
                                        <td class="border-0 fw-bold" data-bs-toggle="tooltip" data-bs-title="{{ number_format($totalBillableHours, 2) }}h">
                                            @php
                                                $totalBillH = (int) floor($totalBillableHours);
                                                $totalBillM = (int) round(($totalBillableHours - $totalBillH) * 60);
                                            @endphp
                                            {{ $totalBillH }}h {{ $totalBillM }}m
                                        </td>
                                        <td class="border-0" colspan="{{ $isAdmin ? 2 : 1 }}"></td>
                                    </tr>
                                </tfoot>
                            @endif
                        </table>
                    </div>
                </div>
            </div>
            @if($trackings->hasPages())
                <div class="d-flex justify-content-center mt-3 pagination">
                    {{ $trackings->withQueryString()->links('pagination::bootstrap-5') }}
                </div>
            @endif
        </div>
    </div>
    @if($isAdmin)
        <!-- User Filter Modal -->
        <div class="modal fade" id="user-filter-modal" tabindex="-1" aria-labelledby="user-filter-modal-label" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="user-filter-modal-label">{{ __('filter_by_users') }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="{{ __('close') }}"></button>
                    </div>
                    <form action="{{ route('trackings') }}" method="GET" id="user-filter-form">
                        <!-- Preserve other filters -->
                        @if($q)
                            <input type="hidden" name="q" value="{{ $q }}">
                        @endif
                        @if($dateFrom)
                            <input type="hidden" name="date_from" value="{{ $dateFrom }}">
                        @endif
                        @if($dateTo)
                            <input type="hidden" name="date_to" value="{{ $dateTo }}">
                        @endif
                        @if(request('sort'))
                            <input type="hidden" name="sort" value="{{ request('sort') }}">
                        @endif
                        @if(request('direction'))
                            <input type="hidden" name="direction" value="{{ request('direction') }}">
                        @endif
                        <div class="modal-body">
                            <div class="mb-3">
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" id="select-all-users">
                                    <label class="form-check-label fw-bold" for="select-all-users">{{ __('select_all') }}</label>
                                </div>
                                <hr>
                                <div style="max-height: 300px; overflow-y: auto;">
                                    @foreach($users as $filterUser)
                                        <div class="form-check mb-2">
                                            <input class="form-check-input user-checkbox" type="checkbox"
                                                   name="user_ids[]"
                                                   value="{{ $filterUser->id }}"
                                                   id="user-{{ $filterUser->id }}"
                                                   {{ in_array($filterUser->id, $userIds ?? []) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="user-{{ $filterUser->id }}">
                                                {{ $filterUser->name }}
                                            </label>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">{{ __('cancel') }}</button>
                            <button type="submit" class="btn btn-dark">{{ __('apply') }}</button>
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

    let highlightedRow = null;

    document.querySelectorAll('.copy-message-btn').forEach(function(btn) {
        btn.addEventListener('click', function() {
            const message = this.getAttribute('data-message');
            const trackingId = this.getAttribute('data-tracking-id');

            navigator.clipboard.writeText(message).then(function() {
                const icon = btn.querySelector('i');
                icon.classList.remove('bi-clipboard');
                icon.classList.add('bi-clipboard-check');

                // Remove highlight from previous row
                if (highlightedRow) {
                    highlightedRow.classList.remove('row-highlighted');
                }

                // Highlight current row
                const currentRow = document.querySelector(`tr[data-tracking-id="${trackingId}"]`);
                if (currentRow) {
                    currentRow.classList.add('row-highlighted');
                    highlightedRow = currentRow;
                }

                // Show toast
                showToast('{{ __("copied_to_clipboard") }}');

                setTimeout(function() {
                    icon.classList.remove('bi-clipboard-check');
                    icon.classList.add('bi-clipboard');
                }, 1500);
            });
        });
    });

    // Select all users checkbox
    const selectAllCheckbox = document.getElementById('select-all-users');
    const userCheckboxes = document.querySelectorAll('.user-checkbox');

    if (selectAllCheckbox) {
        // Update select all state based on current selections
        function updateSelectAllState() {
            const allChecked = userCheckboxes.length > 0 &&
                Array.from(userCheckboxes).every(cb => cb.checked);
            selectAllCheckbox.checked = allChecked;
            selectAllCheckbox.indeterminate = !allChecked &&
                Array.from(userCheckboxes).some(cb => cb.checked);
        }

        selectAllCheckbox.addEventListener('change', function() {
            userCheckboxes.forEach(cb => cb.checked = this.checked);
        });

        userCheckboxes.forEach(cb => {
            cb.addEventListener('change', updateSelectAllState);
        });

        updateSelectAllState();
    }
});

function showToast(message) {
    const toastContainer = document.querySelector('.toast-container');
    const toast = document.createElement('div');
    toast.className = 'toast align-items-center text-bg-success border-0 show mb-2';
    toast.setAttribute('role', 'alert');
    toast.setAttribute('aria-live', 'assertive');
    toast.setAttribute('aria-atomic', 'true');
    toast.innerHTML = `
        <div class="d-flex">
            <div class="toast-body">${message}</div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
    `;
    toastContainer.appendChild(toast);

    setTimeout(function() {
        toast.remove();
    }, 3000);
}
</script>
@endsection
