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

namespace Dreitier\Nadi\Vendor\Twig\Node\Expression;

use Dreitier\Nadi\Vendor\Twig\Compiler;

class ConstantExpression extends AbstractExpression
{
    public function __construct($value, int $lineno)
    {
        parent::__construct([], ['value' => $value], $lineno);
    }

    public function compile(Compiler $compiler): void
    {
        $compiler->repr($this->getAttribute('value'));
    }
}
