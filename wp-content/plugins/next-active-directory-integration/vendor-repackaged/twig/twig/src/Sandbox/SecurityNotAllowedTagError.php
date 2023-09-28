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

namespace Dreitier\Nadi\Vendor\Twig\Sandbox;

/**
 * Exception thrown when a not allowed tag is used in a template.
 *
 * @author Martin Hasoň <martin.hason@gmail.com>
 */
final class SecurityNotAllowedTagError extends SecurityError
{
    private $tagName;

    public function __construct(string $message, string $tagName)
    {
        parent::__construct($message);
        $this->tagName = $tagName;
    }

    public function getTagName(): string
    {
        return $this->tagName;
    }
}
