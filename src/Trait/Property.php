<?php

namespace YukataRm\Laravel\Auth\Trait;

/**
 * Property trait
 * 
 * @package YukataRm\Laravel\Auth\Trait
 */
trait Property
{
    /**
     * default guard
     * 
     * @var string
     */
    protected string $defaultGuard = "web";
    
    /**
     * default route
     * 
     * @var string
     */
    protected string $defaultRoute = "home";

    /**
     * get guard name
     * 
     * @return string
     */
    protected function guard(): string
    {
        return property_exists($this, "guard") ? $this->guard : $this->defaultGuard;
    }

    /**
     * get success redirect route
     * 
     * @return string
     */
    protected function route(): string
    {
        return property_exists($this, "route") ? $this->route : $this->defaultRoute;
    }
}
