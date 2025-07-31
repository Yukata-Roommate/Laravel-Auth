<?php

namespace YukataRm\Laravel\Auth\Controllers\Base;

use YukataRm\Laravel\Auth\Controllers\Base\FormController;

use Illuminate\Http\RedirectResponse;

/**
 * Single Failed Controller
 *
 * @package YukataRm\Laravel\Auth\Controllers\Base
 */
abstract class SingleFailedController extends FormController
{
    /**
     * with errors key
     *
     * @var string
     */
    protected string $withErrorsKey = "alert.failure";

    /**
     * with errors message key
     *
     * @var string
     */
    protected string $withErrorsMessageKey;

    /**
     * failed
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function failed(): RedirectResponse
    {
        $this->prepareFailedRedirect();

        return $this->failedRedirect(
            $this->withInput($this->withInputKey()),
            $this->withErrors()
        );
    }

    /**
     * prepare failed redirect
     *
     * @return void
     */
    protected function prepareFailedRedirect(): void {}

    /**
     * get with input key
     *
     * @return array<string>
     */
    abstract protected function withInputKey(): array;

    /**
     * get with errors
     *
     * @return array<string, mixed>
     */
    protected function withErrors(): array
    {
        return [
            $this->withErrorsKey() => $this->withErrorsMessage($this->withErrorsMessageKey())
        ];
    }

    /**
     * get with errors key
     *
     * @return string
     */
    protected function withErrorsKey(): string
    {
        return $this->withErrorsKey;
    }

    /**
     * get with errors message key
     *
     * @return string
     */
    protected function withErrorsMessageKey(): string
    {
        return $this->withErrorsMessageKey;
    }
}
