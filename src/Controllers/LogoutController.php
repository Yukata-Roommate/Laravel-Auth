<?php

namespace YukataRm\Laravel\Auth\Controllers;

use YukataRm\Laravel\Auth\Controllers\Base\AuthController;

use Illuminate\Http\RedirectResponse;

/**
 * Logout Controller
 *
 * @package YukataRm\Laravel\Auth\Controllers
 */
abstract class LogoutController extends AuthController
{
    /*========================================*
     * Handle
     *========================================*/

    /*----------------------------------------*
     * Request
     *----------------------------------------*/

    /**
     * get validate request rules
     *
     * @return array<string, string>
     */
    protected function validateRequestRules(): array
    {
        return [];
    }

    /*----------------------------------------*
     * Process
     *----------------------------------------*/

    /**
     * process
     *
     * @return \Illuminate\Http\RedirectResponse|null
     */
    protected function process(): RedirectResponse|null
    {
        $this->guardLogout();

        $this->sessionInvalidate();

        $this->sessionRegenerateToken();

        return null;
    }

    /*----------------------------------------*
     * Session
     *----------------------------------------*/

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

    /*----------------------------------------*
     * Success
     *----------------------------------------*/

    /**
     * route
     *
     * @var string
     */
    protected string $route = "auth.login.form";

    /**
     * with message key
     *
     * @var string
     */
    protected string $withMessageKey = "logout.success";
}
