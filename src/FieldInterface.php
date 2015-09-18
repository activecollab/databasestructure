<?php

namespace ActiveCollab\DatabaseStructure;

/**
 * @package ActiveCollab\DatabaseStructure
 */
interface FieldInterface
{
    const SIZE_TINY = 'tiny';
    const SIZE_SMALL = 'small';
    const SIZE_MEDIUM = 'medium';
    const SIZE_NORMAL = 'normal';
    const SIZE_BIG = 'big';

    /**
     * Return field name
     *
     * @return string
     */
    public function getName();

    /**
     * Method that is called when field is added to a type
     *
     * @param  Type $type
     */
    public function onAddedToType(Type &$type);
}