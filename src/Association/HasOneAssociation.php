<?php

/*
 * This file is part of the Active Collab DatabaseStructure project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\DatabaseStructure\Association;

use ActiveCollab\DatabaseStructure\Association\ProgramToInterfaceInterface\Implementation as ProgramToInterfaceInterfaceImplementation;
use ActiveCollab\DatabaseStructure\Association\RequiredInterface\Implementation as RequiredInterfaceImplementation;
use ActiveCollab\DatabaseStructure\AssociationInterface;
use ActiveCollab\DatabaseStructure\AssociationInterface\Implementation as AssociationInterfaceImplementation;
use ActiveCollab\DatabaseStructure\Field\Composite\ForeignKeyField;
use ActiveCollab\DatabaseStructure\Index;
use ActiveCollab\DatabaseStructure\ProtectSetterInterface;
use ActiveCollab\DatabaseStructure\ProtectSetterInterface\Implementation as ProtectSetterInterfaceImplementation;
use ActiveCollab\DatabaseStructure\StructureInterface;
use ActiveCollab\DatabaseStructure\TypeInterface;
use Doctrine\Inflector\Inflector;
use Doctrine\Inflector\InflectorFactory;
use InvalidArgumentException;

class HasOneAssociation extends Association implements
    AssociationInterface,
    InjectFieldsInsterface,
    InjectIndexesInsterface,
    ProgramToInterfaceInterface,
    ProtectSetterInterface,
    RequiredInterface
{
    use
        AssociationInterfaceImplementation,
        ProgramToInterfaceInterfaceImplementation,
        ProtectSetterInterfaceImplementation,
        RequiredInterfaceImplementation;

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
            $target_type_name = $this->getInflector()->pluralize($name);
        }

        $this->name = $name;
        $this->target_type_name = $target_type_name;
    }

    public function getFields(): array
    {
        return [
            (new ForeignKeyField($this->getFieldName()))
                ->required($this->isRequired()),
        ];
    }

    public function getIndexes(): array
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
        return 'has_one_' . md5($this->getVerboseConstraintName());
    }

    /**
     * Return verbose constraint name.
     *
     * @return string
     */
    public function getVerboseConstraintName()
    {
        return $this->getInflector()->singularize($this->getSourceTypeName()) . '_' . $this->getName() . '_constraint';
    }

    public function getAttributes(): array
    {
        return [$this->getName()];
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

        $inflector = $this->getInflector();

        $target_instance_class = $namespace . '\\' . $inflector->classify($inflector->singularize($target_type->getName()));

        $returns_and_accepts = $target_instance_class;
        if ($this->getAccepts()) {
            $returns_and_accepts = '\\' . ltrim($this->getAccepts(), '\\');
        }

        $classified_association_name = $inflector->classify($this->getName());

        $getter_name = "get{$classified_association_name}";
        $setter_name = "set{$classified_association_name}";
        $fk_getter_name = "get{$classified_association_name}Id";
        $fk_setter_name = "set{$classified_association_name}Id";

        $inflector = $this->getInflector();

        $result[] = '';
        $result[] = '    /**';
        $result[] = '     * Return ' . $inflector->singularize($source_type->getName()) . ' ' . $this->getName() . '.';
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
        $result[] = '     * Set ' . $inflector->singularize($source_type->getName()) . ' ' . $this->getName() . '.';
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
            $result[] = '            $this->' . $fk_setter_name . '(0);';
            $result[] = '        } else {';
            $result[] = '            $this->' . $fk_setter_name . '($value->getId());';
            $result[] = '        }';
            $result[] = '';
            $result[] = '        return $this;';
        }

        $result[] = '    }';
    }

    private ?Inflector $inflector = null;

    private function getInflector(): Inflector
    {
        if ($this->inflector === null) {
            $this->inflector = InflectorFactory::create()->build();
        }

        return $this->inflector;
    }
}
