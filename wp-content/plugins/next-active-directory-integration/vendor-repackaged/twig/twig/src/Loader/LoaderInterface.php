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

namespace Dreitier\Nadi\Vendor\Twig\Loader;

use Dreitier\Nadi\Vendor\Twig\Error\LoaderError;
use Dreitier\Nadi\Vendor\Twig\Source;

/**
 * Interface all loaders must implement.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
interface LoaderInterface
{
    /**
     * Returns the source context for a given template logical name.
     *
     * @throws LoaderError When $name is not found
     */
    public function getSourceContext(string $name): Source;

    /**
     * Gets the cache key to use for the cache for a given template name.
     *
     * @throws LoaderError When $name is not found
     */
    public function getCacheKey(string $name): string;

    /**
     * @param int $time Timestamp of the last modification time of the cached template
     *
     * @throws LoaderError When $name is not found
     */
    public function isFresh(string $name, int $time): bool;

    /**
     * @return bool
     */
    public function exists(string $name);
}
