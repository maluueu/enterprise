<?php

declare(strict_types=1);

namespace Snicco\Enterprise\Bundle\Auth\Session\Event;

use Snicco\Component\BetterWPHooks\EventMapping\MappedHook;
use Snicco\Component\EventDispatcher\ClassAsName;
use Snicco\Component\EventDispatcher\ClassAsPayload;
use WP_User;

use function time;
use function wp_doing_ajax;

final class SessionActivityRecorded implements MappedHook
{
    use ClassAsPayload;
    use ClassAsName;

    /**
     * @psalm-readonly
     */
    public string $raw_token;

    /**
     * @psalm-readonly
     */
    public int  $user_id;

    /**
     * @psalm-readonly
     */
    public int $timestamp;

    /**
     * @param array{
     *   token: string,
     * }  $cookie_elements
     */
    public function __construct(array $cookie_elements, WP_User $user)
    {
        $this->raw_token = $cookie_elements['token'];
        $this->user_id = $user->ID;
        $this->timestamp = time();
    }

    public function shouldDispatch(): bool
    {
        if (! wp_doing_ajax()) {
            return true;
        }

        if (! isset($_REQUEST['action'])) {
            return true;
        }

        return 'heartbeat' !== $_REQUEST['action'];
    }
}