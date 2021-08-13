<?php

declare(strict_types=1);

namespace Pollen\Session\Concerns;

use Pollen\Session\FlashBagInterface;

interface FlashBagAwareTraitInterface
{
    /**
     * Retrieves flash bag instance|Sets flash bag parameters|Gets a parameter value by its key.
     *
     * @param string|array|null $key
     * @param mixed $default
     *
     * @return string|array|object|FlashBagInterface
     */
    public function flash($key = null, $default = null);

    /**
     * Sets related flash bag instance.
     *
     * @param FlashBagInterface $flashBag
     *
     * @return void
     */
    public function setFlashBag(FlashBagInterface $flashBag): void;
}