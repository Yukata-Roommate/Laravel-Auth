<?php

namespace YukataRm\Laravel\Auth\Middleware;

use YukataRm\Laravel\Middleware\BaseMiddleware;
use Symfony\Component\HttpFoundation\Response;

use Symfony\Component\HttpFoundation\IpUtils;
use Illuminate\Auth\Access\AuthorizationException;

/**
 * Firewall Middleware
 * 
 * @package YukataRm\Laravel\Auth\Middleware
 */
abstract class Firewall extends BaseMiddleware
{
    /**
     * run middleware handle
     * 
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function runHandle(): Response
    {
        if ($this->throughFirewall()) return $this->next();

        if ($this->isAllowedIp()) return $this->next();

        $this->throwAuthorizationException();
    }

    /*----------------------------------------*
     * Method
     *----------------------------------------*/

    /**
     * whether through firewall
     * 
     * @return bool
     */
    protected function throughFirewall(): bool
    {
        return false;
    }

    /**
     * get allowed ip addresses
     * 
     * @return array<string>
     */
    abstract protected function allowedIps(): array;

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
     * throw authorization exception
     * 
     * @return void
     */
    protected function throwAuthorizationException(): void
    {
        throw new AuthorizationException($this->exceptionMessage());
    }

    /**
     * get authorization exception message
     * 
     * @return string
     */
    protected function exceptionMessage(): string
    {
        return sprintf("Access Denied from %s", $this->request->ip());
    }
}
