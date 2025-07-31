@extends('yr-auth::layout', [
    'title' => __('yr-auth::title.forgot-password'),
    'action' => route('auth.forgot-password.handle'),
    'success' => session('alert.success'),
    'failure' => session('alert.failure'),
])

@section('body')
    <div class="form-group">
        <label for="email">
            {{ __('yr-auth::item.email') }}
        </label>

        <input id="email" name="email" type="email"class="form-control"  value="{{ old('email') }}" required />
    </div>
@endsection

@section('footer')
    <button type="submit" class="btn btn-outline-primary d-block w-100">
        {{ __('yr-auth::button.send') }}
    </button>
@endsection
