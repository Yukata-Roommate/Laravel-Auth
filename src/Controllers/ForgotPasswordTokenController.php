<?php

namespace YukataRm\Laravel\Auth\Controllers;

use YukataRm\Laravel\Auth\Controllers\Base\FormController;

use Illuminate\Http\RedirectResponse;

use YukataRm\Laravel\Auth\Models\PasswordResetToken;
use Illuminate\Foundation\Auth\User;

use Illuminate\Support\Facades\Hash;

/**
 * Forgot Password Token Controller
 *
 * @package YukataRm\Laravel\Auth\Controllers
 */
abstract class ForgotPasswordTokenController extends FormController
{
    /*========================================*
     * Form
     *========================================*/

    /**
     * form view
     *
     * @var string
     */
    protected string $view = "auth.forgot-password-token";

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
     * token key
     *
     * @var string
     */
    protected string $tokenKey = "token";

    /**
     * password key
     *
     * @var string
     */
    protected string $passwordKey = "password";

    /**
     * get token key
     *
     * @return string
     */
    protected function tokenKey(): string
    {
        return $this->tokenKey;
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
            $this->tokenKey()    => ["required", "string", "exists:password_reset_tokens,token"],
            $this->passwordKey() => ["required", "confirmed", "min:8", "max:32"]
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
        $token = $this->findToken();

        if (is_null($token)) return $this->failedFindToken();

        if ($token->isExpired()) return $this->expiredToken();

        $user = $this->findUser($token);

        if (!$this->resetPassword($token, $user)) return $this->failedResetPassword();

        return null;
    }

    /*----------------------------------------*
     * Find Token
     *----------------------------------------*/

    /**
     * find email reset token
     *
     * @return \YukataRm\Laravel\Auth\Models\PasswordResetToken|null
     */
    protected function findToken(): PasswordResetToken|null
    {
        return PasswordResetToken::where("token", $this->token())->first();
    }

    /**
     * get token
     *
     * @return string
     */
    protected function token(): string
    {
        return $this->requestInput($this->tokenKey());
    }

    /*----------------------------------------*
     * Failed Find Token
     *----------------------------------------*/

    /**
     * find token with errors key
     *
     * @var string
     */
    protected string $failedFindTokenWithErrorsKey = "alert.failure";

    /**
     * find token with errors message key
     *
     * @var string
     */
    protected string $failedFindTokenWithErrorsMessageKey = "forgot-password.failure.find-token";

    /**
     * failed find token
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function failedFindToken(): RedirectResponse
    {
        return $this->failedRedirect(
            $this->withInput($this->findTokenWithInputKey()),
            $this->findTokenWithErrors()
        );
    }

    /**
     * get find token with input key
     *
     * @return array<string>
     */
    protected function findTokenWithInputKey(): array
    {
        return [
            $this->tokenKey()
        ];
    }

    /**
     * get find token with errors
     *
     * @return array<string, mixed>
     */
    protected function findTokenWithErrors(): array
    {
        return [
            $this->failedFindTokenWithErrorsKey() => $this->withErrorsMessage($this->failedFindTokenWithErrorsMessageKey())
        ];
    }

    /**
     * get failed find token with errors key
     *
     * @return string
     */
    protected function failedFindTokenWithErrorsKey(): string
    {
        return $this->failedFindTokenWithErrorsKey;
    }

    /**
     * get failed find token with errors message key
     *
     * @return string
     */
    protected function failedFindTokenWithErrorsMessageKey(): string
    {
        return $this->failedFindTokenWithErrorsMessageKey;
    }

    /*----------------------------------------*
     * Expired Token
     *----------------------------------------*/

    /**
     * expired token with errors key
     *
     * @var string
     */
    protected string $expiredTokenWithErrorsKey = "alert.failure";

    /**
     * expired token with errors message key
     *
     * @var string
     */
    protected string $expiredTokenWithErrorsMessageKey = "forgot-password.failure.expired-token";

