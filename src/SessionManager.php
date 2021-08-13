<?php

declare(strict_types=1);

namespace Pollen\Session;

use BadMethodCallException;
use Exception;
use Pollen\Support\Concerns\ConfigBagAwareTrait;
use Pollen\Support\Proxy\ContainerProxy;
use Pollen\Support\Exception\ManagerRuntimeException;
use Psr\Container\ContainerInterface as Container;
use SessionHandler;
use SessionHandlerInterface;
use Symfony\Component\HttpFoundation\Session\Storage\Handler\StrictSessionHandler;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManager;
use RuntimeException;
use Throwable;

/**
 * @mixin SessionProcessor
 */
class SessionManager implements SessionManagerInterface
{
    use ConfigBagAwareTrait;
    use ContainerProxy;

    /**
     * Session manager main instance.
     * @var static|null
     */
    private static ?SessionManagerInterface $instance = null;

    /**
     * Related session processor instance.
     * @var SessionProcessorInterface|null
     */
    private ?SessionProcessorInterface $sessionProcessor = null;

    /**
     * CSRF protection identifier.
     * @var string|null
     */
    protected ?string $tokenID = null;

    /**
     * @param array $config
     * @param Container|null $container
     */
    public function __construct(array $config = [], ?Container $container = null)
    {
        $this->setConfig($config);

        if ($container !== null) {
            $this->setContainer($container);
        }

        if (!self::$instance instanceof static) {
            self::$instance = $this;
        }
    }

    /**
     * Retrieves session manager main instance.
     *
     * @return static
     */
    public static function getInstance(): SessionManagerInterface
    {
        if (self::$instance instanceof self) {
            return self::$instance;
        }
        throw new ManagerRuntimeException(sprintf('Unavailable [%s] instance', __CLASS__));
    }

    /**
     * Delegates methods call of related session processor instance.
     *
     * @param string $method
     * @param array $arguments
     *
     * @return mixed
     *
     * @throws Exception
     */
    public function __call(string $method, array $arguments)
    {
        try {
            return $this->processor()->{$method}(...$arguments);
        } catch (Exception $e) {
            throw $e;
        } catch (Throwable $e) {
            throw new BadMethodCallException(
                sprintf(
                    'Session method call [%s] throws an exception: %s',
                    $method,
                    $e->getMessage()
                ), 0, $e
            );
        }
    }

    /**
     * Creates a new instance of session processor based on native session processor class.
     *
     * @return SessionProcessorInterface
     */
    protected function createNativeProcessor(): SessionProcessorInterface
    {
        $sessionStorage = new SessionStorage([], $this->createNativeHandler(), new MetadataBag());

        return $this->sessionProcessor = new SessionProcessor($sessionStorage, new AttributeBag(), new FlashBag());
    }

    /**
     * Creates a new instance of native session handler.
     *
     * @return SessionHandlerInterface
     */
    protected function createNativeHandler(): SessionHandlerInterface
    {
        return new StrictSessionHandler(new SessionHandler());
    }

    /**
     * Gets CSRF protection identifier.
     *
     * @return string
     */
    protected function getTokenID(): string
    {
        if ($this->tokenID === null) {
            throw new RuntimeException(
                '[Session Manager] Protection against CSRF attacks requires a valid key.'
            );
        }

        return $this->tokenID;
    }

    /**
     * @inheritDoc
     */
    public function getToken(?string $tokenID = null): string
    {
        if ($tokenID === null) {
            $tokenID = $this->getTokenID();
        }

        return (new CsrfTokenManager())->getToken($tokenID)->getValue();
    }

    /**
     * @inheritDoc
     */
    public function removeToken(?string $tokenID = null): SessionManagerInterface
    {
        if ($tokenID === null) {
            $tokenID = $this->getTokenID();
        }

        (new CsrfTokenManager())->removeToken($tokenID);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setTokenID(string $tokenID): SessionManagerInterface
    {
        $this->tokenID = $tokenID;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function processor(): SessionProcessorInterface
    {
        if ($this->sessionProcessor === null) {
            $this->sessionProcessor = $this->createNativeProcessor();
        }
        return $this->sessionProcessor;
    }

    /**
     * @inheritDoc
     */
    public function verifyToken(string $value, ?string $tokenID = null): bool
    {
        if ($tokenID === null) {
            $tokenID = $this->getTokenID();
        }
        $tokenManager = new CsrfTokenManager();
        $token = new CsrfToken($tokenID, $value);

        return $tokenManager->isTokenValid($token);
    }
}