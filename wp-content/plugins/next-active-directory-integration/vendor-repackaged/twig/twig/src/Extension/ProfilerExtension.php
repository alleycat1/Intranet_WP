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

use Dreitier\Nadi\Vendor\Twig\Profiler\NodeVisitor\ProfilerNodeVisitor;
use Dreitier\Nadi\Vendor\Twig\Profiler\Profile;

class ProfilerExtension extends AbstractExtension
{
    private $actives = [];

    public function __construct(Profile $profile)
    {
        $this->actives[] = $profile;
    }

    /**
     * @return void
     */
    public function enter(Profile $profile)
    {
        $this->actives[0]->addProfile($profile);
        array_unshift($this->actives, $profile);
    }

    /**
     * @return void
     */
    public function leave(Profile $profile)
    {
        $profile->leave();
        array_shift($this->actives);

        if (1 === \count($this->actives)) {
            $this->actives[0]->leave();
        }
    }

    public function getNodeVisitors(): array
    {
        return [new ProfilerNodeVisitor(static::class)];
    }
}
