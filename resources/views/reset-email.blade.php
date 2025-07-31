@extends('yr-auth::layout', [
    'title' => __('yr-auth::title.reset-email'),
    'action' => route('auth.reset-email.handle'),
    'success' => session('alert.success'),
    'failure' => session('alert.failure'),
])

@section('body')
    <div class="form-group">
        <label for="newEmail">
            {{ __('yr-auth::item.new-email') }}
        </label>

        <input id="newEmail" name="email" type="email" class="form-control" value="{{ old('email') }}" required />
    </div>
@endsection

@section('footer')
    <button type="submit" class="btn btn-outline-primary d-block w-100">
        {{ __('yr-auth::button.send') }}
    </button>
@endsection
