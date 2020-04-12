<?php

namespace Youshido\Tests\Issue242;

use PHPUnit\Framework\TestCase;
use Youshido\GraphQL\Parser\Parser;
use Youshido\GraphQL\Parser\Location;
use Youshido\GraphQL\Parser\Ast\Field;
use Youshido\GraphQL\Parser\Ast\Query;
use Youshido\GraphQL\Parser\Ast\Argument;
use Youshido\GraphQL\Parser\Ast\Directive;
use Youshido\GraphQL\Parser\Ast\ArgumentValue\Literal;

class Issue242Test extends TestCase
{
    public function testDoNotOverrideFirstLevelQueryDirectives()
    {
        /**
         * Without the patch, the operation query will override the first level
         * query directives.
         * In this case, directive `@include(if:false)` would be lost
         * Please notice: the bug happens only when adding "query" at the beginning:
         * query { ... }
         * So this query had no issue:
         * $payload = '{ user @include(if:false) { name } }';
         */
        $payload = 'query { user @include(if:false) { name } }';
        $parser  = new Parser();
        $data = $parser->parse($payload);
        $this->assertEquals([
            'queries'            => [
                new Query(
                    'user',
                    null,
                    [],
                    [
                        new Field('name', null, [], [], new Location(1, 35)),
                    ],
                    [
                        new Directive(
                            'include',
                            [
                                new Argument('if', new Literal(false, new Location(1, 26)), new Location(1, 23)),
                            ],
                            new Location(1, 15)
                        ),
                    ],
                    new Location(1, 9)
                )
            ],
            'mutations'          => [],
            'fragments'          => [],
            'fragmentReferences' => [],
            'variables'          => [],
            'variableReferences' => [],
        ], $data);
    }
}
