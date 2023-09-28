<?php

/*
 * This file is part of Twig.
 *
 * (c) Fabien Potencier
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * Modified by __root__ on 14-September-2023 using Strauss.
 * @see https://github.com/BrianHenryIE/strauss
 */

namespace Dreitier\Nadi\Vendor\Twig\Cache;

/**
 * Implements a no-cache strategy.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
final class NullCache implements CacheInterface
{
    public function generateKey(string $name, string $className): string
    {
        return '';
    }

    public function write(string $key, string $content): void
    {
    }

    public function load(string $key): void
    {
    }

    public function getTimestamp(string $key): int
    {
        return 0;
    }
}
