<?php

namespace YukataRm\Laravel\Auth\Trait;

use YukataRm\Laravel\Auth\Trait\Common\Property;
use YukataRm\Laravel\Auth\Trait\Common\RateLimit;

use Illuminate\Http\RedirectResponse;

use Illuminate\Support\Facades\Auth;

/**
 * Login trait
 * 
 * @package YukataRm\Laravel\Auth\Trait
 */
trait Login
{
    use Property;
    use RateLimit;

    /**
     * login
     * 
     * @return \Illuminate\Http\RedirectResponse
     */
    public function login(): RedirectResponse
    {
        if ($this->tooManyAttempts()) return $this->reachedRateLimit();
    }

    /*----------------------------------------*
     * Property
     *----------------------------------------*/

    /**
     * credentials key
     * 
     * @var array<string>
     */
    protected $credentials = ["email", "password"];

    /**
     * remember key
     * 
     * @var string
     */
    protected $remember = "remember";

    /**
     * with input key
     * 
     * @var array<string>
     */
    protected $withInput = ["email"];

    /**
     * get credentials
     * 
     * @return array<string, mixed>
     */
    protected function credentials(): array
    {
        return $this->request()->only($this->credentials);
    }

    /**
     * get remember
     * 
     * @return bool
     */
    protected function remember(): bool
    {
        return $this->request()->filled($this->remember);
    }

    /**
     * get redirect with input
     * 
     * @return array<string, mixed>
     */
    protected function withInput(): array
    {
        return $this->request()->only($this->withInput);
    }

    /**
     * get redirect with errors
     * 
     * @return array<string, mixed>
     */
    protected function withErrors(): array
    {
        return [];
    }

    /*----------------------------------------*
     * Method
     *----------------------------------------*/

    /**
     * attempt login
     * 
     * @return bool
     */
    protected function attemptLogin(): bool
    {
        return Auth::guard($this->guard())
            ->attempt(
                $this->credentials(),
                $this->remember()
            );
    }

    /**
     * failed login
     * 
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function failedLogin(): RedirectResponse
    {
        $this->hitRateLimit();

        return redirect()
            ->back()
            ->withInput($this->withInput())
            ->withErrors($this->withErrors());
    }

    /**
     * success login
     * 
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function successLogin(): RedirectResponse
    {
        $this->request()->session()->regenerate();

        $this->clearRateLimit();

        return redirect()->intended($this->route());
    }
}