    /**
     * expired token
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function expiredToken(): RedirectResponse
    {
        return $this->failedRedirect(
            $this->withInput($this->expiredTokenWithInputKey()),
            $this->expiredTokenWithErrors()
        );
    }

    /**
     * get expired token with input key
     *
     * @return array<string>
     */
    protected function expiredTokenWithInputKey(): array
    {
        return [
            $this->tokenKey()
        ];
    }

    /**
     * get expired token with errors
     *
     * @return array<string, mixed>
     */
    protected function expiredTokenWithErrors(): array
    {
        return [
            $this->expiredTokenWithErrorsKey() => $this->withErrorsMessage($this->expiredTokenWithErrorsMessageKey())
        ];
    }

    /**
     * get expired token with errors key
     *
     * @return string
     */
    protected function expiredTokenWithErrorsKey(): string
    {
        return $this->expiredTokenWithErrorsKey;
    }

    /**
     * get expired token with errors message key
     *
     * @return string
     */
    protected function expiredTokenWithErrorsMessageKey(): string
    {
        return $this->expiredTokenWithErrorsMessageKey;
    }

    /*----------------------------------------*
     * Find User
     *----------------------------------------*/

    /**
     * user primary key
     *
     * @var string
     */
    protected string $userPrimaryKey = "id";

    /**
     * find user
     *
     * @param \YukataRm\Laravel\Auth\Models\PasswordResetToken $token
     * @return \Illuminate\Foundation\Auth\User
     */
    protected function findUser(PasswordResetToken $token): User
    {
        return User::where($this->userPrimaryKey(), $token->user_id)->first();
    }

    /**
     * get user primary key
     *
     * @return string
     */
    protected function userPrimaryKey(): string
    {
        return $this->userPrimaryKey;
    }

    /*----------------------------------------*
     * Reset Password
     *----------------------------------------*/

    /**
     * reset password
     *
     * @param \YukataRm\Laravel\Auth\Models\PasswordResetToken $token
     * @param \Illuminate\Foundation\Auth\User $user
     * @return bool
     */
    protected function resetPassword(PasswordResetToken $token, User $user): bool
    {
        $user->{$this->passwordKey()} = Hash::make($this->password());

        return $this->runTransaction(function () use ($user, $token) {
            $user->save();

            $token->delete();
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
     * Failed Reset Password
     *----------------------------------------*/

    /**
     * reset password with errors key
     *
     * @var string
     */
    protected string $failedResetPasswordWithErrorsKey = "alert.failure";

    /**
     * reset password with errors message key
     *
     * @var string
     */
    protected string $failedResetPasswordWithErrorsMessageKey = "forgot-password.failure.reset";

    /**
     * failed reset password
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function failedResetPassword(): RedirectResponse
    {
        return $this->failedRedirect(
            $this->withInput($this->failedResetPasswordWithInputKey()),
            $this->failedResetPasswordWithErrors()
        );
    }

    /**
     * get failed reset password with input key
     *
     * @return array<string>
     */
    protected function failedResetPasswordWithInputKey(): array
    {
        return [
            $this->tokenKey()
        ];
    }

    /**
     * get failed reset password with errors
     *
     * @return array<string, mixed>
     */
    protected function failedResetPasswordWithErrors(): array
    {
        return [
            $this->failedResetPasswordWithErrorsKey() => $this->withErrorsMessage($this->failedResetPasswordWithErrorsMessageKey())
        ];
    }

    /**
     * get failed reset password with errors key
     *
     * @return string
     */
    protected function failedResetPasswordWithErrorsKey(): string
    {
        return $this->failedResetPasswordWithErrorsKey;
    }

    /**
     * get failed reset password with errors message key
     *
     * @return string
     */
    protected function failedResetPasswordWithErrorsMessageKey(): string
    {
        return $this->failedResetPasswordWithErrorsMessageKey;
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
    protected string $withMessageKey = "forgot-password.success.reset";
}
