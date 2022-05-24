<?php

declare(strict_types=1);

namespace Snicco\Enterprise\AuthBundle\Auth\TwoFactor\Domain;

/**
 * @internal
 *
 * @psalm-immutable
 *
 * @psalm-internal Snicco\Enterprise\AuthBundle
 */
final class TwoFactorChallenge
{
    /**
     * @var positive-int
     */
    public int $user_id;
    
    public int $expires_at;
    
    public string $hashed_validator;
    
    /**
     * @var positive-int $user_id
     */
    public function __construct(string $hashed_validator, int $user_id, int $expires_at) {
        $this->user_id = $user_id;
        $this->expires_at = $expires_at;
        $this->hashed_validator = $hashed_validator;
    }
    
}