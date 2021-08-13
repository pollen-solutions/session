<?php

declare(strict_types=1);

namespace Pollen\Session;

use Symfony\Component\HttpFoundation\Session\SessionBagInterface;

/**
 * @internal
 */
final class SessionBagProxy implements SessionBagInterface
{
    private SessionBagInterface $bag;
    private array $data;
    private ?int $usageIndex;
    private $usageReporter;

    public function __construct(SessionBagInterface $bag, array &$data, ?int &$usageIndex, ?callable $usageReporter)
    {
        $this->bag = $bag;
        $this->data = &$data;
        $this->usageIndex = &$usageIndex;
        $this->usageReporter = $usageReporter;
    }

    public function getBag(): SessionBagInterface
    {
        ++$this->usageIndex;
        if ($this->usageReporter && 0 <= $this->usageIndex) {
            ($this->usageReporter)();
        }

        return $this->bag;
    }

    public function isEmpty(): bool
    {
        if (!isset($this->data[$this->bag->getStorageKey()])) {
            return true;
        }
        ++$this->usageIndex;
        if ($this->usageReporter && 0 <= $this->usageIndex) {
            ($this->usageReporter)();
        }

        return empty($this->data[$this->bag->getStorageKey()]);
    }

    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return $this->bag->getName();
    }

    /**
     * @inheritDoc
     */
    public function initialize(array &$array): void
    {
        ++$this->usageIndex;
        if ($this->usageReporter && 0 <= $this->usageIndex) {
            ($this->usageReporter)();
        }

        $this->data[$this->bag->getStorageKey()] = &$array;

        $this->bag->initialize($array);
    }

    /**
     * @inheritDoc
     */
    public function getStorageKey(): string
    {
        return $this->bag->getStorageKey();
    }

    /**
     * @inheritDoc
     */
    public function clear()
    {
        return $this->bag->clear();
    }
}