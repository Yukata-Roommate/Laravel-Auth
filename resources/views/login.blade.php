@extends('yr-auth::layout', [
    'title' => __('yr-auth::title.login'),
    'action' => route('auth.login.handle'),
    'success' => session('alert.success'),
    'failure' => session('alert.failure'),
])

@section('body')
    <div class="form-group mb-3">
        <label for="email">
            {{ __('yr-auth::item.email') }}
        </label>

        <input id="email" name="email" type="email" class="form-control" value="{{ old('email') }}" required />
    </div>

    <div class="form-group mb-3">
        <label for="password">
            {{ __('yr-auth::item.password') }}
        </label>

        <input id="password" name="password" type="password" class="form-control" value="{{ old('password') }}" required />
    </div>

    <div class="form-check form-check-inline">
        <input id="remember" class="form-check-input" type="checkbox" name="remember" class="form-control" value="1"
            @checked(old('remember') == 1) />

        <label class="form-check-label" for="remember">
            {{ __('yr-auth::item.remember') }}
        </label>
    </div>
@endsection

@section('footer')
    <button type="submit" class="btn btn-outline-primary d-block w-100">
        {{ __('yr-auth::button.login') }}
    </button>

    <div class="border-top border-secondary my-2"></div>

    <a class="btn btn-light d-block w-100" href="{{ route('auth.forgot-password.handle') }}">
        {{ __('yr-auth::button.forgot-password') }}
    </a>
@endsection
