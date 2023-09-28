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

namespace Dreitier\Nadi\Vendor\Twig\Node\Expression;

use Dreitier\Nadi\Vendor\Twig\Compiler;

class TempNameExpression extends AbstractExpression
{
    public function __construct(string $name, int $lineno)
    {
        parent::__construct([], ['name' => $name], $lineno);
    }

    public function compile(Compiler $compiler): void
    {
        $compiler
            ->raw('$_')
            ->raw($this->getAttribute('name'))
            ->raw('_')
        ;
    }
}
