<?php

namespace YukataRm\Laravel\Auth\Middlewares\Base;

use YukataRm\Laravel\Middleware\BaseMiddleware;

use YukataRm\Laravel\Auth\Lang;

/**
 * Auth Middleware
 *
 * @package YukataRm\Laravel\Auth\Middlewares\Base
 */
abstract class AuthMiddleware extends BaseMiddleware
{
    use Lang;
}
