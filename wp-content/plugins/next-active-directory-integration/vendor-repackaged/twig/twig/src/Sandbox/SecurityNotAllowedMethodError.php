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
 * Exception thrown when a not allowed class method is used in a template.
 *
 * @author Kit Burton-Senior <mail@kitbs.com>
 */
final class SecurityNotAllowedMethodError extends SecurityError
{
    private $className;
    private $methodName;

    public function __construct(string $message, string $className, string $methodName)
    {
        parent::__construct($message);
        $this->className = $className;
        $this->methodName = $methodName;
    }

    public function getClassName(): string
    {
        return $this->className;
    }

    public function getMethodName()
    {
        return $this->methodName;
    }
}
