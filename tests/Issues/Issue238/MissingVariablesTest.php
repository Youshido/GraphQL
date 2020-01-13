<?php

namespace Youshido\Tests\Issues\Issue238;

use PHPUnit\Framework\TestCase;
use Youshido\GraphQL\Execution\Processor;
use Youshido\Tests\StarWars\Schema\StarWarsSchema;

class MissingVariablesTest extends TestCase
{

    /**
     * @see https://github.com/youshido-php/GraphQL/issues/238
     */
    public function testProperExceptionOnMissingVariables()
    {
        $payload = <<<GQL
query GetHero {
    hero(episode:\$episode) {
        name
    }
}
GQL;

        $schema = new StarWarsSchema();
        $processor = new Processor($schema);

        $processor->processPayload($payload);
        $response = $processor->getResponseData();

        $this->assertEquals([
            'errors' => [
                [
                    'message' => 'Variable "episode" does not exists',
                    'locations' => [
                        [
                            'line' => 2,
                            'column' => 18,
                        ],
                    ],
                ],
            ],
        ], $response);
    }
}
