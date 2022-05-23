<?php

declare(strict_types=1);

namespace Snicco\Enterprise\Bundle\Auth;

use Defuse\Crypto\Key;
use RuntimeException;
use Snicco\Bundle\HttpRouting\HttpRoutingBundle;
use Snicco\Component\Kernel\Bundle;
use Snicco\Component\Kernel\Configuration\Config;
use Snicco\Component\Kernel\Configuration\WritableConfig;
use Snicco\Component\Kernel\Kernel;
use Snicco\Component\Kernel\ValueObject\Environment;
use Snicco\Enterprise\Bundle\Auth\Authentication\AuthenticationModule;

use Snicco\Enterprise\Bundle\Auth\Fail2Ban\Infrastructure\Fail2BanModule;
use Snicco\Enterprise\Bundle\Auth\Password\Infrastructure\PasswordModule;
use Snicco\Enterprise\Bundle\Auth\Session\SessionModule;

use function array_filter;
use function array_map;
use function in_array;

final class AuthBundle implements Bundle
{
    /**
     * @var string
     */
    public const ALIAS = 'snicco/auth-bundle';

    /**
     * @var array<class-string<AuthModule>>
     */
    private const MODULES = [
        SessionModule::class,
        PasswordModule::class,
        Fail2BanModule::class,
        AuthenticationModule::class,
    ];

    /**
     * @var AuthModule[]
     */
    private array $modules;

    /**
     * @var AuthModule[]|null
     */
    private ?array $enabled_modules = null;

    public function __construct()
    {
        $this->modules = array_map(fn (string $class): AuthModule => new $class(), self::MODULES);
    }

    public function shouldRun(Environment $env): bool
    {
        return true;
    }

    public function configure(WritableConfig $config, Kernel $kernel): void
    {
        $this->validateBundles($kernel);

        $config->setIfMissing('snicco_auth.modules', [
            'password',
            'session',
            'fail2ban',
            'authentication',
        ]);

        if ($kernel->env()->isTesting()) {
            $config->setIfMissing(
                'snicco_auth.encryption_secret',
                Key::createNewRandomKey()->saveToAsciiSafeString()
            );
        }

        foreach ($this->enabledModules($config) as $enabled_module) {
            $enabled_module->configure($config, $kernel);
        }
    }

    public function register(Kernel $kernel): void
    {
        $this->validateBundles($kernel);

        foreach ($this->enabledModules($kernel->config()) as $enabled_module) {
            $enabled_module->register($kernel);
        }
    }

    public function bootstrap(Kernel $kernel): void
    {
        foreach ($this->enabledModules($kernel->config()) as $enabled_module) {
            $enabled_module->boot($kernel);
        }
    }

    public function alias(): string
    {
        return self::ALIAS;
    }

    /**
     * @return AuthModule[]
     */
    private function enabledModules(Config $config): array
    {
        if (null === $this->enabled_modules) {
            $enabled = $config->getListOfStrings('snicco_auth.modules');

            $this->enabled_modules = array_filter(
                $this->modules,
                fn (AuthModule $module): bool => in_array($module->name(), $enabled, true)
            );
        }

        return $this->enabled_modules;
    }

    private function validateBundles(Kernel $kernel): void
    {
        if (! $kernel->usesBundle(HttpRoutingBundle::ALIAS)) {
            throw new RuntimeException(self::ALIAS . ' needs the ' . HttpRoutingBundle::ALIAS . ' to run.');
        }
    }
}
