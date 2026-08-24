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
    {{__('About')}}
@endsection
@section('breadcrumbs')
    @include('shared.breadcrumb', ['breadcrumbs' => [
        ['label' => __('about')]
    ]])
@endsection
@section('content')
    <div>
        <div class="mx-auto my-5 text-center" style="max-width: 600px">
            <div class="mb-5">
                <img src="images/logo.png" alt="Logo" class="me-2" style="height: 128px">
            </div>
            <h1 class="fs-3 mb-5">
                Timecrack <span class="text-muted">v{{config('app.version')}}</span>
            </h1>
            <div class="mb-5 text-secondary">
                Timecrack is an open-source time tracking application that helps you track your work hours on
                different projects. Start, pause, and stop timers with a clean and intuitive interface, keeping
                your time records organized and easily retrievable.
            </div>
            <div class="d-flex gap-2 justify-content-center">
                <a href="https://github.com/alextselegidis/timecrack" class="btn btn-outline-primary btn-equal-width" target="_blank" style="min-width: 180px;">
                    <i class="bi bi-github me-2"></i>
                    GitHub
                </a>
                <a href="https://alextselegidis.com" class="btn btn-outline-secondary btn-equal-width" target="_blank" style="min-width: 180px;">
                    <img src="images/alextselegidis-logo-16x16.png" alt="logo" class="me-2"/>
                    alextselegidis.com
                </a>
            </div>
        </div>

        @if(count($posts))
            <div class="mx-auto mb-5" style="max-width: 600px">
                <hr class="my-5">
                <h2 class="fs-4 mb-4 text-center">
                    <i class="bi bi-journal-text me-2"></i>
                    {{ __('from_the_blog') }}
                </h2>
                <div class="list-group list-group-flush">
                    @foreach($posts as $post)
                        <a href="{{ $post['link'] }}" class="list-group-item list-group-item-action px-3 py-3 text-decoration-none" target="_blank" rel="noopener">
                            <div class="d-flex justify-content-between align-items-baseline gap-3">
                                <span class="fw-semibold text-dark">{{ $post['title'] }}</span>
                                <small class="text-muted text-nowrap">{{ $post['published_at']->isoFormat('LL') }}</small>
                            </div>
                            <div class="small text-secondary mt-1">{{ Str::limit($post['description'], 140) }}</div>
                        </a>
                    @endforeach
                </div>
                <div class="text-center mt-4">
                    <a href="https://timecrack.org/blog" class="btn btn-outline-secondary" target="_blank" rel="noopener">
                        <i class="bi bi-arrow-right me-2"></i>
                        {{ __('read_more_on_the_blog') }}
                    </a>
                </div>
            </div>
        @endif

        <div class="mx-auto mb-5 text-center" style="max-width: 600px">
            <hr class="my-5">
            <h2 class="fs-4 mb-4">
                <i class="bi bi-star-fill text-warning me-2"></i>
                {{ __('premium') }}
            </h2>
            <div class="mb-4 text-secondary">
                {{ __('premium_description') }}
            </div>
            <a href="https://timecrack.org/premium" class="btn btn-primary btn-lg w-100" target="_blank">
                <i class="bi bi-star me-2"></i>
                {{ __('go_premium') }}
            </a>
        </div>
    </div>
@endsection
