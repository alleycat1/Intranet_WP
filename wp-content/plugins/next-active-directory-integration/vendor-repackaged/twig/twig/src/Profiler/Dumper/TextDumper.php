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

namespace Dreitier\Nadi\Vendor\Twig\Profiler\Dumper;

use Dreitier\Nadi\Vendor\Twig\Profiler\Profile;

/**
 * @author Fabien Potencier <fabien@symfony.com>
 */
final class TextDumper extends BaseDumper
{
    protected function formatTemplate(Profile $profile, $prefix): string
    {
        return sprintf('%s└ %s', $prefix, $profile->getTemplate());
    }

    protected function formatNonTemplate(Profile $profile, $prefix): string
    {
        return sprintf('%s└ %s::%s(%s)', $prefix, $profile->getTemplate(), $profile->getType(), $profile->getName());
    }

    protected function formatTime(Profile $profile, $percent): string
    {
        return sprintf('%.2fms/%.0f%%', $profile->getDuration() * 1000, $percent);
    }
}
