<?php

namespace YukataRm\Laravel\Auth\Trait;

use YukataRm\Laravel\Auth\Trait\Common\Property;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Logout trait
 * 
 * @package YukataRm\Laravel\Auth\Trait
 */
trait Logout
{
    use Property;

    /**
     * logout
     * 
     * @param \Illuminate\Http\Request $logoutRequest
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function logout(Request $logoutRequest): RedirectResponse
    {
        $this->_logoutRequest = $logoutRequest;

        $this->guardLogout();

        $this->sessionInvalidate();

        $this->sessionRegenerateToken();

        return $this->redirectToRoute();
    }

    /*----------------------------------------*
     * Property
     *----------------------------------------*/

    /**
     * logout request
     * 
     * @var \Illuminate\Http\Request
     */
    private Request $_logoutRequest;

    /*----------------------------------------*
     * Method
     *----------------------------------------*/

    /**
     * guard logout
     * 
     * @return void
     */
    protected function guardLogout(): void
    {
        Auth::guard($this->guard())->logout();
    }

    /**
     * session invalidate
     * 
     * @return void
     */
    protected function sessionInvalidate(): void
    {
        $this->_logoutRequest->session()->invalidate();
    }

    /**
     * session regenerate token
     * 
     * @return void
     */
    protected function sessionRegenerateToken(): void
    {
        $this->_logoutRequest->session()->regenerateToken();
    }

    /**
     * get redirect to route
     * 
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function redirectToRoute(): RedirectResponse
    {
        return redirect()->route($this->route());
    }
}
