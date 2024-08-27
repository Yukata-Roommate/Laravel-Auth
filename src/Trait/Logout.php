<?php

namespace YukataRm\Laravel\Auth\Trait;

use YukataRm\Laravel\Auth\Trait\Common\Property;

use Illuminate\Http\RedirectResponse;

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
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function logout(): RedirectResponse
    {
        $this->guardLogout();

        return $this->redirectToRoute();
    }

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
     * get redirect to route
     * 
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function redirectToRoute(): RedirectResponse
    {
        return redirect()->route($this->route());
    }
}
