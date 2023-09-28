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

namespace Dreitier\Nadi\Vendor\Twig\NodeVisitor;

use Dreitier\Nadi\Vendor\Twig\Environment;
use Dreitier\Nadi\Vendor\Twig\Node\Node;

/**
 * Interface for node visitor classes.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
interface NodeVisitorInterface
{
    /**
     * Called before child nodes are visited.
     *
     * @return Node The modified node
     */
    public function enterNode(Node $node, Environment $env): Node;

    /**
     * Called after child nodes are visited.
     *
     * @return Node|null The modified node or null if the node must be removed
     */
    public function leaveNode(Node $node, Environment $env): ?Node;

    /**
     * Returns the priority for this visitor.
     *
     * Priority should be between -10 and 10 (0 is the default).
     *
     * @return int The priority level
     */
    public function getPriority();
}
