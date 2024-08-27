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
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function logout(Request $request): RedirectResponse
    {
        $this->request = $request;

        $this->guardLogout();

        $this->sessionInvalidate();

        $this->sessionRegenerateToken();

        return $this->redirectToRoute();
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
        $this->request->session()->invalidate();
    }

    /**
     * session regenerate token
     * 
     * @return void
     */
    protected function sessionRegenerateToken(): void
    {
        $this->request->session()->regenerateToken();
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
