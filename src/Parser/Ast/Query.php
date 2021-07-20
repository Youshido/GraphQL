<?php
/**
 * Date: 23.11.15
 *
 * @author Portey Vasil <portey@gmail.com>
 */

namespace Youshido\GraphQL\Parser\Ast;

use Youshido\GraphQL\Parser\Ast\Interfaces\FieldInterface;
use Youshido\GraphQL\Parser\Ast\Interfaces\FragmentInterface;
use Youshido\GraphQL\Parser\Location;

class Query extends AbstractAst implements FieldInterface
{

    use AstArgumentsTrait;
    use AstDirectivesTrait;

    /** @var string */
    protected $name;

    /** @var string */
    protected $alias;

    /** @var Field[]|Query[] */
    protected $fields = [];

    /**
     * Query constructor.
     *
     * @param string   $name
     * @param string   $alias
     * @param array    $arguments
     * @param array    $fields
     * @param array    $directives
     * @param Location $location
     */
    public function __construct($name, $alias, array $arguments, array $fields, array $directives, Location $location)
    {
        parent::__construct($location);

        $this->name      = $name;
        $this->alias     = $alias;
        $this->setFields($fields);
        $this->setArguments($arguments);
        $this->setDirectives($directives);
    }

    public function getName()
    {
        return $this->name;
    }

    /**
     * @return Field[]|Query[]|FragmentInterface[]
     */
    public function getFields()
    {
        return array_values($this->fields);
    }

    /**
     * @return bool
     */
    public function hasFields()
    {
        return (bool)count($this->fields);
    }

    /**
     * @param Field[]|Query[] $fields
     */
    public function setFields($fields)
    {
        /**
         * we cannot store fields by name because of TypedFragments
         */
        $this->fields = $fields;
        }

    public function getAlias()
    {
        return $this->alias;
    }

    public function hasField($name, $deep = false)
    {
        foreach ($this->getFields() as $field) {
            if ($field->getName() == $name) {
                return true;
            }

            if ($deep && $field instanceof Query) {
                if ($field->hasField($name)) {
                    return true;
                }
            }
        }

        return false;
    }

}
