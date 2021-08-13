<?php

declare(strict_types=1);

namespace Pollen\Session;

use Pollen\Support\Proxy\ContainerProxyInterface;

/**
 * @mixin SessionProcessor
 */
interface SessionManagerInterface extends ContainerProxyInterface
{
    /**
     * Gets CSRF protection token.
     *
     * @param string|null $tokenID
     *
     * @return string
     */
    public function getToken(?string $tokenID = null): string;

    /**
     * Retrieves related session processor instance.
     *
     * @return SessionProcessorInterface
     */
    public function processor(): SessionProcessorInterface;

    /**
     * Removes CSRF protection token.
     *
     * @param string|null $tokenID
     *
     * @return static
     */
    public function removeToken(?string $tokenID = null): SessionManagerInterface;

    /**
     * Sets CSRF protection token identifier.
     *
     * @param string|null $tokenID
     *
     * @return static
     */
    public function setTokenID(string $tokenID): SessionManagerInterface;

    /**
     * Checks validity of CSRF protection token.
     *
     * @param string $value
     * @param string|null $tokenID
     *
     * @return bool
     */
    public function verifyToken(string $value, ?string $tokenID = null): bool;
}