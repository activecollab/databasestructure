<?php

namespace ActiveCollab\DatabaseStructure\Behaviour;

/**
 * @package ActiveCollab\DatabaseStructure\Behaviour
 */
interface UpdatedAtInterface
{
    /**
     * Return object ID
     *
     * @return integer
     */
    public function getId();

    /**
     * Return value of updated_at field
     *
     * @return \ActiveCollab\DateValue\DateTimeValueInterface|null
     */
    public function getUpdatedAt();

    /**
     * @param  \ActiveCollab\DateValue\DateTimeValueInterface|null $value
     * @return $this
     */
    public function &setUpdatedAt($value);
}
