<?php

namespace YukataRm\Laravel\Auth;

use Illuminate\Support\Facades\Auth;
use Illuminate\Contracts\Auth\Guard as GuardContract;
use Illuminate\Contracts\Auth\StatefulGuard as StatefulGuardContract;
use Illuminate\Foundation\Auth\User;

/**
 * Auth Guard Trait
 *
 * @package YukataRm\Laravel\Auth
 */
trait Guard
{
    /**
     * get auth guard
     *
     * @return string
     */
    protected function guard(): string
    {
        return property_exists($this, "guard") ? $this->guard : "web";
    }

    /**
     * get guard instance
     *
     * @return \Illuminate\Contracts\Auth\Guard|\Illuminate\Contracts\Auth\StatefulGuard
     */
    protected function guardInstance(): GuardContract|StatefulGuardContract
    {
        return Auth::guard($this->guard());
    }

    /**
     * get guard user
     *
     * @return \Illuminate\Foundation\Auth\User
     */
    protected function guardUser(): User
    {
        return $this->guardInstance()->user();
    }

    /**
     * whether user is logged in
     *
     * @return bool
     */
    protected function isLoggedIn(): bool
    {
        return $this->guardInstance()->check();
    }

    /**
     * guard logout
     *
     * @return void
     */
    protected function guardLogout(): void
    {
        $this->guardInstance()->logout();
    }
}
