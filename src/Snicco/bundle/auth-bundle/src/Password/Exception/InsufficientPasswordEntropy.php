<?php

declare(strict_types=1);

namespace Snicco\Enterprise\Bundle\Auth\Password\Exception;

use InvalidArgumentException;

final class InsufficientPasswordEntropy extends InvalidArgumentException
{
}