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
        <a href="#" class="nav-link" data-bs-toggle="modal" data-bs-target="#create-modal">
            <i class="bi bi-plus-square me-2"></i>
            {{ __('add') }}
        </a>
    @endif
@endsection

@section('content')
    <div class="d-flex flex-column flex-lg-row gap-4">
        <!-- Main Content -->
        <div class="flex-grow-1">
            <h5 class="fw-bold mb-3">{{ __('history') }}</h5>
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
                    @if($q || $dateFrom || $dateTo)
                        <div class="col-auto">
                            <a href="{{ route('trackings') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-x-lg"></i>
                                {{ __('clear') }}
                            </a>
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
                                    <th class="border-0 ps-4">
                                        <a href="{{ route('trackings', ['sort' => 'started_at', 'direction' => request('direction') === 'asc' ? 'desc' : 'asc']) }}" class="text-decoration-none text-white">
                                            {{ __('project') }}
                                            @if(request('sort') === 'started_at')
                                                <i class="bi bi-chevron-{{ request('direction') === 'asc' ? 'up' : 'down' }} ms-1"></i>
                                            @endif
                                        </a>
                                    </th>
                                    @if($isAdmin)
                                        <th class="border-0">{{ __('user') }}</th>
                                    @endif
                                    <th class="border-0">{{ __('started') }}</th>
                                    <th class="border-0">{{ __('ended') }}</th>
                                    <th class="border-0">{{ __('duration') }}</th>
                                    <th class="border-0">{{ __('message') }}</th>
                                    @if($isAdmin)
                                        <th class="border-0 pe-4 text-end" style="width: 100px;"></th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($trackings as $tracking)
                                    <tr @if($isAdmin) onclick="window.location='{{ route('trackings.edit', $tracking->id) }}'" style="cursor: pointer;" @endif>
                                        <td class="border-0 ps-4">
                                            <span class="badge" style="background-color: {{ $tracking->project->color ?? '#6c757d' }}">
                                                {{ $tracking->project->name ?? __('unknown') }}
                                            </span>
                                        </td>
                                        @if($isAdmin)
                                            <td class="border-0">{{ $tracking->user->name ?? __('unknown') }}</td>
                                        @endif
                                        <td class="border-0">{{ $tracking->started_at->format('M d, Y H:i') }}</td>
                                        <td class="border-0">{{ $tracking->ended_at->format('M d, Y H:i') }}</td>
                                        <td class="border-0">{{ $tracking->duration }}</td>
                                        <td class="border-0">
                                            @if($tracking->message)
                                                {{ Str::limit($tracking->message, 30) }}
                                                <button type="button" class="btn btn-sm btn-link p-0 ms-1 copy-message-btn" 
                                                        data-message="{{ e($tracking->message) }}"
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
                                        <td colspan="{{ $isAdmin ? 7 : 5 }}" class="border-0 text-center text-muted py-5">
                                            <i class="bi bi-inbox display-4 d-block mb-3"></i>
                                            {{ __('no_records_found') }}
                                        </td>
                                    </tr>
                                @endif
                            </tbody>
                            @if($trackings->isNotEmpty())
                                <tfoot class="table-light">
                                    <tr>
                                        <td class="border-0 ps-4 fw-bold" colspan="{{ $isAdmin ? 4 : 3 }}">{{ __('total') }}</td>
                                        <td class="border-0 fw-bold">
                                            @php
                                                $hours = floor($totalDurationSeconds / 3600);
                                                $minutes = floor(($totalDurationSeconds % 3600) / 60);
                                            @endphp
                                            {{ $hours }}h {{ $minutes }}m
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
                <div class="d-flex justify-content-center mt-4">
                    {{ $trackings->withQueryString()->links() }}
                </div>
            @endif
        </div>
    </div>
    @if($isAdmin)
        @include('modals.create-modal', ['route' => route('trackings.store')])
    @endif
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.copy-message-btn').forEach(function(btn) {
        btn.addEventListener('click', function() {
            const message = this.getAttribute('data-message');
            navigator.clipboard.writeText(message).then(function() {
                const icon = btn.querySelector('i');
                icon.classList.remove('bi-clipboard');
                icon.classList.add('bi-clipboard-check');
                
                // Show toast
                showToast('{{ __("copied_to_clipboard") }}');
                
                setTimeout(function() {
                    icon.classList.remove('bi-clipboard-check');
                    icon.classList.add('bi-clipboard');
                }, 1500);
            });
        });
    });
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
