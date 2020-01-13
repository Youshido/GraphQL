<?php

namespace Youshido\Tests\Schema;

use Youshido\GraphQL\Execution\Processor;
use Youshido\GraphQL\Schema\Schema;
use Youshido\GraphQL\Type\ListType\ListType;
use Youshido\GraphQL\Type\NonNullType;
use Youshido\GraphQL\Type\Object\ObjectType;
use Youshido\GraphQL\Type\Scalar\IdType;
use Youshido\GraphQL\Type\Scalar\StringType;

class VariablesTest extends \PHPUnit_Framework_TestCase
{
    public function testInvalidNullableList()
    {
        $schema = new Schema([
            'query' => new ObjectType([
                'name'   => 'RootQuery',
                'fields' => [
                    'list' => [
                        'type'    => new StringType(),
                        'args'    => [
                            'ids' => new ListType(new NonNullType(new IdType())),
                        ],
                        'resolve' => function () {
                            return 'item';
                        },
                    ],
                ],
            ]),
        ]);


        $processor = new Processor($schema);
        $processor->processPayload(
            'query getList($ids: [ID!]) { list(ids: $ids) }',
            [
                'ids' => [1, 12],
            ]
        );
        $this->assertEquals(['data' => ['list' => 'item']], $processor->getResponseData());

        $processor->getExecutionContext()->clearErrors();
        $processor->processPayload(
            'query getList($ids: [ID!]) { list(ids: $ids) }',
            [
                'ids' => [1, 12, null],
            ]
        );
        $this->assertEquals(['data' => ['list' => null], 'errors' => [
            [
                'message'   => 'Not valid type for argument "ids" in query "list": Field must not be NULL',
                'locations' => [
                    ['line' => 1, 'column' => 35],
                ],
            ],
        ]], $processor->getResponseData());

        $processor->getExecutionContext()->clearErrors();
        $processor->processPayload(
            'query getList($ids: [ID]) { list(ids: $ids) }',
            [
                'ids' => [1, 12, null],
            ]
        );
        $this->assertEquals(
            [
                'data'   => ['list' => null],
                'errors' => [
                    [
                        'message'   => 'Invalid variable "ids" type, allowed type is "ID"',
                        'locations' => [
                            [
                                'line'   => 1,
                                'column' => 15,
                            ],
                        ],
                    ],
                ],
            ],
            $processor->getResponseData());
    }

    /**
     * @dataProvider queries
     *
     * @param $query
     * @param $expected
     * @param $variables
     */
    public function testVariables($query, $expected, $variables)
    {
        $schema = new Schema([
            'query' => new ObjectType([
                'name'   => 'RootQuery',
                'fields' => [
                    'stringQuery' => [
                        'type'    => new StringType(),
                        'args'    => [
                            'sortOrder' => new StringType(),
                        ],
                        'resolve' => function ($args) {
                            return sprintf('Result with %s order', empty($args['sortOrder']) ? 'default' : $args['sortOrder']);
                        },
                    ],
                ],
            ]),
        ]);

        $processor = new Processor($schema);
        $processor->processPayload($query, $variables);
        $result = $processor->getResponseData();

        $this->assertEquals($expected, $result);
    }

    public function queries()
    {
        return [
            [
                'query ListQuery($sort:String) {
                  stringQuery(sortOrder: $sort)
                }',
                [
                    'data' => [
                        'stringQuery' => 'Result with default order',
                    ],
                ],
                [
                    'sort' => null,
                ],
            ],
            [
                'query queryWithVariable($abc:String) {
                  stringQuery {
                    ...someFragment
                  }
                }',
                [
                    'errors' => [
                        [
                            'message'   => 'Fragment "someFragment" is not defined in query',
                            'locations' => [
                                [
                                    'line'   => 3,
                                    'column' => 24,
                                ],
                            ],
                        ],
                    ],
                ],
                [
                    'abc' => null,
                ],
            ],
        ];
    }
}
