<?php

namespace App\Functions;

use Doctrine\ORM\Query\AST\Functions\FunctionNode;
use Doctrine\ORM\Query\TokenType;

class DateFormat extends FunctionNode
{

    public $date = null;
    public $format = null;

    // parsear la funcion y establecer las partes
    public function parse(\Doctrine\ORM\Query\Parser $parser): void
    {
        $parser->match(TokenType::T_IDENTIFIER);
        $parser->match(TokenType::T_OPEN_PARENTHESIS);
        $this->date = $parser->ArithmeticExpression();
        $parser->match(TokenType::T_COMMA);
        $this->format = $parser->StringPrimary();
        $parser->match(TokenType::T_CLOSE_PARENTHESIS);
    }

    // devuelve el sql nativo
    public function getSql(\Doctrine\ORM\Query\SqlWalker $sqlWalker): string
    {
        return "to_char(" . $this->date->dispatch($sqlWalker) . "," .
            $this->format->dispatch($sqlWalker) . ")";
    }

}
