<?php

namespace ActiveCollab\DatabaseStructure\Association;

use ActiveCollab\DatabaseStructure\AssociationInterface;
use ActiveCollab\DatabaseStructure\Field\Composite\ForeignKey;
use ActiveCollab\DatabaseStructure\FieldInterface;
use Doctrine\Common\Inflector\Inflector;
use InvalidArgumentException;

/**
 * @package ActiveCollab\DatabaseStructure\Association
 */
class BelongsTo implements AssociationInterface
{
    use AssociationInterface\Implementation;

    /**
     * $name is in singular. If $target_type_name is empty, it will be set to pluralized value of association name:
     *
     * new BelongsTo('author')
     *
     * will result in $target_type_name pointing at 'authors' type
     *
     * @param string $name
     * @param null   $target_type_name
     */
    public function __construct($name, $target_type_name = null)
    {
        if (empty($name)) {
            throw new InvalidArgumentException("Value '$name' is not a valid association name");
        }

        $this->name = $name;
        $this->target_type_name = $target_type_name ? $target_type_name : Inflector::pluralize($name);
    }

    /**
     * Return a list of fields that are to be added to the source type
     *
     * @return FieldInterface[]
     */
    public function getFields()
    {
        return [new ForeignKey($this->getFieldName())];
    }

    /**
     * Return field name
     *
     * @return string
     */
    private function getFieldName()
    {
        return $this->getName() . '_id';
    }
}