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
 * @author Fabien Potencier <fabien@symfony.com>
 */
class CheckSecurityCallNode extends Node
{
    public function compile(Compiler $compiler)
    {
        $compiler
            ->write("\$this->sandbox = \$this->env->getExtension('\Dreitier\Nadi\Vendor\Twig\Extension\SandboxExtension');\n")
            ->write("\$this->checkSecurity();\n")
        ;
    }
}
