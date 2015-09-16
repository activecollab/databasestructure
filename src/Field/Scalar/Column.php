<?php
namespace ActiveCollab\Id\Model\Column\Scalar;

abstract class Column
{
    public function __construct($name, $default_value = null)
    {

    }

    /**
     * @var bool
     */
    private $is_required = false;

    /**
     * Value of this column is required
     *
     * @return $this
     */
    public function &required()
    {
        $this->is_required = true;

        return $this;
    }

    /**
     * @var bool
     */
    private $is_unique = false;

    /**
     * Value of this column needs to be unique (in the given context)
     *
     * @param  array|string|null $context
     * @return $this
     */
    public function &unique($context = null)
    {
        $this->is_unique = true;

        return $this;
    }
}