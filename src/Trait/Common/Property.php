<?php

namespace YukataRm\Laravel\Auth\Trait\Common;

use Illuminate\Http\Request;

/**
 * Property trait
 * 
 * @package YukataRm\Laravel\Auth\Trait\Common
 */
trait Property
{
    /**
     * get request
     * 
     * @return \Illuminate\Http\Request
     */
    protected function request(): Request
    {
        return request();
    }

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
