<?php

namespace YukataRm\Laravel\Auth\Controllers\Base;

use YukataRm\Laravel\Auth\Lang;
use YukataRm\Laravel\Auth\Guard;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

use Illuminate\Support\Facades\RateLimiter;

use YukataRm\Laravel\Db\Facades\Transaction;

/**
 * Auth Controller
 *
 * @package YukataRm\Laravel\Auth\Controllers\Base
 */
abstract class AuthController
{
    use Lang, Guard;

    /*========================================*
     * Handle
     *========================================*/

    /**
     * handle
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request): RedirectResponse
    {
        $this->initialize($request);

        try {
            $this->validateRequest();
        } catch (ValidationException $exception) {
            if ($this->useRateLimit) $this->hitRateLimit();

            throw $exception;
        }

        if ($this->isReachedRateLimit()) return $this->reachedRateLimit();

        return $this->process() ?? $this->successRedirect();
    }

    /**
     * initialize
     *
     * @param \Illuminate\Http\Request $request
     * @return void
     */
    protected function initialize(Request $request): void
    {
        $this->setRequest($request);

        $this->setRateLimitKey($request);
    }

    /**
     * process
     *
     * @return \Illuminate\Http\RedirectResponse|null
     */
    abstract protected function process(): RedirectResponse|null;

    /*----------------------------------------*
     * Request
     *----------------------------------------*/

    /**
     * request
     *
     * @var \Illuminate\Http\Request
     */
    protected Request $request;

    /**
     * set request
     *
     * @param \Illuminate\Http\Request $request
     * @return void
     */
    protected function setRequest(Request $request): void
    {
        $this->request = $request;
    }

    /**
     * validate request
     *
     * @return void
     */
    protected function validateRequest(): void
    {
        $this->request->validate($this->validateRequestRules());
    }

    /**
     * get validate request rules
     *
     * @return array<string, string>
     */
    abstract protected function validateRequestRules(): array;

    /**
     * get request only
     *
     * @param array<string> $keys
     * @return array<string, mixed>
     */
    protected function requestOnly(array $keys): array
    {
        return $this->request->only($keys);
    }

    /**
     * get request input
     *
     * @param string $key
     * @return mixed
     */
    protected function requestInput(string $key)
    {
        return $this->request->input($key);
    }

    /**
     * get request filled
     *
     * @param string $key
     * @return bool
     */
    protected function requestFilled(string $key): bool
    {
        return $this->request->filled($key);
    }

    /*----------------------------------------*
     * Rate Limit
     *----------------------------------------*/

    /**
     * whether use rate limit
     *
     * @var bool
     */
    protected bool $useRateLimit = false;

    /**
     * rate limit
     *
     * @var int
     */
    protected int $rateLimit = 3;

    /**
     * rate limit decay
     *
     * @var int
     */
    protected int $rateLimitDecay = 3600;

    /**
     * rate limit key
     *
     * @var string
     */
    protected string $rateLimitKey;

    /**
     * set rate limit key
     *
     * @param \Illuminate\Http\Request $request
     * @return void
     */
    protected function setRateLimitKey(Request $request): void
    {
        $this->rateLimitKey = $request->ip();
    }

    /**
     * whether is reached rate limit
     *
     * @return bool
     */
    protected function isReachedRateLimit(): bool
    {
        return RateLimiter::tooManyAttempts($this->rateLimitKey(), $this->rateLimit());
    }

    /**
     * reached rate limit
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function reachedRateLimit(): RedirectResponse
    {
        return redirect()
            ->back()
            ->withErrors($this->rateLimitErrors());
    }

    /**
     * hit rate limit
     *
     * @return void
     */
    protected function hitRateLimit(): void
    {
        RateLimiter::hit($this->rateLimitKey(), $this->rateLimitDecay());
    }

    /**
     * clear rate limit
     *
     * @return void
     */
    protected function clearRateLimit(): void
    {
        RateLimiter::clear($this->rateLimitKey());
    }

    /**
     * get seconds to rate limit wait
     *
     * @return int
     */
    protected function rateLimitWait(): int
    {
        return RateLimiter::availableIn($this->rateLimitKey());
    }

    /**
     * get redirect with rate limit errors
     *
     * @return array<string, mixed>
     */
    protected function rateLimitErrors(): array
    {
        $seconds = $this->rateLimitWait();

        return [
            "form" => match (true) {
                $seconds < 60    => $this->lang("rate-limit.seconds", ["seconds" => $seconds]),
                $seconds < 3600  => $this->lang("rate-limit.minutes", ["minutes" => floor($seconds / 60)]),
                $seconds < 86400 => $this->lang("rate-limit.hours", ["hours" => floor($seconds / 3600)]),

                default => $this->lang("rate-limit.days", ["days" => floor($seconds / 86400)]),
            },
        ];
    }

    /**
     * get rate limit
     *
     * @return int
     */
    protected function rateLimit(): int
    {
        return $this->rateLimit;
    }

    /**
     * get rate limit decay
     *
     * @return int
     */
    protected function rateLimitDecay(): int
    {
        return $this->rateLimitDecay;
    }

    /**
     * get rate limit key
     *
     * @return string
     */
    protected function rateLimitKey(): string
    {
        return $this->rateLimitKey;
    }

    /*----------------------------------------*
     * Transaction
     *----------------------------------------*/

    /**
     * run transaction
     *
     * @param callable $callback
     * @return bool
     */
    protected function runTransaction(callable $callback): bool
    {
        try {
            Transaction::execute($callback);

            return true;
        } catch (\Throwable $e) {
            return false;
        }
    }

    /*----------------------------------------*
     * Success
     *----------------------------------------*/

    /**
     * route
     *
     * @var string
     */
    protected string $route;

    /**
     * with key
     *
     * @var string
     */
    protected string $withKey = "alert.success";

    /**
     * with message key
     *
     * @var string
     */
    protected string $withMessageKey;

    /**
     * get success redirect
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function successRedirect(): RedirectResponse
    {
        return redirect()
            ->route($this->route())
            ->with($this->with());
    }

    /**
     * get route
     *
     * @return string
     */
    protected function route(): string
    {
        return $this->route;
    }

    /**
     * get with
     *
     * @return array<string, mixed>
     */
    protected function with(): array
    {
        return [
            $this->withKey() => $this->withMessage()
        ];
    }

    /**
     * get with key
     *
     * @return string
     */
    protected function withKey(): string
    {
        return $this->withKey;
    }

    /**
     * get with message
     *
     * @return string
     */
    protected function withMessage(): string
    {
        $key = $this->withMessageKey();

        $lang    = __($key);
        $default = $this->lang($key);

        return $key === $lang ? $default : $lang;
    }

    /**
     * get with message key
     *
     * @return string
     */
    protected function withMessageKey(): string
    {
        return $this->withMessageKey;
    }

    /*----------------------------------------*
     * Failed
     *----------------------------------------*/

    /**
     * get failed redirect
     *
     * @param array<string, mixed> $withInput
     * @param array<string, mixed> $withErrors
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function failedRedirect(array $withInput, array $withErrors): RedirectResponse
    {
        return redirect()
            ->back()
            ->withInput($withInput)
            ->withErrors($withErrors);
    }

    /**
     * get with input
     *
     * @param array<string> $keys
     * @return array<string, mixed>
     */
    protected function withInput(array $keys): array
    {
        return $this->requestOnly($keys);
    }

    /**
     * get with errors message
     *
     * @param string $key
     * @return string
     */
    protected function withErrorsMessage(string $key): string
    {
        $lang = __($key);

        return $key !== $lang ? $lang : $this->lang($key);
    }
}
