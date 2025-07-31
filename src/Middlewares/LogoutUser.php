<?php

namespace YukataRm\Laravel\Auth\Middlewares;

use YukataRm\Laravel\Auth\Middlewares\Base\AuthMiddleware;
use Symfony\Component\HttpFoundation\Response;

use YukataRm\Laravel\Auth\Guard;

use Illuminate\Http\RedirectResponse;

/**
 * Logout User Middleware
 *
 * @package YukataRm\Laravel\Auth\Middlewares
 */
abstract class LogoutUser extends AuthMiddleware
{
    use Guard;

    /*----------------------------------------*
     * Handle
     *----------------------------------------*/

    /**
     * whether run middleware handle
     *
     * @return bool
     */
    #[\Override]
    protected function isRunHandle(): bool
    {
        if (!$this->isLoggedIn()) return false;

        if (!$this->shouldLogout()) return false;

        return true;
    }

    /**
     * run middleware handle
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function runHandle(): Response
    {
        return $this->logout();
    }

    /*----------------------------------------*
     * Should Logout
     *----------------------------------------*/

    /**
     * whether user should be logged out
     *
     * @return bool
     */
    protected function shouldLogout(): bool
    {
        return !$this->guardUser()->is_active;
    }

    /*----------------------------------------*
     * Logout
     *----------------------------------------*/

    /**
     * logout
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function logout(): RedirectResponse
    {
        $this->guardLogout();

        return $this->successLogout();
    }

    /*----------------------------------------*
     * Success Logout
     *----------------------------------------*/

    /**
     * success logout route
     *
     * @var string
     */
    protected string $successLogoutRoute = "";

    /**
     * success logout with errors key
     *
     * @var string
     */
    protected string $successLogoutWithErrorsKey = "";

    /**
     * success logout
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function successLogout(): RedirectResponse
    {
        return $this->successLogoutRedirect();
    }

    /**
     * get success logout redirect
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function successLogoutRedirect(): RedirectResponse
    {
        return redirect()
            ->route($this->successLogoutRoute())
            ->withErrors($this->successLogoutWithErrors());
    }

    /**
     * get success logout route
     *
     * @return string
     */
    protected function successLogoutRoute(): string
    {
        return empty($this->successLogoutRoute) ? "auth.login.form" : $this->successLogoutRoute;
    }

    /**
     * get success logout with errors
     *
     * @return array<string, mixed>
     */
    protected function successLogoutWithErrors(): array
    {
        return [
            $this->successLogoutWithErrorsKey() => $this->successLogoutWithErrorsMessage()
        ];
    }

    /**
     * get success logout with errors key
     *
     * @return string
     */
    protected function successLogoutWithErrorsKey(): string
    {
        return empty($this->successLogoutWithErrorsKey) ? "form" : $this->successLogoutWithErrorsKey;
    }

    /**
     * get success logout with errors message
     *
     * @return string
     */
    protected function successLogoutWithErrorsMessage(): string
    {
        return $this->lang("middleware.logout-user");
    }
}
