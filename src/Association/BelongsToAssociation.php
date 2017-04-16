<?php

/*
 * This file is part of the Active Collab DatabaseStructure project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\DatabaseStructure\Association;

use ActiveCollab\DatabaseStructure\Association\ProgramToInterfaceInterface\Implementation as ProgramToInterfaceInterfaceImplementation;
use ActiveCollab\DatabaseStructure\Association\RequiredInterface\Implementation as RequiredInterfaceImplementation;
use ActiveCollab\DatabaseStructure\AssociationInterface;
use ActiveCollab\DatabaseStructure\Field\Composite\ForeignKeyField;
use ActiveCollab\DatabaseStructure\Index;
use ActiveCollab\DatabaseStructure\ProtectSetterInterface;
use ActiveCollab\DatabaseStructure\ProtectSetterInterface\Implementation as ProtectSetterInterfaceImplementation;
use ActiveCollab\DatabaseStructure\StructureInterface;
use ActiveCollab\DatabaseStructure\TypeInterface;
use Doctrine\Common\Inflector\Inflector;
use InvalidArgumentException;

/**
 * @package ActiveCollab\DatabaseStructure\Association
 */
class BelongsToAssociation extends Association implements
    AssociationInterface,
    InjectFieldsInsterface,
    InjectIndexesInsterface,
    ProgramToInterfaceInterface,
    ProtectSetterInterface,
    RequiredInterface
{
    use ProtectSetterInterfaceImplementation, AssociationInterface\Implementation, ProgramToInterfaceInterfaceImplementation, RequiredInterfaceImplementation;

    /**
     * $name is in singular. If $target_type_name is empty, it will be set to pluralized value of association name:.
     *
     * new BelongsToAssociation('author')
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
     * {@inheritdoc}
     */
    public function getFields()
    {
        return [
            (new ForeignKeyField($this->getFieldName()))
                ->required($this->isRequired()),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getIndexes()
    {
        return [new Index($this->getFieldName())];
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
        return 'belongs_to_' . md5($this->getVerboseConstraintName());
    }

    /**
     * Return verbose constraint name.
     *
     * @return string
     */
    public function getVerboseConstraintName()
    {
        return Inflector::singularize($this->getSourceTypeName()) . '_' . $this->getName() . '_constraint';
    }

    public function buildAssociatedEntitiesManagerConstructionLine(
        StructureInterface $structure,
        TypeInterface $source_type,
        TypeInterface $target_type,
        string $indent,
        array &$result
    )
    {
    }

    public function buildClassPropertiesAndMethods(
        StructureInterface $structure,
        TypeInterface $source_type,
        TypeInterface $target_type,
        array &$result
    )
    {
        $namespace = $structure->getNamespace();

        if ($namespace) {
            $namespace = '\\' . ltrim($namespace, '\\');
        }

        $target_instance_class = $namespace . '\\' . Inflector::classify(Inflector::singularize($target_type->getName()));

        $returns_and_accepts = $target_instance_class;
        if ($this->getAccepts()) {
            $returns_and_accepts = '\\' . ltrim($this->getAccepts(), '\\');
        }

        $classified_association_name = Inflector::classify($this->getName());

        $getter_name = "get{$classified_association_name}";
        $setter_name = "set{$classified_association_name}";
        $fk_getter_name = "get{$classified_association_name}Id";
        $fk_setter_name = "set{$classified_association_name}Id";

        $result[] = '';
        $result[] = '    /**';
        $result[] = '     * Return ' . Inflector::singularize($source_type->getName()) . ' ' . $this->getName() . '.';
        $result[] = '     *';
        $result[] = '     * @return ' . $returns_and_accepts . ($this->isRequired() ? '' : '|null');
        $result[] = '     */';
        $result[] = '    public function ' . $getter_name . '(): ' . ($this->isRequired() ? '' : '?') . $returns_and_accepts;
        $result[] = '    {';

        if ($this->isRequired()) {
            $result[] = '        return $this->pool->getById(' . var_export($target_instance_class, true) . ', $this->' . $fk_getter_name . '());';
        } else {
            $result[] = '        return $this->' . $fk_getter_name . '() ?';
            $result[] = '            $this->pool->getById(' . var_export($target_instance_class, true) . ', $this->' . $fk_getter_name . '()) :';
            $result[] = '            null;';
        }

        $result[] = '    }';

        $setter_access_level = $this->getProtectSetter() ? 'protected' : 'public';

        $result[] = '';
        $result[] = '    /**';
        $result[] = '     * Set ' . Inflector::singularize($source_type->getName()) . ' ' . $this->getName() . '.';
        $result[] = '     *';
        $result[] = '     * @param  ' . $returns_and_accepts . ($this->isRequired() ? '' : '|null') . ' $value';
        $result[] = '     * @return $this';
        $result[] = '     */';
        $result[] = '    ' . $setter_access_level . ' function &' . $setter_name . '(' . $returns_and_accepts . ' $value' . ($this->isRequired() ? '' : ' = null') . ')';
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
            $result[] = '            $this->' . $fk_setter_name . '(null);';
            $result[] = '        } else {';
            $result[] = '            $this->' . $fk_setter_name . '($value->getId());';
            $result[] = '        }';
            $result[] = '';
            $result[] = '        return $this;';
        }

        $result[] = '    }';
    }
}
