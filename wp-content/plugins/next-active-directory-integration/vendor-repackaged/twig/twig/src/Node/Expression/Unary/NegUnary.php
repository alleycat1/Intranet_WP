<?php

/*
 * This file is part of Twig.
 *
 * (c) Fabien Potencier
 * (c) Armin Ronacher
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * Modified by __root__ on 14-September-2023 using Strauss.
 * @see https://github.com/BrianHenryIE/strauss
 */

namespace Dreitier\Nadi\Vendor\Twig\Node\Expression\Unary;

use Dreitier\Nadi\Vendor\Twig\Compiler;

class NegUnary extends AbstractUnary
{
    public function operator(Compiler $compiler): Compiler
    {
        return $compiler->raw('-');
    }
}
