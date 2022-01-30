<?php

declare(strict_types=1);

namespace Darkalchemy\Twig;

/**
 * Class SimpleSession.
 */
class SimpleSession
{
    /**
     * @param string $key The get to get from the session
     *
     * @return null|mixed
     */
    public function getSessionValue(string $key)
    {
        return $_SESSION[$key] ?? null;
    }
}
