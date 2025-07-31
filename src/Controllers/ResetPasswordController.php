<?php

namespace YukataRm\Laravel\Auth\Controllers;

use YukataRm\Laravel\Auth\Controllers\Base\SingleFailedController;

use Illuminate\Http\RedirectResponse;

use Illuminate\Support\Facades\Hash;

/**
 * Reset Password Controller
 *
 * @package YukataRm\Laravel\Auth\Controllers
 */
abstract class ResetPasswordController extends SingleFailedController
{
    /*========================================*
     * Form
     *========================================*/

    /**
     * form view
     *
     * @var string
     */
    protected string $view = "auth.reset-password";

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
     * current password key
     *
     * @var string
     */
    protected string $currentPasswordKey = "current_password";

    /**
     * password key
     *
     * @var string
     */
    protected string $passwordKey = "password";

    /**
     * get current password key
     *
     * @return string
     */
    protected function currentPasswordKey(): string
    {
        return $this->currentPasswordKey;
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
            $this->currentPasswordKey() => ["required", "string", "min:8", "max:32"],
            $this->passwordKey()        => ["required", "confirmed", "min:8", "max:32"],
        ];
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
        if (!$this->updatePassword()) return $this->failed();

        $this->guardLogout();

        return null;
    }

    /*----------------------------------------*
     * Update Password
     *----------------------------------------*/

    /**
     * update password
     *
     * @return bool
     */
    protected function updatePassword(): bool
    {
        $user = $this->guardUser();

        $user->{$this->passwordKey()} = Hash::make($this->password());

        return $this->runTransaction(function () use ($user) {
            $user->save();
        });
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
    protected string $withErrorsMessageKey = "reset-password.failure";

    /**
     * get with input key
     *
     * @return array<string>
     */
    protected function withInputKey(): array
    {
        return [];
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
    protected string $withMessageKey = "reset-password.success";
}
