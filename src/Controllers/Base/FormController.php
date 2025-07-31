<?php

namespace YukataRm\Laravel\Auth\Controllers\Base;

use YukataRm\Laravel\Auth\Controllers\Base\AuthController;

use Illuminate\View\View;

/**
 * Form Controller
 *
 * @package YukataRm\Laravel\Auth\Controllers\Base
 */
abstract class FormController extends AuthController
{
    /*========================================*
     * Form
     *========================================*/

    /**
     * form view
     *
     * @var string
     */
    protected string $view;

    /**
     * form data
     *
     * @var array<string, mixed>
     */
    protected array $data;

    /**
     * show form
     *
     * @return \Illuminate\View\View
     */
    public function form(): View
    {
        return view($this->view(), $this->data());
    }

    /**
     * get form view
     *
     * @return string
     */
    protected function view(): string
    {
        return $this->view;
    }

    /**
     * get form data
     *
     * @return array<string, mixed>
     */
    protected function data(): array
    {
        return $this->data;
    }
}
