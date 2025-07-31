<?php

namespace YukataRm\Laravel\Auth\Controllers;

use YukataRm\Laravel\Auth\Controllers\Base\FormController;

use Illuminate\Http\RedirectResponse;

use YukataRm\Laravel\Auth\Models\PasswordResetToken;
use YukataRm\Laravel\Mail\Client;
use YukataRm\Random\Proxies\Random;
use YukataRm\Text\Proxies\Text;
use Carbon\Carbon;

use Illuminate\Foundation\Auth\User;

/**
 * Forgot Password Controller
 *
 * @package YukataRm\Laravel\Auth\Controllers
 */
abstract class ForgotPasswordController extends FormController
{
    /*========================================*
     * Form
     *========================================*/

    /**
     * form view
     *
     * @var string
     */
    protected string $view = "auth.forgot-password";

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
     * email key
     *
     * @var string
     */
    protected string $emailKey = "email";

    /**
     * get email key
     *
     * @return string
     */
    protected function emailKey(): string
    {
        return $this->emailKey;
    }

    /**
     * get validate request rules
     *
     * @return array<string, string>
     */
    protected function validateRequestRules(): array
    {
        return [
            $this->emailKey() => ["required", "email", "exists:users,email"],
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
        $user = $this->findUser();

        $passwordResetToken = $this->storeToken($user);

        if (is_null($passwordResetToken)) return $this->failedStoreToken();

        if (!$this->sendEmail($passwordResetToken, $user)) return $this->failedSendEmail();

        return null;
    }

    /*----------------------------------------*
     * Find User
     *----------------------------------------*/

    /**
     * find user
     *
     * @return \Illuminate\Foundation\Auth\User
     */
    protected function findUser(): User
    {
        return User::where($this->emailKey(), $this->email())->first();
    }

    /**
     * get email
     *
     * @return string
     */
    protected function email(): string
    {
        return $this->requestInput($this->emailKey());
    }

    /*----------------------------------------*
     * Store Token
     *----------------------------------------*/

    /**
     * expired minutes
     *
     * @var int
     */
    protected int $expiredMinutes = 10;

    /**
     * store token
     *
     * @param \Illuminate\Foundation\Auth\User $user
     * @return \YukataRm\Laravel\Auth\Models\PasswordResetToken|null
     */
    protected function storeToken(User $user): PasswordResetToken|null
    {
        $passwordResetToken = new PasswordResetToken();

        $passwordResetToken->user_id    = $user->id;
        $passwordResetToken->token      = $this->generateToken();
        $passwordResetToken->expired_at = Carbon::now()->addMinutes($this->expiredMinutes);

        $result = $this->runTransaction(function () use ($passwordResetToken) {
            $passwordResetToken->save();
        });

        return $result ? $passwordResetToken : null;
    }

    /**
     * generate token
     *
     * @return string
     */
    protected function generateToken(): string
    {
        return Random::password();
    }

    /*----------------------------------------*
     * Failed Store Token
     *----------------------------------------*/

    /**
     * store token with errors key
     *
     * @var string
     */
    protected string $storeTokenWithErrorsKey = "alert.failure";

    /**
     * store token with errors message key
     *
     * @var string
     */
    protected string $storeTokenWithErrorsMessageKey = "forgot-password.failure.issue-token";

    /**
     * failed store token
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function failedStoreToken(): RedirectResponse
    {
        return $this->failedRedirect(
            $this->withInput($this->storeTokenWithInputKey()),
            $this->storeTokenWithErrors()
        );
    }

    /**
     * get store token with input key
     *
     * @return array<string>
     */
    protected function storeTokenWithInputKey(): array
    {
        return [
            $this->emailKey(),
        ];
    }

    /**
     * get store token with errors
     *
     * @return array<string, mixed>
     */
    protected function storeTokenWithErrors(): array
    {
        return [
            $this->storeTokenWithErrorsKey() => $this->withErrorsMessage($this->storeTokenWithErrorsMessageKey())
        ];
    }

    /**
     * get store token with errors key
     *
     * @return string
     */
    protected function storeTokenWithErrorsKey(): string
    {
        return $this->storeTokenWithErrorsKey;
    }

    /**
     * get store token with errors message key
     *
     * @return string
     */
    protected function storeTokenWithErrorsMessageKey(): string
    {
        return $this->storeTokenWithErrorsMessageKey;
    }

    /*----------------------------------------*
     * Send Email
     *----------------------------------------*/

    /**
     * send email
     *
     * @param \YukataRm\Laravel\Auth\Models\PasswordResetToken $passwordResetToken
     * @param \Illuminate\Foundation\Auth\User $user
     * @return bool
     */
    protected function sendEmail(PasswordResetToken $passwordResetToken, User $user): bool
    {
        $client = new Client();

        $client->setSenderName($this->emailSenderName())
            ->setSenderAddress($this->emailSenderAddress())
            ->setRecipientName($user->name)
            ->setRecipientAddress($user->email)
            ->setSubject($this->emailSubject())
            ->setView($this->emailView())
            ->setWith($this->emailWith($passwordResetToken, $user));

        return $client->send();
    }

    /**
     * get email sender name
     *
     * @return string
     */
    protected function emailSenderName(): string
    {
        return config("mail.from.name");
    }

    /**
     * get email sender address
     *
     * @return string
     */
    protected function emailSenderAddress(): string
    {
        return config("mail.from.address");
    }

    /**
     * get email recipient name
     *
     * @param \Illuminate\Foundation\Auth\User $user
     * @return string
     */
    protected function emailRecipientName(User $user): string
    {
        return $user->name;
    }

    /**
     * get email subject
     *
     * @return string
     */
    protected function emailSubject(): string
    {
        return $this->lang("forgot-password.email.subject");
    }

    /**
     * get email view
     *
     * @return string
     */
    protected function emailView(): string
    {
        return "email.forgot-password";
    }

    /**
     * get email with
     *
     * @param \YukataRm\Laravel\Auth\Models\PasswordResetToken $passwordResetToken
     * @param \Illuminate\Foundation\Auth\User $user
     * @return array<string, mixed>
     */
    protected function emailWith(PasswordResetToken $passwordResetToken, User $user): array
    {
        return [
            "subject"            => $this->emailSubject(),
            "passwordResetToken" => $passwordResetToken,
            "user"               => $user,
            "remarksMessage"     => Text::enl2br($this->emailRemarksMessage()),
            "expiredMessage"     => $this->emailExpiredMessage(),
            "tokenMessage"       => $this->emailTokenMessage(),
        ];
    }

    /**
     * get email remarks message
     *
     * @return string
     */
    protected function emailRemarksMessage(): string
    {
        return $this->lang("forgot-password.email.message.remarks");
    }

    /**
     * get email expired message
     *
     * @return string
     */
    protected function emailExpiredMessage(): string
    {
        return $this->lang("forgot-password.email.message.expired");
    }

    /**
     * get email token message
     *
     * @return string
     */
    protected function emailTokenMessage(): string
    {
        return $this->lang("forgot-password.email.message.token");
    }

    /*----------------------------------------*
     * Failed Send Email
     *----------------------------------------*/

    /**
     * send email with errors key
     *
     * @var string
     */
    protected string $sendEmailWithErrorsKey = "alert.failure";

    /**
     * send email with errors message key
     *
     * @var string
     */
    protected string $sendEmailWithErrorsMessageKey = "forgot-password.failure.send-mail";

    /**
     * failed send email
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function failedSendEmail(): RedirectResponse
    {
        return $this->failedRedirect(
            $this->withInput($this->sendEmailWithInputKey()),
            $this->sendEmailWithErrors()
        );
    }

    /**
     * get send email with input key
     *
     * @return array<string>
     */
    protected function sendEmailWithInputKey(): array
    {
        return [
            $this->emailKey(),
        ];
    }

    /**
     * get send email with errors
     *
     * @return array<string, mixed>
     */
    protected function sendEmailWithErrors(): array
    {
        return [
            $this->sendEmailWithErrorsKey() => $this->withErrorsMessage($this->sendEmailWithErrorsMessageKey())
        ];
    }

    /**
     * get send email with errors key
     *
     * @return string
     */
    protected function sendEmailWithErrorsKey(): string
    {
        return $this->sendEmailWithErrorsKey;
    }

    /**
     * get send email with errors message key
     *
     * @return string
     */
    protected function sendEmailWithErrorsMessageKey(): string
    {
        return $this->sendEmailWithErrorsMessageKey;
    }

    /*----------------------------------------*
     * Success
     *----------------------------------------*/

    /**
     * route
     *
     * @var string
     */
    protected string $route = "auth.forgot-password-token.form";

    /**
     * with message key
     *
     * @var string
     */
    protected string $withMessageKey = "forgot-password.success.send-mail";
}
