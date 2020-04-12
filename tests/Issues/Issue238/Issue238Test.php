<?php

namespace Youshido\Tests\Issue238;

use PHPUnit\Framework\TestCase;
use Youshido\GraphQL\Schema\Schema;
use Youshido\GraphQL\Execution\Processor;
use Youshido\GraphQL\Type\Scalar\IntType;
use Youshido\GraphQL\Execution\ResolveInfo;
use Youshido\GraphQL\Type\Object\ObjectType;
use Youshido\GraphQL\Type\Scalar\StringType;

class Issue238Test extends TestCase
{
    public function testNotDefinedVariableThrowsException()
    {
        $schema = new Schema([
            'query' => new ObjectType([
                'name'   => 'RootQuery',
                'fields' => [
                    'currentUser' => [
                        'type'    => new StringType(),
                        'args'    => [
                            'age' => [
                                'type'         => new IntType(),
                                'defaultValue' => 20,
                            ],
                        ],
                        'resolve' => static function ($source, $args, ResolveInfo $info) {
                            return 'Alex age ' . $args['age'];
                        },
                    ],
                ],
            ]),
        ]);

        $processor = new Processor($schema);

        /**
         * If using a variable in a query, it must be defined in the operation
         * $properQuery = 'query GetUserAge($age:Int) { currentUser(age:$age) }';
         * When not declaring the variable in the operation, it must return a nice error message
         * (instead of throwing a RuntimeException)
         */
        $unproperQuery = '{ currentUser(age:$age) }';
        $response = $processor->processPayload($unproperQuery)->getResponseData();
        if ($this->assertArrayHasKey(
            'errors',
            $response
        )) {
            $this->assertArraySubset(
                ['message' => 'Variable age hasn\'t been declared'],
                $response['errors']
            );
        }
    }
}
