<?php
/**
 * @license MIT
 *
 * Modified by __root__ on 14-September-2023 using Strauss.
 * @see https://github.com/BrianHenryIE/strauss
 */ declare(strict_types=1);

/*
 * This file is part of the Monolog package.
 *
 * (c) Jordi Boggiano <j.boggiano@seld.be>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Dreitier\Nadi\Vendor\Monolog\Processor;

/**
 * Adds value of getmypid into records
 *
 * @author Andreas Hörnicke
 */
class ProcessIdProcessor implements ProcessorInterface
{
    /**
     * {@inheritDoc}
     */
    public function __invoke(array $record): array
    {
        $record['extra']['process_id'] = getmypid();

        return $record;
    }
}
