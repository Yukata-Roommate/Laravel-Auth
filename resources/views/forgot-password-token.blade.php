@extends('yr-auth::layout', [
    'title' => __('yr-auth::title.forgot-password-token'),
    'action' => route('auth.forgot-password-token.handle'),
    'success' => session('alert.success'),
    'failure' => session('alert.failure'),
])

@section('body')
    <div class="form-group mb-3">
        <label for="token">
            {{ __('yr-auth::item.token') }}
        </label>

        <input id="token" name="token" type="text" class="form-control" value="{{ old('token') }}" required />
    </div>

    <div class="form-group mb-3">
        <label for="newPassword">
            {{ __('yr-auth::item.new-password') }}
        </label>

        <input id="newPassword" name="password" type="password" class="form-control" value="{{ old('password') }}"
            required />
    </div>

    <div class="form-group">
        <label for="newPasswordConfirmation">
            {{ __('yr-auth::item.new-password-confirmation') }}
        </label>

        <input id="newPasswordConfirmation" name="password_confirmation" type="password" class="form-control"
            value="{{ old('password_confirmation') }}" required />
    </div>
@endsection

@section('footer')
    <button type="submit" class="btn btn-outline-primary d-block w-100">
        {{ __('yr-auth::button.reset-password') }}
    </button>
@endsection
