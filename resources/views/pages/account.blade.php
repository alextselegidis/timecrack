{{--
/* ----------------------------------------------------------------------------
 * Timecrack - Simple Bookmark Manager
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
    {{__('account')}}
@endsection

@section('breadcrumbs')
    @include('shared.breadcrumb', ['breadcrumbs' => [
        ['label' => __('account')]
    ]])
@endsection

@section('content')
    <div>
        <div style="max-width: 600px" class="mx-auto my-4">

            <!-- Account Details Card -->
            <h5 class="text-dark fw-bold mb-3">{{ __('profile') }}</h5>

            <div class="card shadow-sm border-0 rounded-3 mb-4">
                <div class="card-body p-4">
                    <form action="{{ route('account.update') }}" method="POST" id="account-form">
                        @csrf
                        @method('PUT')

                        <!-- Name -->
                        <div class="mb-3">
                            <label for="name" class="form-label text-dark fw-medium">
                                {{ __('name') }} <span class="text-danger">*</span>
                            </label>
                            <input
                                type="text"
                                id="name"
                                name="name"
                                class="form-control @error('name') is-invalid @enderror"
                                value="{{ old('name', auth()->user()->name) }}"
                                required
                            >
                            @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Email -->
                        <div class="mb-3">
                            <label for="email" class="form-label text-dark fw-medium">
                                {{ __('email') }} <span class="text-danger">*</span>
                            </label>
                            <input
                                type="email"
                                id="email"
                                name="email"
                                class="form-control @error('email') is-invalid @enderror"
                                value="{{ old('email', auth()->user()->email) }}"
                                required
                            >
                            @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Timezone -->
                        <div class="mb-3">
                            <label for="timezone" class="form-label text-dark fw-medium">
                                {{ __('timezone') }}
                            </label>
                            @include('shared.timezone-dropdown', [
                                'name' => 'timezone',
                                'id' => 'timezone',
                                'value' => old('timezone', auth()->user()->timezone ?: setting('default_timezone', 'UTC')),
                                'required' => false,
                            ])
                            <div class="form-text">{{ __('timezone_help_message') }}</div>
                            @error('timezone')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </form>
                </div>

                <!-- Card Footer with Save Button -->
                <div class="card-footer bg-body-secondary border-top text-end py-3 px-4">
                    <button type="submit" form="account-form" class="btn btn-dark">
                        {{ __('save') }}
                    </button>
                </div>
            </div>
            <!-- Change Password Section -->
            <h5 class="text-dark fw-bold mb-3">{{ __('password') }}</h5>
            <div class="card shadow-sm border-0 rounded-3 mb-4">
                <div class="card-body p-4">
                    <form action="{{ route('account.update') }}" method="POST" id="password-form">
                        @csrf
                        @method('PUT')

                        <!-- Hidden fields to preserve account data -->
                        <input type="hidden" name="name" value="{{ auth()->user()->name }}">
                        <input type="hidden" name="email" value="{{ auth()->user()->email }}">

                        <!-- Current Password -->
                        <div class="mb-3">
                            <label for="current_password" class="form-label text-dark fw-medium">
                                {{ __('current_password') }}
                            </label>
                            <input
                                type="password"
                                id="current_password"
                                name="current_password"
                                class="form-control @error('current_password') is-invalid @enderror"
                                autocomplete="current-password"
                            >
                            @error('current_password')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- New Password -->
                        <div class="mb-3">
                            <label for="password" class="form-label text-dark fw-medium">
                                {{ __('new_password') }}
                            </label>
                            <input
                                type="password"
                                id="password"
                                name="password"
                                class="form-control @error('password') is-invalid @enderror"
                                autocomplete="new-password"
                            >
                            @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Password Confirmation -->
                        <div class="mb-3">
                            <label for="password_confirmation" class="form-label text-dark fw-medium">
                                {{ __('password_repeat') }}
                            </label>
                            <input
                                type="password"
                                id="password_confirmation"
                                name="password_confirmation"
                                class="form-control"
                                autocomplete="new-password"
                            >
                        </div>
                    </form>
                </div>

                <!-- Card Footer with Save Button -->
                <div class="card-footer bg-body-secondary border-top text-end py-3 px-4">
                    <button type="submit" form="password-form" class="btn btn-dark">
                        {{ __('save') }}
                    </button>
                </div>
            </div>

            <!-- API Tokens Section -->
            <h5 class="text-dark fw-bold mb-3">{{ __('api_tokens') }}</h5>

            @if(session('new_token'))
                <div class="alert alert-success">
                    <strong>{{ __('token_created_message') }}</strong>
                    <p class="mb-2">{{ __('token_copy_warning') }}</p>
                    <div class="input-group">
                        <input type="text" class="form-control font-monospace" value="{{ session('new_token') }}" readonly id="new-token-input">
                        <button class="btn btn-outline-secondary" type="button" onclick="copyToken()">
                            <i class="bi bi-clipboard"></i>
                        </button>
                    </div>
                </div>
            @endif

            <div class="card shadow-sm border-0 rounded-3 mb-4">
                <div class="card-body p-4">
                    <form action="{{ route('account.tokens.create') }}" method="POST" id="token-form" class="mb-4">
                        @csrf
                        <div class="row g-2 align-items-end">
                            <div class="col">
                                <label for="token_name" class="form-label text-dark fw-medium">
                                    {{ __('token_name') }} <span class="text-danger">*</span>
                                </label>
                                <input
                                    type="text"
                                    id="token_name"
                                    name="token_name"
                                    class="form-control @error('token_name') is-invalid @enderror"
                                    placeholder="{{ __('token_name_placeholder') }}"
                                    required
                                >
                                @error('token_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-auto">
                                <button type="submit" class="btn btn-dark">
                                    <i class="bi bi-plus-lg me-1"></i>
                                    {{ __('create') }}
                                </button>
                            </div>
                        </div>
                    </form>

                    @if($tokens->isEmpty())
                        <p class="text-muted mb-0">{{ __('no_tokens_message') }}</p>
                    @else
                        <div class="table-responsive">
                            <table class="table table-sm table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>{{ __('name') }}</th>
                                        <th>{{ __('created') }}</th>
                                        <th>{{ __('last_used') }}</th>
                                        <th class="text-end">{{ __('actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($tokens as $token)
                                        <tr>
                                            <td>{{ $token->name }}</td>
                                            <td>{{ tz($token->created_at)->format('Y-m-d H:i') }}</td>
                                            <td>{{ tz($token->last_used_at)?->format('Y-m-d H:i') ?? '-' }}</td>
                                            <td class="text-end">
                                                <form action="{{ route('account.tokens.revoke', $token->id) }}" method="POST" class="d-inline" onsubmit="return confirm('{{ __('revoke_token_prompt') }}')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>

        </div>

    </div>
@endsection

@section('scripts')
<script>
function copyToken() {
    const input = document.getElementById('new-token-input');
    input.select();
    document.execCommand('copy');
    alert('{{ __('copied_to_clipboard') }}');
}
</script>
@endsection
