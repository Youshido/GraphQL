<?php
/**
 * Copyright (c) 2015–2018 Alexandr Viniychuk <http://youshido.com>.
 * Copyright (c) 2015–2018 Portey Vasil <https://github.com/portey>.
 * Copyright (c) 2018 Ryan Parman <https://github.com/skyzyx>.
 * Copyright (c) 2018 Ashley Hutson <https://github.com/asheliahut>.
 * Copyright (c) 2015–2018 Contributors.
 *
 * http://opensource.org/licenses/MIT
 */

declare(strict_types=1);
/*
 * This file is a part of graphql-youshido project.
 *
 * @author Alexandr Viniychuk <a@viniychuk.com>
 * created: 11/27/15 1:11 AM
 */

namespace Youshido\Tests\Library\Type;

use Youshido\GraphQL\Type\Scalar\AbstractScalarType;
use Youshido\GraphQL\Type\Scalar\DateTimeType;
use Youshido\GraphQL\Type\Scalar\StringType;
use Youshido\GraphQL\Type\TypeFactory;
use Youshido\GraphQL\Type\TypeMap;
use Youshido\GraphQL\Type\TypeService;

class ScalarTypeTest extends \PHPUnit_Framework_TestCase
{
    public function testScalarPrimitives(): void
    {
        foreach (TypeFactory::getScalarTypesNames() as $typeName) {
            $scalarType     = TypeFactory::getScalarType($typeName);
            $testDataMethod = 'get' . $typeName . 'TestData';

            $this->assertNotEmpty($scalarType->getDescription());
            $this->assertEquals($scalarType->getKind(), TypeMap::KIND_SCALAR);
            $this->assertEquals($scalarType->isCompositeType(), false);
            $this->assertEquals(TypeService::isAbstractType($scalarType), false);
            $this->assertEquals($scalarType->getType(), $scalarType);
            $this->assertEquals($scalarType->getType(), $scalarType->getNamedType());
            $this->assertNull($scalarType->getConfig());

            foreach (\call_user_func(['Youshido\Tests\DataProvider\TestScalarDataProvider', $testDataMethod]) as [$data, $serialized, $isValid]) {
                $this->assertSerialization($scalarType, $data, $serialized);
                $this->assertParse($scalarType, $data, $serialized, $typeName);

                if ($isValid) {
                    $this->assertTrue($scalarType->isValidValue($data), $typeName . ' validation for :' . \serialize($data));
                } else {
                    $this->assertFalse($scalarType->isValidValue($data), $typeName . ' validation for :' . \serialize($data));
                }
            }
        }

        try {
            TypeFactory::getScalarType('invalid type');
        } catch (\Exception $e) {
            $this->assertEquals('Configuration problem with type invalid type', $e->getMessage());
        }
        $this->assertEquals('String', (string) new StringType());
    }

    public function testDateTimeType(): void
    {
        $dateType = new DateTimeType('Y/m/d H:i:s');
        $this->assertEquals('2016/05/31 12:00:00', $dateType->serialize(new \DateTimeImmutable('2016-05-31 12:00pm')));
    }

    private function assertSerialization(AbstractScalarType $object, $input, $expected): void
    {
        $this->assertEquals($expected, $object->serialize($input), $object->getName() . ' serialize for: ' . \serialize($input));
    }

    private function assertParse(AbstractScalarType $object, $input, $expected, $typeName): void
    {
        $parsed = $object->parseValue($input);

        if ($parsed instanceof \DateTime) {
            $expected = \DateTime::createFromFormat('datetime' === $typeName ? 'Y-m-d H:i:s' : 'D, d M Y H:i:s O', $expected);
            $parsed   = \DateTime::createFromFormat('Y-m-d H:i:s', $parsed->format('Y-m-d H:i:s'));
        }

        $this->assertEquals($expected, $parsed, $object->getName() . ' parse for: ' . \serialize($input));
    }
}
