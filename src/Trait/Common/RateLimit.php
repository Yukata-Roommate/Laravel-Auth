<?php

namespace YukataRm\Laravel\Auth\Trait\Common;

use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Http\RedirectResponse;

/**
 * Rate Limit trait
 * 
 * @package YukataRm\Laravel\Auth\Trait\Common
 */
trait RateLimit
{
    /*----------------------------------------*
     * Property
     *----------------------------------------*/

    /**
     * whether use rate limit
     * 
     * @return bool
     */
    protected function useRateLimit(): bool
    {
        return property_exists($this, "useRateLimit") ? $this->useRateLimit : true;
    }

    /**
     * get rate limit
     * 
     * @return int
     */
    protected function rateLimit(): int
    {
        return property_exists($this, "rateLimit") ? $this->rateLimit : 5;
    }

    /**
     * get rate limit decay
     * 
     * @return int
     */
    protected function rateLimitDecay(): int
    {
        return property_exists($this, "rateLimitDecay") ? $this->rateLimitDecay : 300;
    }


    /**
     * get rate limit key
     * 
     * @return string
     */
    protected function rateLimitKey(): string
    {
        return request()->ip();
    }

    /**
     * get available in
     * 
     * @return int
     */
    protected function availableIn(): int
    {
        return RateLimiter::availableIn($this->rateLimitKey());
    }

    /**
     * get redirect with rate limit errors
     * 
     * @return array<string, mixed>
     */
    protected function withRateLimitErrors(): array
    {
        return [];
    }

    /*----------------------------------------*
     * Method
     *----------------------------------------*/

    /**
     * whether too many attempts
     * 
     * @return bool
     */
    protected function tooManyAttempts(): bool
    {
        return $this->useRateLimit() && RateLimiter::tooManyAttempts($this->rateLimitKey(), $this->rateLimit());
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
            ->withErrors($this->withRateLimitErrors());
    }

    /**
     * hit rate limit
     * 
     * @return void
     */
    protected function hitRateLimit(): void
    {
        if (!$this->useRateLimit()) return;

        RateLimiter::hit($this->rateLimitKey(), $this->rateLimitDecay());
    }

    /**
     * clear rate limit
     * 
     * @return void
     */
    protected function clearRateLimit(): void
    {
        if (!$this->useRateLimit()) return;

        RateLimiter::clear($this->rateLimitKey());
    }
}
