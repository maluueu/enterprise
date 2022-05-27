<?php

declare(strict_types=1);

namespace Snicco\Enterprise\Bundle\Fortress\Auth\User\Domain;

use WP_User;

interface UserProvider
{
    /**
     * @throws UserNotFound
     */
    public function getUserByIdentifier(string $identifier): WP_User;
}