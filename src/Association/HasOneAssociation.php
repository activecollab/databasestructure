<?php

/*
 * This file is part of the Active Collab DatabaseStructure project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\DatabaseStructure\Association;

use ActiveCollab\DatabaseStructure\AssociationInterface;
use ActiveCollab\DatabaseStructure\Field\Composite\ForeignKeyField;
use ActiveCollab\DatabaseStructure\FieldInterface;
use ActiveCollab\DatabaseStructure\StructureInterface;
use ActiveCollab\DatabaseStructure\TypeInterface;
use Doctrine\Common\Inflector\Inflector;
use InvalidArgumentException;
use ActiveCollab\DatabaseStructure\ProtectSetterInterface;
use ActiveCollab\DatabaseStructure\ProtectSetterInterface\Implementation as ProtectSetterInterfaceImplementation;

/**
 * @package ActiveCollab\DatabaseStructure\Association
 */
class HasOneAssociation extends Association implements AssociationInterface, ProtectSetterInterface
{
    use AssociationInterface\Implementation, ProtectSetterInterfaceImplementation;

    /**
     * @param string $name
     * @param string $target_type_name
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
     * @var bool
     */
    private $is_required = true;

    /**
     * Return true if this field is required.
     *
     * @return bool
     */
    public function isRequired()
    {
        return $this->is_required;
    }

    /**
     * Value of this column is required.
     *
     * @param  bool  $value
     * @return $this
     */
    public function &required($value = true)
    {
        $this->is_required = (boolean) $value;

        return $this;
    }

    /**
     * Return a list of fields that are to be added to the source type.
     *
     * @return FieldInterface[]
     */
    public function getFields()
    {
        return [(new ForeignKeyField($this->getFieldName()))->required($this->isRequired())];
    }

    /**
     * Return field name.
     *
     * @return string
     */
    public function getFieldName()
    {
        return $this->getName() . '_id';
    }

    /**
     * Return association name.
     *
     * @return string
     */
    public function getConstraintName()
    {
        return Inflector::singularize($this->getSourceTypeName()) . '_' . $this->getName() . '_constraint';
    }

    /**
     * Build class methods.
     *
     * @param StructureInterface $structure
     * @param TypeInterface      $source_type
     * @param TypeInterface      $target_type
     * @param array              $result
     */
    public function buildClassMethods(StructureInterface $structure, TypeInterface $source_type, TypeInterface $target_type, array &$result)
    {
        $namespace = $structure->getNamespace();

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
        $result[] = '     * Return ' . Inflector::singularize($source_type->getName()) . ' ' . $this->getName() . '.';
        $result[] = '     *';
        $result[] = '     * @return ' . $target_instance_class;
        $result[] = '     */';
        $result[] = '    public function ' . $getter_name . '()';
        $result[] = '    {';
        $result[] = '        return $this->pool->getById(' . var_export($target_instance_class, true) . ', $this->' . $fk_getter_name . '());';
        $result[] = '    }';

        $setter_access_level = $this->getProtectSetter() ? 'protected' : 'public';

        $result[] = '';
        $result[] = '    /**';
        $result[] = '     * Set ' . Inflector::singularize($source_type->getName()) . ' ' . $this->getName() . '.';
        $result[] = '     *';
        $result[] = '     * @param  ' . $target_instance_class  . ' $value';
        $result[] = '     * @return $this';
        $result[] = '     */';
        $result[] = '    ' . $setter_access_level . ' function &' . $setter_name . '(' . $target_instance_class . ' $value' . ($this->isRequired() ? '' : ' = null') . ')';
        $result[] = '    {';

        if ($this->isRequired()) {
            $result[] = '        if (empty($value) || !$value->isLoaded()) {';
            $result[] = '            throw new \\InvalidArgumentException(\'Valid related instance is required\');';
            $result[] = '        }';
            $result[] = '';
            $result[] = '        $this->' . $fk_setter_name . '($value->getId());';
            $result[] = '';
            $result[] = '        return $this;';
        } else {
            $result[] = '        if (empty($value)) {';
            $result[] = '            $this->' . $fk_setter_name . '(0);';
            $result[] = '        } else {';
            $result[] = '            $this->' . $fk_setter_name . '($value->getId());';
            $result[] = '        }';
            $result[] = '';
            $result[] = '        return $this;';
        }

        $result[] = '    }';
    }
}
