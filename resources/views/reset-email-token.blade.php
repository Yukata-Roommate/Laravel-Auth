@extends('yr-auth::layout', [
    'title' => __('yr-auth::title.reset-email-token'),
    'action' => route('auth.reset-email-token.handle'),
    'success' => session('alert.success'),
    'failure' => session('alert.failure'),
])

@section('body')
    <div class="form-group">
        <label for="token">
            {{ __('yr-auth::item.token') }}
        </label>

        <input id="token" name="token" type="text" class="form-control" value="{{ old('token') }}" required />
    </div>
@endsection

@section('footer')
    <button type="submit" class="btn btn-outline-primary d-block w-100">
        {{ __('yr-auth::button.reset-email') }}
    </button>
@endsection
