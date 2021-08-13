<?php

declare(strict_types=1);

namespace Pollen\Session\Concerns;

use InvalidArgumentException;
use Pollen\Session\FlashBagInterface;
use RuntimeException;
use Symfony\Component\HttpFoundation\Session\SessionBagInterface;

/**
 * @see \Pollen\Session\Concerns\FlashBagAwareTraitInterface
 */
trait FlashBagAwareTrait
{
    /**
     * Related flash bag instance.
     * @var FlashBagInterface|null
     */
    private ?FlashBagInterface $flashBag = null;

    /**
     * Retrieves flash bag instance|Sets flash bag parameters|Gets a parameter value by its key.
     *
     * @param string|array|null $key
     * @param mixed $default
     *
     * @return string|array|object|FlashBagInterface
     */
    public function flash($key = null, $default = null)
    {
        if (!$this->flashBag instanceof FlashBagInterface) {
            if (!method_exists($this, 'getFlashBag')) {
                throw new RuntimeException('FlashBag instance unavailable');
            }
            $this->setFlashBag($this->getFlashBag());
        }

        if (is_null($key)) {
            return $this->flashBag;
        }

        if (is_string($key)) {
            if (!$this->flashBag->has($key)) {
                return $default;
            }
            $value = $this->flashBag->get($key);

            if (count($value) <= 1 && is_scalar($value[0])) {
                return reset($value);
            }
            return $value;
        }

        if (is_array($key)) {
            foreach($key as $k => $v) {
                $this->flashBag->set($k, $v);
            }
            return $this->flashBag;
        }

        throw new InvalidArgumentException('FlashBag method arguments invalid');
    }

    /**
     * Sets related flash bag instance.
     *
     * @param SessionBagInterface|FlashBagInterface $flashBag
     *
     * @return void
     */
    public function setFlashBag(FlashBagInterface $flashBag): void
    {
        $this->flashBag = $flashBag;
    }
}