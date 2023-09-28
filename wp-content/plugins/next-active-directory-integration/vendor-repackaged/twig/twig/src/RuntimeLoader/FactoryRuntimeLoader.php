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

namespace Dreitier\Nadi\Vendor\Twig\RuntimeLoader;

/**
 * Lazy loads the runtime implementations for a Twig element.
 *
 * @author Robin Chalas <robin.chalas@gmail.com>
 */
class FactoryRuntimeLoader implements RuntimeLoaderInterface
{
    private $map;

    /**
     * @param array $map An array where keys are class names and values factory callables
     */
    public function __construct(array $map = [])
    {
        $this->map = $map;
    }

    public function load(string $class)
    {
        if (!isset($this->map[$class])) {
            return null;
        }

        $runtimeFactory = $this->map[$class];

        return $runtimeFactory();
    }
}
