<?php

namespace ActiveCollab\DatabaseStructure\Association;

use ActiveCollab\DatabaseStructure\Type;
use ActiveCollab\DatabaseStructure\AssociationInterface;
use ActiveCollab\DatabaseStructure\FieldInterface;
use ActiveCollab\DatabaseStructure\Index;
use ActiveCollab\DatabaseStructure\Field\Composite\ForeignKey;
use Doctrine\Common\Inflector\Inflector;
use InvalidArgumentException;

/**
 * @package ActiveCollab\DatabaseStructure\Association
 */
class BelongsTo implements AssociationInterface
{
    use AssociationInterface\Implementation;

    /**
     * @var bool
     */
    private $optional = false;

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

        if (empty($target_type_name)) {
            $target_type_name = Inflector::pluralize($name);
        }

        $this->name = $name;
        $this->target_type_name = $target_type_name;
    }

    /**
     * Return true if this association is optional
     *
     * @return bool
     */
    public function getOptional()
    {
        return $this->optional;
    }

    /**
     * @param  boolean $value
     * @return $this
     */
    public function &optional($value)
    {
        $this->optional = (boolean) $value;

        return $this;
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
     * Return a list of indexes
     *
     * @return Index[]
     */
    public function getIndexes()
    {
        return [new Index($this->getFieldName())];
    }

    /**
     * Return field name
     *
     * @return string
     */
    public function getFieldName()
    {
        return $this->getName() . '_id';
    }

    /**
     * Build class methods
     *
     * @param string $namespace
     * @param Type   $source_type
     * @param Type   $target_type
     * @param array  $result
     */
    public function buildClassMethods($namespace, Type $source_type, Type $target_type, array &$result)
    {
        if ($namespace) {
            $namespace = '\\' . ltrim($namespace, '\\');
        }

        $target_instance_class = $namespace . '\\' . Inflector::classify(Inflector::singularize($target_type->getName()));

        $classified_association_name = Inflector::classify($this->getName());

        $getter_name = "get{$classified_association_name}";
        $setter_name = "set{$classified_association_name}";
        $fk_getter_name = "get{$classified_association_name}Id";
        $fk_setter_name = "set{$classified_association_name}Id";

        $result[] = '';
        $result[] = '    /**';
        $result[] = '     * Return ' . Inflector::singularize($source_type->getName()) . ' ' . $this->getName();
        $result[] = '     *';
        $result[] = '     * @return ' . $target_instance_class;
        $result[] = '     */';
        $result[] = '    public function ' . $getter_name . '()';
        $result[] = '    {';
        $result[] = '       return $this->pool->getById(' . var_export($target_instance_class, true) . ', $this->' . $fk_getter_name . '());';
        $result[] = '    }';


        $result[] = '';
        $result[] = '    /**';
        $result[] = '     * Set ' . Inflector::singularize($source_type->getName()) . ' ' . $this->getName();
        $result[] = '     *';
        $result[] = '     * @param  ' . $target_instance_class  . ' $value';
        $result[] = '     * @return $this';
        $result[] = '     */';
        $result[] = '    public function ' . $setter_name . '(' . $target_instance_class . ' $value' . ($this->getOptional() ? ' = null' : '') . ')';
        $result[] = '    {';

        if ($this->getOptional()) {
            $result[] = '       if (empty($value)) {';
            $result[] = '           $this->' . $fk_setter_name . '(0);';
            $result[] = '       } else {';
            $result[] = '           $this->' . $fk_setter_name . '($value->getId());';
            $result[] = '       }';
            $result[] = '';
            $result[] = '       return $this;';
        } else {
            $result[] = '       if (empty($value) || !$value->isLoaded()) {';
            $result[] = '           throw new \\InvalidArgumentException(\'Valid related instance is required\');';
            $result[] = '       }';
            $result[] = '';
            $result[] = '       $this->' . $fk_setter_name . '($value->getId());';
            $result[] = '';
            $result[] = '       return $this;';
        }

        $result[] = '    }';
    }
}