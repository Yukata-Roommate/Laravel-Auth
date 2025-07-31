<?php

namespace YukataRm\Laravel\Auth;

/**
 * Auth Lang Trait
 *
 * @package YukataRm\Laravel\Auth
 */
trait Lang
{
    /**
     * get lang
     *
     * @param string $key
     * @param array<string, mixed> $replace
     * @return string
     */
    protected function lang(string $key, array $replace = []): string
    {
        return __("yr-auth::$key", $replace);
    }
}
