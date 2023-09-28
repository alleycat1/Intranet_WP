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

use Dreitier\Nadi\Vendor\Twig\NodeVisitor\OptimizerNodeVisitor;

final class OptimizerExtension extends AbstractExtension
{
    private $optimizers;

    public function __construct(int $optimizers = -1)
    {
        $this->optimizers = $optimizers;
    }

    public function getNodeVisitors(): array
    {
        return [new OptimizerNodeVisitor($this->optimizers)];
    }
}
