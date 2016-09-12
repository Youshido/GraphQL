<?php
/**
 * Date: 03.12.15
 *
 * @author Portey Vasil <portey@gmail.com>
 */

namespace Youshido\GraphQL\Introspection;

use Youshido\GraphQL\Field\AbstractField;
use Youshido\GraphQL\Type\ListType\ListType;
use Youshido\GraphQL\Type\NonNullType;
use Youshido\GraphQL\Type\Object\AbstractObjectType;
use Youshido\GraphQL\Type\TypeMap;

class FieldType extends AbstractObjectType
{

    public function resolveType(AbstractField $value)
    {
        return $value->getType();
    }

    public function resolveArgs(AbstractField $value)
    {
        if ($value->getConfig()->hasArguments()) {
            return $value->getConfig()->getArguments();
        }

        return [];
    }

    public function build($config)
    {
        $config
            ->addField('name', new NonNullType(TypeMap::TYPE_STRING))
            ->addField('description', TypeMap::TYPE_STRING)
            ->addField('isDeprecated', new NonNullType(TypeMap::TYPE_BOOLEAN))
            ->addField('deprecationReason', TypeMap::TYPE_STRING)
            ->addField('type', [
                'type'    => new NonNullType(new QueryType()),
                'resolve' => [$this, 'resolveType'],
            ])
            ->addField('args', [
                'type'    => new NonNullType(new ListType(new NonNullType(new InputValueType()))),
                'resolve' => [$this, 'resolveArgs'],
            ]);
    }

    public function isValidValue($value)
    {
        return $value instanceof AbstractField;
    }

    /**
     * @return String type name
     */
    public function getName()
    {
        return '__Field';
    }
}
