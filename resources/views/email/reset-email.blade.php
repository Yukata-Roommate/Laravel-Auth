@extends('yr-auth::email.layout', ['title' => $subject])

@section('style')
    <style>
        .messages {
            margin: 0;
            margin-bottom: 1rem;
        }

        .section {
            border-top: 1px solid #ddd;
            border-bottom: 1px solid #ddd;
        }

        .section .expired_at {
            font-size: 1.2em;
            color: #999;
        }

        .section .expired_at span {
            color: #333;
        }

        .section .token {
            font-size: 1.2em;
            color: #999;
        }

        .section .token span {
            color: #333;
        }
    </style>
@endsection

@section('content')
    <p class="messages">
        {!! $remarksMessage !!}
    </p>

    <div class="section">
        <p class="expired_at">
            {{ $expiredMessage }}: <span>{{ $emailResetToken->expired_at->format('Y-m-d H:i:s') }}</span>
        </p>

        <p class="token">
            {{ $tokenMessage }}: <span>{{ $emailResetToken->token }}</span>
        </p>
    </div>
@endsection
