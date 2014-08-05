<?php
namespace Boekkooi\Bundle\DoctrineJackBundle\Query\AST\Functions;

use Doctrine\DBAL\Platforms;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\Query\AST\Functions\FunctionNode;
use Doctrine\ORM\Query\Lexer;
use Doctrine\ORM\Query\Parser;
use Doctrine\ORM\Query\SqlWalker;

/**
 * @author Warnar Boekkooi <warnar@boekkooi.net>
 */
class RandFunction extends FunctionNode
{
    /**
     * {@inheritdoc}
     */
    public function getSql(SqlWalker $sqlWalker)
    {
        $platform = $sqlWalker->getConnection()->getDatabasePlatform();
        # http://dev.mysql.com/doc/refman/5.0/en/mathematical-functions.html#function_rand
        # http://msdn.microsoft.com/en-us/library/ms177610.aspx
        if ($platform instanceof Platforms\MySqlPlatform ||
            $platform instanceof Platforms\SQLServer2005Platform) {
            return 'RAND()';
        }
        # http://www.sqlite.org/lang_corefunc.html#random
        if ($platform instanceof Platforms\SqlitePlatform) {
            return '((random() / 18446744073709551616) + 0.5)';
        }
        # http://www.postgresql.org/docs/8.2/static/functions-math.html
        if ($platform instanceof Platforms\PostgreSqlPlatform) {
            return 'RANDOM()';
        }
        # http://docs.oracle.com/cd/B19306_01/appdev.102/b14258/d_random.htm
        if ($platform instanceof Platforms\OraclePlatform) {
            return 'DBMS_RANDOM.VALUE';
        }

        throw ORMException::notSupported();
    }

    /**
     * {@inheritdoc}
     */
    public function parse(Parser $parser)
    {
        $parser->match(Lexer::T_IDENTIFIER);
        $parser->match(Lexer::T_OPEN_PARENTHESIS);
        $parser->match(Lexer::T_CLOSE_PARENTHESIS);
    }
} 