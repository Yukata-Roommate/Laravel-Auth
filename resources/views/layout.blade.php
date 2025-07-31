<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <meta name="format-detection" content="telephone=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="robots" content="noindex, nofollow">

    <title>{{ $title }} | {{ config('app.name') }}</title>

    {{-- Favicon --}}
    <link rel="icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">

    {{-- Fontsource --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fontsource/source-sans-3@5.2.6/index.min.css">

    {{-- Bootstrap --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">

    {{-- Bootstrap Icons --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

    {{-- AdminLTE 4 --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@4.0.0-beta3/dist/css/adminlte.min.css" />
</head>

<body class="login-page bg-body-secondary app-loaded">
    <form method="post" action="{{ $action }}">
        @csrf

        <div class="login-box" style="width: 360px;">
            <div class="card">
                <div class="card-header bg-info">
                    <h1 class="mb-0 text-center text-light">
                        {{ $title }}
                    </h1>
                </div>

                <div class="card-body login-card-body">
                    @if (isset($success))
                        <div class="alert alert-success d-flex flex-column" role="alert">
                            <div class="d-flex h3">
                                <i class="bi bi-check2-circle"></i>

                                <p class="mb-0 ms-3">
                                    Success
                                </p>
                            </div>

                            {!! $success !!}
                        </div>
                    @endif

                    @if (isset($failure))
                        <div class="alert alert-danger d-flex flex-column" role="alert">
                            <div class="d-flex h3">
                                <i class="bi bi-exclamation-triangle"></i>

                                <p class="mb-0 ms-3">
                                    Failure
                                </p>
                            </div>

                            {!! $failure !!}
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="alert alert-danger d-flex flex-column" role="alert">
                            <div class="d-flex h3">
                                <i class="bi bi-exclamation-triangle-fill"></i>

                                <p class="mb-0 ms-3">
                                    Failure
                                </p>
                            </div>

                            <ul class="m-0 p-0 text-left">
                                @foreach ($errors->all() as $error)
                                    <li class="list-group-item-danger d-block mb-1">
                                        {!! Text::enl2br($error) !!}
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @yield('body')
                </div>

                <div class="card-footer login-card-footer">
                    @yield('footer')
                </div>
            </div>
        </div>
    </form>

    {{-- Bootstrap --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    {{-- AdminLTE 4 --}}
    <script src="https://cdn.jsdelivr.net/npm/admin-lte@4.0.0-beta3/dist/js/adminlte.min.js"></script>
</body>

</html>
