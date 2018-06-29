<?php

namespace Youshido\Tests\Issues\Issue193;

use Youshido\GraphQL\Config\Schema\SchemaConfig;
use Youshido\GraphQL\Execution\Processor;
use Youshido\GraphQL\Schema\AbstractSchema;
use Youshido\GraphQL\Type\InterfaceType\AbstractInterfaceType;
use Youshido\GraphQL\Type\NonNullType;
use Youshido\GraphQL\Type\Object\AbstractObjectType;
use Youshido\GraphQL\Type\Scalar\IntType;
use Youshido\GraphQL\Type\Scalar\StringType;

class Issue193Test extends \PHPUnit_Framework_TestCase
{
    public function testResolvedInterfacesShouldBeRegistered()
    {
        $schema    = new Issue193Schema();
        $processor = new Processor($schema);

        $processor->processPayload($this->getIntrospectionQuery(), []);
        $resp = $processor->getResponseData();

        $typeNames = array_map(function ($type) {
            return $type['name'];
        }, $resp['data']['__schema']['types']);

        $this->assertContains('ContentBlockInterface', $typeNames);
        $this->assertContains('Post', $typeNames);
    }

    private function getIntrospectionQuery()
    {
        return <<<TEXT
query IntrospectionQuery {
    __schema {
        types {
            kind
          	name
        }
    }
}
TEXT;
    }
}

class Issue193Schema extends AbstractSchema
{
    public function build(SchemaConfig $config)
    {
        $config->getQuery()->addField(
            'post',
            [
                'type' => new ContentBlockInterface(),
            ]
        );
    }
}

class PostType extends AbstractObjectType
{

    public function build($config)
    {
        $config->applyInterface(new ContentBlockInterface());
        $config->addFields([
            'title'      => new NonNullType(new StringType()),
            'summary'    => new StringType(),
            'likesCount' => new IntType(),
        ]);
    }

    public function getInterfaces()
    {
        return [new ContentBlockInterface()];
    }
}

class ContentBlockInterface extends AbstractInterfaceType
{
    public function build($config)
    {
        $config->addField('title', new NonNullType(new StringType()));
        $config->addField('summary', new StringType());
    }

    public function resolveType($object)
    {
        // since there's only one type right now this interface will always resolve PostType
        return new PostType();
    }
}