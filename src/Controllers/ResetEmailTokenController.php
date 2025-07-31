<?php

namespace YukataRm\Laravel\Auth\Controllers;

use YukataRm\Laravel\Auth\Controllers\Base\FormController;

use Illuminate\Http\RedirectResponse;

use YukataRm\Laravel\Auth\Models\EmailResetToken;

/**
 * Reset Email Token Controller
 *
 * @package YukataRm\Laravel\Auth\Controllers
 */
abstract class ResetEmailTokenController extends FormController
{
    /*========================================*
     * Form
     *========================================*/

    /**
     * form view
     *
     * @var string
     */
    protected string $view = "auth.reset-email-token";

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
     * get token key
     *
     * @return string
     */
    protected function tokenKey(): string
    {
        return $this->tokenKey;
    }

    /**
     * get validate request rules
     *
     * @return array<string, string>
     */
    protected function validateRequestRules(): array
    {
        return [
            $this->tokenKey() => ["required", "string", "exists:email_reset_tokens,token"],
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

        if (!$this->resetEmail($token)) return $this->failedResetEmail();

        return null;
    }

    /*----------------------------------------*
     * Find Token
     *----------------------------------------*/

    /**
     * find email reset token
     *
     * @return \YukataRm\Laravel\Auth\Models\EmailResetToken|null
     */
    protected function findToken(): EmailResetToken|null
    {
        return EmailResetToken::where("token", $this->token())->first();
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
    protected string $failedFindTokenWithErrorsMessageKey = "reset-email.failure.find-token";

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
    protected string $expiredTokenWithErrorsMessageKey = "reset-email.failure.expired-token";

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
     * Reset Email
     *----------------------------------------*/

    /**
     * reset email
     *
     * @param \YukataRm\Laravel\Auth\Models\EmailResetToken $token
     * @return bool
     */
    protected function resetEmail(EmailResetToken $token): bool
    {
        $user = $this->guardUser();

        $user->email = $token->email;

        return $this->runTransaction(function () use ($user, $token) {
            $user->save();

            $token->delete();
        });
    }

    /*----------------------------------------*
     * Failed Reset Email
     *----------------------------------------*/

    /**
     * reset email with errors key
     *
     * @var string
     */
    protected string $failedResetEmailWithErrorsKey = "alert.failure";

    /**
     * reset email with errors message key
     *
     * @var string
     */
    protected string $failedResetEmailWithErrorsMessageKey = "reset-email.failure.reset";

    /**
     * failed reset email
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function failedResetEmail(): RedirectResponse
    {
        return $this->failedRedirect(
            $this->withInput($this->failedResetEmailWithInputKey()),
            $this->failedResetEmailWithErrors()
        );
    }

    /**
     * get failed reset email with input key
     *
     * @return array<string>
     */
    protected function failedResetEmailWithInputKey(): array
    {
        return [
            $this->tokenKey()
        ];
    }

    /**
     * get failed reset email with errors
     *
     * @return array<string, mixed>
     */
    protected function failedResetEmailWithErrors(): array
    {
        return [
            $this->failedResetEmailWithErrorsKey() => $this->withErrorsMessage($this->failedResetEmailWithErrorsMessageKey())
        ];
    }

    /**
     * get failed reset email with errors key
     *
     * @return string
     */
    protected function failedResetEmailWithErrorsKey(): string
    {
        return $this->failedResetEmailWithErrorsKey;
    }

    /**
     * get failed reset email with errors message key
     *
     * @return string
     */
    protected function failedResetEmailWithErrorsMessageKey(): string
    {
        return $this->failedResetEmailWithErrorsMessageKey;
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
    protected string $withMessageKey = "reset-email.success.reset";
}
