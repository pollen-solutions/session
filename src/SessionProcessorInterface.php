<?php

declare(strict_types=1);

namespace Pollen\Session;

use Countable;
use IteratorAggregate;
use Pollen\Session\Concerns\FlashBagAwareTraitInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

/**
 * @mixin \Symfony\Component\HttpFoundation\Session\Session
 */
interface SessionProcessorInterface extends
    Countable,
    FlashBagAwareTraitInterface,
    IteratorAggregate,
    SessionInterface
{
    /**
     * Sets a particular attribute key instance related to a session key.
     *
     * @param string $key
     * @param AttributeKeyBagInterface|null $bag
     *
     * @return AttributeKeyBagInterface
     */
    public function addAttributeKeyBag(string $key, ?AttributeKeyBagInterface $bag = null): AttributeKeyBagInterface;
}