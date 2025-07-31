<?php

namespace YukataRm\Laravel\Auth\Models;

use YukataRm\Laravel\Model\BaseModel;

/**
 * Password Reset Token Model
 *
 * @property int $id
 * @property int $user_id
 * @property string $token
 * @property \Carbon\Carbon $expired_at
 */
class PasswordResetToken extends BaseModel
{
    const CREATED_AT = null;
    const UPDATED_AT = null;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            "expired_at" => "datetime",
        ];
    }

    /*----------------------------------------*
     * Expired At
     *----------------------------------------*/

    /**
     * whether expired
     *
     * @return bool
     */
    public function isExpired(): bool
    {
        return $this->expired_at->isPast();
    }
}
