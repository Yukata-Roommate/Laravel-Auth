<?php

namespace YukataRm\Laravel\Auth\Commands;

use YukataRm\Laravel\Command\BaseCommand;

use YukataRm\Laravel\Auth\Models\EmailResetToken;
use YukataRm\Laravel\Auth\Models\PasswordResetToken;
use YukataRm\Laravel\Db\Facades\Transaction;

/**
 * Delete Tokens Command
 *
 * @package YukataRm\Laravel\Auth\Commands
 */
class DeleteTokensCommand extends BaseCommand
{
    /**
     * command signature
     *
     * @var string
     */
    protected $signature = "token:delete";

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Delete expired tokens";

    /*----------------------------------------*
     * Parameter
     *----------------------------------------*/

    /**
     * set parameter
     *
     * @return void
     */
    protected function setParameter(): void {}

    /*----------------------------------------*
     * Process
     *----------------------------------------*/

    /**
     * run command process
     *
     * @return array<mixed>
     */
    protected function process(): array
    {
        $emailResetTokensCount = $this->deleteEmailResetTokens();

        $passwordResetTokensCount = $this->deletePasswordResetTokens();

        return [
            "count" => [
                "email_reset_tokens"    => $emailResetTokensCount,
                "password_reset_tokens" => $passwordResetTokensCount
            ],
        ];
    }

    /**
     * delete email reset tokens
     *
     * @return int
     */
    protected function deleteEmailResetTokens(): int
    {
        $emailResetTokens = EmailResetToken::all();

        $count = $emailResetTokens->count();

        if ($count === 0) return $count;

        Transaction::execute(function () use ($emailResetTokens) {
            foreach ($emailResetTokens as $emailResetToken) {
                if (!$emailResetToken->isExpired()) continue;

                $emailResetToken->delete();
            }
        });

        return $count;
    }

    /**
     * delete password reset tokens
     *
     * @return int
     */
    protected function deletePasswordResetTokens(): int
    {
        $passwordResetTokens = PasswordResetToken::all();

        $count = $passwordResetTokens->count();

        if ($count === 0) return $count;

        Transaction::execute(function () use ($passwordResetTokens) {
            foreach ($passwordResetTokens as $passwordResetToken) {
                if (!$passwordResetToken->isExpired()) continue;

                $passwordResetToken->delete();
            }
        });

        return $count;
    }
}
