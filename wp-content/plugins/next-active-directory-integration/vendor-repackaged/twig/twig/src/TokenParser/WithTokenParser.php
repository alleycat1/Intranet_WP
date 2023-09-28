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

namespace Dreitier\Nadi\Vendor\Twig\TokenParser;

use Dreitier\Nadi\Vendor\Twig\Node\Node;
use Dreitier\Nadi\Vendor\Twig\Node\WithNode;
use Dreitier\Nadi\Vendor\Twig\Token;

/**
 * Creates a nested scope.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 *
 * @internal
 */
final class WithTokenParser extends AbstractTokenParser
{
    public function parse(Token $token): Node
    {
        $stream = $this->parser->getStream();

        $variables = null;
        $only = false;
        if (!$stream->test(/* Token::BLOCK_END_TYPE */ 3)) {
            $variables = $this->parser->getExpressionParser()->parseExpression();
            $only = (bool) $stream->nextIf(/* Token::NAME_TYPE */ 5, 'only');
        }

        $stream->expect(/* Token::BLOCK_END_TYPE */ 3);

        $body = $this->parser->subparse([$this, 'decideWithEnd'], true);

        $stream->expect(/* Token::BLOCK_END_TYPE */ 3);

        return new WithNode($body, $variables, $only, $token->getLine(), $this->getTag());
    }

    public function decideWithEnd(Token $token): bool
    {
        return $token->test('endwith');
    }

    public function getTag(): string
    {
        return 'with';
    }
}
