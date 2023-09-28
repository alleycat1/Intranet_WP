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

namespace Dreitier\Nadi\Vendor\Twig\Node;

use Dreitier\Nadi\Vendor\Twig\Compiler;

/**
 * Represents a sandbox node.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class SandboxNode extends Node
{
    public function __construct(Node $body, int $lineno, string $tag = null)
    {
        parent::__construct(['body' => $body], [], $lineno, $tag);
    }

    public function compile(Compiler $compiler): void
    {
        $compiler
            ->addDebugInfo($this)
            ->write("if (!\$alreadySandboxed = \$this->sandbox->isSandboxed()) {\n")
            ->indent()
            ->write("\$this->sandbox->enableSandbox();\n")
            ->outdent()
            ->write("}\n")
            ->write("try {\n")
            ->indent()
            ->subcompile($this->getNode('body'))
            ->outdent()
            ->write("} finally {\n")
            ->indent()
            ->write("if (!\$alreadySandboxed) {\n")
            ->indent()
            ->write("\$this->sandbox->disableSandbox();\n")
            ->outdent()
            ->write("}\n")
            ->outdent()
            ->write("}\n")
        ;
    }
}
