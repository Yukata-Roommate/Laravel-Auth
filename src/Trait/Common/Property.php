<?php

namespace YukataRm\Laravel\Auth\Trait\Common;

/**
 * Property trait
 * 
 * @package YukataRm\Laravel\Auth\Trait\Common
 */
trait Property
{
    /**
     * get guard name
     * 
     * @return string
     */
    protected function guard(): string
    {
        return property_exists($this, "guard") ? $this->guard : "web";
    }

    /**
     * get success redirect route
     * 
     * @return string
     */
    protected function route(): string
    {
        return property_exists($this, "route") ? $this->route : "dashboard";
    }
}
