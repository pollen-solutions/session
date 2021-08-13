<?php

declare(strict_types=1);

namespace Pollen\Session;

use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface as BaseFlashBagInterface;

interface FlashBagInterface extends BaseFlashBagInterface
{
    /**
     * Push a new value onto the end of parameter list.
     *
     * @param string $type
     * @param mixed $value
     *
     * @return void
     */
    public function push(string $type, $value): void;

    /**
     * Returns a parameter value without removing it.
     *
     * @param string $type
     * @param mixed $default
     *
     * @return string|array|object|null
     */
    public function read(string $type, $default = null);

    /**
     * Returns all parameter values without removing them.
     *
     * @return array
     */
    public function readAll(): array;

    /**
     * Removes a parameter by its key (type).
     *
     * @param string $type
     *
     * @return void
     */
    public function remove(string $type): void;

}