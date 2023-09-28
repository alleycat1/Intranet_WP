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

namespace Dreitier\Nadi\Vendor\Twig\Extension;

/**
 * Enables usage of the deprecated Twig\Extension\AbstractExtension::getGlobals() method.
 *
 * Explicitly implement this interface if you really need to implement the
 * deprecated getGlobals() method in your extensions.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
interface GlobalsInterface
{
    public function getGlobals(): array;
}
