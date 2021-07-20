<?php
/**
 * Date: 23.11.15
 *
 * @author Portey Vasil <portey@gmail.com>
 */

namespace Youshido\GraphQL\Parser\Ast;


use Youshido\GraphQL\Parser\Ast\Interfaces\FieldInterface;
use Youshido\GraphQL\Parser\Location;

class Field extends AbstractAst implements FieldInterface
{
    use AstArgumentsTrait;
    use AstDirectivesTrait;

    /** @var string */
    private $name;

    /** @var string */
    private $alias;

    /**
     * @param string   $name
     * @param string   $alias
     * @param array    $arguments
     * @param array    $directives
     * @param Location $location
     */
    public function __construct($name, $alias, array $arguments, array $directives, Location $location)
    {
        parent::__construct($location);

        $this->name      = $name;
        $this->alias     = $alias;
        $this->setArguments($arguments);
        $this->setDirectives($directives);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return null|string
     */
    public function getAlias()
    {
        return $this->alias;
    }

    /**
     * @param null|string $alias
     */
    public function setAlias($alias)
    {
        $this->alias = $alias;
    }

    public function hasFields()
    {
        return false;
    }

    public function getFields()
    {
        return [];
    }

}
