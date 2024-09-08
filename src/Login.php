<?php

namespace YukataRm\Laravel\Auth;

use YukataRm\Laravel\Auth\Trait\Property;
use YukataRm\Laravel\Auth\Trait\RateLimit;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Login trait
 * 
 * @package YukataRm\Laravel\Auth
 */
trait Login
{
    use Property;
    use RateLimit;

    /**
     * login
     * 
     * @param \Illuminate\Http\Request $loginRequest
     * @return \Illuminate\Http\RedirectResponse
     */
    public function login(Request $loginRequest): RedirectResponse
    {
        $this->_loginRequest = $loginRequest;

        if ($this->tooManyAttempts()) return $this->reachedRateLimit();

        if (!$this->attemptLogin()) return $this->failedLogin();

        return $this->successLogin();
    }

    /*----------------------------------------*
     * Property
     *----------------------------------------*/

    /**
     * login request
     * 
     * @var \Illuminate\Http\Request
     */
    private Request $_loginRequest;

    /**
     * get credentials key
     * 
     * @return array<string>
     */
    protected function credentialsKey(): array
    {
        return property_exists($this, "credentials") ? $this->credentials : ["email", "password"];
    }

    /**
     * get credentials
     * 
     * @return array<string, mixed>
     */
    protected function credentials(): array
    {
        return $this->_loginRequest->only($this->credentialsKey());
    }

    /**
     * get remember key
     * 
     * @return string
     */
    protected function rememberKey(): string
    {
        return property_exists($this, "remember") ? $this->remember : "remember";
    }

    /**
     * get remember
     * 
     * @return bool
     */
    protected function remember(): bool
    {
        return $this->_loginRequest->filled($this->rememberKey());
    }

    /**
     * get with input key
     * 
     * @return array<string>
     */
    protected function withInputKey(): array
    {
        return property_exists($this, "withInput") ? $this->withInput : ["email"];
    }

    /**
     * get redirect with input
     * 
     * @return array<string, mixed>
     */
    protected function withInput(): array
    {
        return $this->_loginRequest->only($this->withInputKey());
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

    /**
     * get whether logout other devices
     * 
     * @return bool
     */
    protected function logoutOtherDevices(): bool
    {
        return property_exists($this, "logoutOtherDevices") ? $this->logoutOtherDevices : false;
    }

    /**
     * get password key
     * 
     * @return string
     */
    protected function passwordKey(): string
    {
        return property_exists($this, "password") ? $this->password : "password";
    }

    /**
     * get password
     * 
     * @return string
     */
    protected function password(): string
    {
        return $this->_loginRequest->input($this->passwordKey());
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

        $this->runLogoutOtherDevices();

        return $this->successLoginRedirect();
    }

    /**
     * session regenerate
     * 
     * @return void
     */
    protected function sessionRegenerate(): void
    {
        $this->_loginRequest->session()->regenerate();
    }

    /**
     * run logout other devices
     * 
     * @return void
     */
    protected function runLogoutOtherDevices(): void
    {
        if (!$this->logoutOtherDevices()) return;

        Auth::logoutOtherDevices($this->password());
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
