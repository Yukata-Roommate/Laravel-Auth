<?php

namespace YukataRm\Laravel\Auth\Trait\Common;

use Illuminate\Support\Facades\RateLimiter;

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
     * get rate limit key
     * 
     * @return string
     */
    protected function rateLimitKey(): string
    {
        return property_exists($this, "rateLimitKey") ? $this->rateLimitKey : request()->ip();
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
        return !RateLimiter::tooManyAttempts($this->rateLimitKey(), $this->rateLimit());
    }

    /**
     * reached rate limit
     * 
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function reachedRateLimit(): RedirectResponse
    {
        return $this->redirectToBack($this->withRateLimitErrors());
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
}
