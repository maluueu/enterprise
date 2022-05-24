<?php

declare(strict_types=1);

use Snicco\Bundle\BetterWPDB\BetterWPDBBundle;
use Snicco\Bundle\BetterWPHooks\BetterWPHooksBundle;
use Snicco\Bundle\HttpRouting\HttpRoutingBundle;
use Snicco\Component\Kernel\KernelOption;
use Snicco\Component\Kernel\ValueObject\Environment;
use Snicco\Enterprise\AuthBundle\AuthBundle;
use Snicco\Enterprise\Bundle\ApplicationLayer\ApplicationLayerBundle;

return [
    KernelOption::BUNDLES => [
        Environment::ALL => [
            HttpRoutingBundle::class,
            BetterWPDBBundle::class,
            BetterWPHooksBundle::class,
            AuthBundle::class,
            ApplicationLayerBundle::class,
        ],
    ],
];
