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

/**
 * Represents a parent node.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class ParentExpression extends AbstractExpression
{
    public function __construct(string $name, int $lineno, string $tag = null)
    {
        parent::__construct([], ['output' => false, 'name' => $name], $lineno, $tag);
    }

    public function compile(Compiler $compiler): void
    {
        if ($this->getAttribute('output')) {
            $compiler
                ->addDebugInfo($this)
                ->write('$this->displayParentBlock(')
                ->string($this->getAttribute('name'))
                ->raw(", \$context, \$blocks);\n")
            ;
        } else {
            $compiler
                ->raw('$this->renderParentBlock(')
                ->string($this->getAttribute('name'))
                ->raw(', $context, $blocks)')
            ;
        }
    }
}
