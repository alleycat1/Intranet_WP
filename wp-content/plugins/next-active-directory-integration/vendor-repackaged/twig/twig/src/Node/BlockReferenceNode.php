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

namespace Dreitier\Nadi\Vendor\Twig\Node;

use Dreitier\Nadi\Vendor\Twig\Compiler;

/**
 * Represents a block call node.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class BlockReferenceNode extends Node implements NodeOutputInterface
{
    public function __construct(string $name, int $lineno, string $tag = null)
    {
        parent::__construct([], ['name' => $name], $lineno, $tag);
    }

    public function compile(Compiler $compiler): void
    {
        $compiler
            ->addDebugInfo($this)
            ->write(sprintf("\$this->displayBlock('%s', \$context, \$blocks);\n", $this->getAttribute('name')))
        ;
    }
}
