@extends('yr-auth::layout', [
    'title' => __('yr-auth::title.reset-password'),
    'action' => route('auth.reset-password.handle'),
    'success' => session('alert.success'),
    'failure' => session('alert.failure'),
])

@section('body')
    <div class="form-group mb-3">
        <label for="currentPassword">
            {{ __('yr-auth::item.current-password') }}
        </label>

        <input id="currentPassword" name="current_password" type="password" class="form-control"
            value="{{ old('current_password') }}" required />
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
