<?php

namespace YukataRm\Laravel\Auth\Middleware;

use YukataRm\Laravel\Middleware\BaseMiddleware;
use Symfony\Component\HttpFoundation\Response;

use YukataRm\Laravel\Auth\Trait\Property;

use Illuminate\Support\Facades\Auth;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Http\RedirectResponse;

/**
 * Logout User Middleware
 * 
 * @package YukataRm\Laravel\Auth\Middleware
 */
abstract class LogoutUser extends BaseMiddleware
{
    use Property;

    /**
     * default route
     * 
     * @var string
     */
    protected string $defaultRoute = "login";

    /**
     * run middleware handle
     * 
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function runHandle(): Response
    {
        $user = Auth::guard($this->guard())->user();

        if (is_null($user)) return $this->next();

        if ($this->shouldLogout($user)) {
            Auth::guard($this->guard())->logout();

            return $this->logoutRedirect();
        }

        return $this->next();
    }

    /*----------------------------------------*
     * Method
     *----------------------------------------*/

    /**
     * whether user should be logged out
     * 
     * @param \Illuminate\Contracts\Auth\Authenticatable $user
     * @return bool
     */
    abstract protected function shouldLogout(Authenticatable $user): bool;

    /**
     * redirect to route
     * 
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function logoutRedirect(): RedirectResponse
    {
        return redirect()->route($this->route())->withErrors($this->logoutErrors());
    }

    /**
     * get logout redirect errors
     * 
     * @return array<string>
     */
    protected function logoutErrors(): array
    {
        return [
            $this->logoutMessage()
        ];
    }

    /**
     * get logout message
     * 
     * @return string
     */
    protected function logoutMessage(): string
    {
        return "You have been logged out.";
    }
}
