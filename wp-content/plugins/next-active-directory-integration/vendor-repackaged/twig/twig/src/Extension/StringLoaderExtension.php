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

namespace Dreitier\Nadi\Vendor\Twig\Extension {
use Dreitier\Nadi\Vendor\Twig\TwigFunction;

final class StringLoaderExtension extends AbstractExtension
{
    public function getFunctions(): array
    {
        return [
            new TwigFunction('template_from_string', 'dreitier_nadi_twig_template_from_string', ['needs_environment' => true]),
        ];
    }
}
}

namespace {
use Dreitier\Nadi\Vendor\Twig\Environment;
use Dreitier\Nadi\Vendor\Twig\TemplateWrapper;

/**
 * Loads a template from a string.
 *
 *     {{ include(template_from_string("Hello {{ name }}")) }}
 *
 * @param string $template A template as a string or object implementing __toString()
 * @param string $name     An optional name of the template to be used in error messages
 */
function dreitier_nadi_twig_template_from_string(Environment $env, $template, string $name = null): TemplateWrapper
{
    return $env->createTemplate((string) $template, $name);
}
}
