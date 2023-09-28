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

use Dreitier\Nadi\Vendor\Twig\Node\Expression\AssignNameExpression;
use Dreitier\Nadi\Vendor\Twig\Node\ImportNode;
use Dreitier\Nadi\Vendor\Twig\Node\Node;
use Dreitier\Nadi\Vendor\Twig\Token;

/**
 * Imports macros.
 *
 *   {% import 'forms.html' as forms %}
 *
 * @internal
 */
final class ImportTokenParser extends AbstractTokenParser
{
    public function parse(Token $token): Node
    {
        $macro = $this->parser->getExpressionParser()->parseExpression();
        $this->parser->getStream()->expect(/* Token::NAME_TYPE */ 5, 'as');
        $var = new AssignNameExpression($this->parser->getStream()->expect(/* Token::NAME_TYPE */ 5)->getValue(), $token->getLine());
        $this->parser->getStream()->expect(/* Token::BLOCK_END_TYPE */ 3);

        $this->parser->addImportedSymbol('template', $var->getAttribute('name'));

        return new ImportNode($macro, $var, $token->getLine(), $this->getTag(), $this->parser->isMainScope());
    }

    public function getTag(): string
    {
        return 'import';
    }
}
