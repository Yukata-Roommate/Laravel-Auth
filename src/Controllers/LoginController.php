<?php

namespace YukataRm\Laravel\Auth\Controllers;

use YukataRm\Laravel\Auth\Controllers\Base\SingleFailedController;

use Illuminate\Http\RedirectResponse;

use Illuminate\Support\Facades\Auth;

/**
 * Login Controller
 *
 * @package YukataRm\Laravel\Auth\Controllers
 */
abstract class LoginController extends SingleFailedController
{
    /*========================================*
     * Form
     *========================================*/

    /**
     * form view
     *
     * @var string
     */
    protected string $view = "auth.login";

    /**
     * form data
     *
     * @var array<string, mixed>
     */
    protected array $data = [];

    /*========================================*
     * Handle
     *========================================*/

    /*----------------------------------------*
     * Request
     *----------------------------------------*/

    /**
     * user name key
     *
     * @var string
     */
    protected string $userNameKey = "email";

    /**
     * password key
     *
     * @var string
     */
    protected string $passwordKey = "password";

    /**
     * get user name key
     *
     * @return string
     */
    protected function userNameKey(): string
    {
        return $this->userNameKey;
    }

    /**
     * get password key
     *
     * @return string
     */
    protected function passwordKey(): string
    {
        return $this->passwordKey;
    }

    /**
     * get validate request rules
     *
     * @return array<string, string>
     */
    protected function validateRequestRules(): array
    {
        return [
            $this->userNameKey() => ["required", "email"],
            $this->passwordKey() => ["required", "string", "min:8", "max:32"],
        ];
    }

    /*----------------------------------------*
     * Rate Limit
     *----------------------------------------*/

    /**
     * whether use rate limit
     *
     * @var bool
     */
    protected bool $useRateLimit = true;

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
        if (!$this->attemptLogin()) return $this->failed();

        $this->sessionRegenerate();

        $this->clearRateLimit();

        $this->runLogoutOtherDevices();

        return null;
    }

    /*----------------------------------------*
     * Attempt
     *----------------------------------------*/

    /**
     * remember key
     *
     * @var string
     */
    protected string $rememberKey = "remember";

    /**
     * attempt login
     *
     * @return bool
     */
    protected function attemptLogin(): bool
    {
        return $this->guardInstance()
            ->attempt(
                $this->credentials(),
                $this->remember()
            );
    }

    /**
     * get credentials
     *
     * @return array<string, mixed>
     */
    protected function credentials(): array
    {
        return $this->requestOnly($this->credentialsKey());
    }

    /**
     * get credentials key
     *
     * @return array<string>
     */
    protected function credentialsKey(): array
    {
        return [
            $this->userNameKey(),
            $this->passwordKey()
        ];
    }

    /**
     * get remember
     *
     * @return bool
     */
    protected function remember(): bool
    {
        return $this->requestFilled($this->rememberKey());
    }

    /**
     * get remember key
     *
     * @return string
     */
    protected function rememberKey(): string
    {
        return $this->rememberKey;
    }

    /*----------------------------------------*
     * Session
     *----------------------------------------*/

    /**
     * session regenerate
     *
     * @return void
     */
    protected function sessionRegenerate(): void
    {
        $this->request->session()->regenerate();
    }

    /*----------------------------------------*
     * Logout Other Devices
     *----------------------------------------*/

    /**
     * whether logout other devices
     *
     * @var bool
     */
    protected bool $logoutOtherDevices = false;

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
     * whether logout other devices
     *
     * @return bool
     */
    protected function logoutOtherDevices(): bool
    {
        return $this->logoutOtherDevices;
    }

    /**
     * get password
     *
     * @return string
     */
    protected function password(): string
    {
        return $this->requestInput($this->passwordKey());
    }

    /*----------------------------------------*
     * Failed
     *----------------------------------------*/

    /**
     * with errors message key
     *
     * @var string
     */
    protected string $withErrorsMessageKey = "login.failure";

    /**
     * prepare failed redirect
     *
     * @return void
     */
    #[\Override]
    protected function prepareFailedRedirect(): void
    {
        $this->hitRateLimit();
    }

    /**
     * get with input key
     *
     * @return array<string>
     */
    protected function withInputKey(): array
    {
        return [
            $this->userNameKey(),
        ];
    }

    /*----------------------------------------*
     * Success
     *----------------------------------------*/

    /**
     * route
     *
     * @var string
     */
    protected string $route = "home";

    /**
     * with message key
     *
     * @var string
     */
    protected string $withMessageKey = "login.success";

    /**
     * get success redirect
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    #[\Override]
    protected function successRedirect(): RedirectResponse
    {
        return redirect()
            ->intended($this->route())
            ->with($this->with());
    }
}
