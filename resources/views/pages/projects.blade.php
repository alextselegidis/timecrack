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
    {{ __('projects') }}
@endsection

@section('breadcrumbs')
    @include('shared.breadcrumb', ['breadcrumbs' => [
        ['label' => __('setup'), 'url' => route('setup.localization')],
        ['label' => __('projects')]
    ]])
@endsection

@section('navActions')
    <a href="#" class="nav-link" data-bs-toggle="modal" data-bs-target="#create-modal">
        <i class="bi bi-plus-square me-2"></i>
        {{ __('add') }}
    </a>
@endsection

@section('content')
    <div class="d-flex flex-column flex-lg-row gap-4">
        <!-- Sidebar -->
        <div class="flex-shrink-0" style="min-width: 200px;">
            @include('shared.setup-sidebar')
        </div>
        <!-- Main Content -->
        <div class="flex-grow-1">
            <!-- Search -->
            <form action="{{ route('setup.projects') }}" method="GET" class="mb-4">
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0">
                        <i class="bi bi-search text-muted"></i>
                    </span>
                    <input type="text" id="q" name="q" class="form-control bg-light border-start-0"
                           value="{{ $q }}"
                           placeholder="{{ __('search') }}..." style="max-width: 300px;">
                </div>
            </form>
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-body p-0">
                    <!-- Table -->
                    <div class="table-responsive" style="overflow: visible;">
                        <table class="table table-sm table-striped table-hover align-middle mb-0">
                            <thead class="table-dark">
                                <tr>
                                    <th class="border-0 ps-4">
                                        <a href="{{ route('setup.projects', ['sort' => 'name', 'direction' => request('direction') === 'asc' ? 'desc' : 'asc']) }}" class="text-decoration-none text-white">
                                            {{ __('name') }}
                                            @if(request('sort') === 'name')
                                                <i class="bi bi-chevron-{{ request('direction') === 'asc' ? 'up' : 'down' }} ms-1"></i>
                                            @endif
                                        </a>
                                    </th>
                                    <th class="border-0">{{ __('description') }}</th>
                                    <th class="border-0">{{ __('users') }}</th>
                                    <th class="border-0 pe-4 text-end" style="width: 100px;"></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($projects as $project)
                                    <tr onclick="window.location='{{ route('setup.projects.edit', $project->id) }}'" style="cursor: pointer;">
                                        <td class="border-0 ps-4">
                                            <span class="d-inline-block me-2" style="width: 12px; height: 12px; background-color: {{ $project->color ?? '#6c757d' }}; border-radius: 2px;"></span>
                                            <span class="fw-medium">{{ $project->name }}</span>
                                        </td>
                                        <td class="border-0">
                                            {{ Str::limit($project->description, 50) ?: '-' }}
                                        </td>
                                        <td class="border-0">
                                            <span class="badge bg-light text-dark">{{ $project->users->count() }}</span>
                                        </td>
                                        <td class="border-0 pe-4 text-end">
                                            <div class="dropdown" onclick="event.stopPropagation();">
                                                <button class="btn btn-sm btn-dark dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                                    {{ __('actions') }}
                                                </button>
                                                <ul class="dropdown-menu dropdown-menu-end">
                                                    <li>
                                                        <a href="{{ route('setup.projects.edit', ['project' => $project->id]) }}" class="dropdown-item">
                                                            <i class="bi bi-pencil me-2"></i>{{ __('edit') }}
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <form action="{{ route('setup.projects.destroy', $project->id) }}"
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
                                    </tr>
                                @endforeach
                                @if($projects->isEmpty())
                                    <tr>
                                        <td colspan="4" class="border-0 text-center text-muted py-5">
                                            <i class="bi bi-inbox display-4 d-block mb-3"></i>
                                            {{ __('no_records_found') }}
                                        </td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @if($projects->hasPages())
                <div class="d-flex justify-content-center mt-4">
                    {{ $projects->withQueryString()->links('pagination::bootstrap-5') }}
                </div>
            @endif
        </div>
    </div>
    @include('modals.create-modal', ['route' => route('setup.projects.store')])
@endsection
