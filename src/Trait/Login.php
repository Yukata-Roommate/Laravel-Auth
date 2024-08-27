<?php

namespace YukataRm\Laravel\Auth\Trait;

use YukataRm\Laravel\Auth\Trait\Common\Property;
use YukataRm\Laravel\Auth\Trait\Common\RateLimit;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
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
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function login(Request $request): RedirectResponse
    {
        $this->request = $request;

        if ($this->tooManyAttempts()) return $this->reachedRateLimit();

        if (!$this->attemptLogin()) return $this->failedLogin();

        return $this->successLogin();
    }

    /*----------------------------------------*
     * Property
     *----------------------------------------*/

    /**
     * request
     * 
     * @var \Illuminate\Http\Request
     */
    protected Request $request;

    /**
     * credentials key
     * 
     * @var array<string>
     */
    protected array $credentials = ["email", "password"];

    /**
     * remember key
     * 
     * @var string
     */
    protected string $remember = "remember";

    /**
     * with input key
     * 
     * @var array<string>
     */
    protected array $withInput = ["email"];

    /**
     * get credentials
     * 
     * @return array<string, mixed>
     */
    protected function credentials(): array
    {
        return $this->request->only($this->credentials);
    }

    /**
     * get remember
     * 
     * @return bool
     */
    protected function remember(): bool
    {
        return $this->request->filled($this->remember);
    }

    /**
     * get redirect with input
     * 
     * @return array<string, mixed>
     */
    protected function withInput(): array
    {
        return $this->request->only($this->withInput);
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

        return $this->failedLoginRedirect();
    }

    /**
     * get failed login redirect
     * 
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function failedLoginRedirect(): RedirectResponse
    {
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
        $this->sessionRegenerate();

        $this->clearRateLimit();

        return $this->successLoginRedirect();
    }

    /**
     * session regenerate
     * 
     * @return void
     */
    protected function sessionRegenerate(): void
    {
        $this->request->session()->regenerate();
    }

    /**
     * get success login redirect
     * 
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function successLoginRedirect(): RedirectResponse
    {
        return redirect()->intended($this->route());
    }
}
