<?php

namespace YukataRm\Laravel\Auth\Middlewares;

use YukataRm\Laravel\Auth\Middlewares\Base\AuthMiddleware;
use Symfony\Component\HttpFoundation\Response;

use Symfony\Component\HttpFoundation\IpUtils;
use Illuminate\Auth\Access\AuthorizationException;

/**
 * Firewall Middleware
 *
 * @package YukataRm\Laravel\Auth\Middlewares
 */
abstract class Firewall extends AuthMiddleware
{
    /*----------------------------------------*
     * Handle
     *----------------------------------------*/

    /**
     * whether run middleware handle
     *
     * @return bool
     */
    #[\Override]
    protected function isRunHandle(): bool
    {
        if ($this->throughFirewall()) return false;

        if ($this->isAllowedIp()) return false;

        return true;
    }

    /**
     * run middleware handle
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function runHandle(): Response
    {
        $this->throwAuthorizationException();

        return $this->next();
    }

    /*----------------------------------------*
     * Through Firewall
     *----------------------------------------*/

    /**
     * whether through firewall
     *
     * @return bool
     */
    protected function throughFirewall(): bool
    {
        return app()->isLocal();
    }

    /*----------------------------------------*
     * Allowed Ip
     *----------------------------------------*/

    /**
     * whether ip address is allowed
     *
     * @return bool
     */
    protected function isAllowedIp(): bool
    {
        return IpUtils::checkIp($this->request->ip(), $this->allowedIps());
    }

    /**
     * get allowed ip addresses
     *
     * @return array<string>
     */
    abstract protected function allowedIps(): array;

    /*----------------------------------------*
     * Throw Exception
     *----------------------------------------*/

    /**
     * throw authorization exception
     *
     * @return void
     */
    protected function throwAuthorizationException(): void
    {
        throw new AuthorizationException($this->lang("middleware.firewall", ["ip" => $this->request->ip()]));
    }
}
