<?php

/*
 * This file is part of the Active Collab DatabaseStructure project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\DatabaseStructure\Association;

use ActiveCollab\DatabaseStructure\AssociationInterface;
use ActiveCollab\DatabaseStructure\StructureInterface;
use ActiveCollab\DatabaseStructure\TypeInterface;
use Doctrine\Common\Inflector\Inflector;
use InvalidArgumentException;

/**
 * @package ActiveCollab\DatabaseStructure\Association
 */
class HasOneAssociation extends Association implements AssociationInterface
{
    use AssociationInterface\Implementation;

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

        $fk_name = Inflector::singularize($source_type->getName()) . '_id';
        $getter_name = "get{$classified_association_name}";

        $result[] = '';
        $result[] = '    /**';
        $result[] = '     * Return ' . Inflector::singularize($source_type->getName()) . ' ' . $this->getName() . '.';
        $result[] = '     *';
        $result[] = '     * @return ' . $target_instance_class;
        $result[] = '     */';
        $result[] = '    public function ' . $getter_name . '()';
        $result[] = '    {';
        $result[] = '       return $this->pool->find(' . var_export($target_instance_class, true) . ')->where(\'`' . $fk_name . '` = ?\', $this->getId())->first();';
        $result[] = '    }';
    }
}
