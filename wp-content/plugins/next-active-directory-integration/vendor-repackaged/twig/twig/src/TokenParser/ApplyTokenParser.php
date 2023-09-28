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

use Dreitier\Nadi\Vendor\Twig\Node\Expression\TempNameExpression;
use Dreitier\Nadi\Vendor\Twig\Node\Node;
use Dreitier\Nadi\Vendor\Twig\Node\PrintNode;
use Dreitier\Nadi\Vendor\Twig\Node\SetNode;
use Dreitier\Nadi\Vendor\Twig\Token;

/**
 * Applies filters on a section of a template.
 *
 *   {% apply upper %}
 *      This text becomes uppercase
 *   {% endapply %}
 *
 * @internal
 */
final class ApplyTokenParser extends AbstractTokenParser
{
    public function parse(Token $token): Node
    {
        $lineno = $token->getLine();
        $name = $this->parser->getVarName();

        $ref = new TempNameExpression($name, $lineno);
        $ref->setAttribute('always_defined', true);

        $filter = $this->parser->getExpressionParser()->parseFilterExpressionRaw($ref, $this->getTag());

        $this->parser->getStream()->expect(Token::BLOCK_END_TYPE);
        $body = $this->parser->subparse([$this, 'decideApplyEnd'], true);
        $this->parser->getStream()->expect(Token::BLOCK_END_TYPE);

        return new Node([
            new SetNode(true, $ref, $body, $lineno, $this->getTag()),
            new PrintNode($filter, $lineno, $this->getTag()),
        ]);
    }

    public function decideApplyEnd(Token $token): bool
    {
        return $token->test('endapply');
    }

    public function getTag(): string
    {
        return 'apply';
    }
}
