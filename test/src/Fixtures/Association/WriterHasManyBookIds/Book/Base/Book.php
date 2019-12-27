<?php

declare(strict_types=1);

namespace ActiveCollab\DatabaseStructure\Test\Fixtures\Association\WriterHasManyBookIds\Book\Base;

/**
 * @package ActiveCollab\DatabaseStructure\Test\Fixtures\Association\WriterHasManyBookIds\Book\Base
 */
abstract class Book extends \ActiveCollab\DatabaseStructure\Entity\Entity
{
    /**
     * Name of the table where records are stored.
     *
     * @var string
     */
    protected $table_name = 'books';

    /**
     * Table fields that are managed by this entity.
     *
     * @var array
     */
    protected $fields = [
        'id',
        'writer_id',
        'name',
    ];

    /**
     * List of default field values.
     *
     * @var array
     */
    protected $default_field_values = [
       'name' => '',
    ];

    /**
     * Generated fields that are loaded, but not managed by the entity..
     *
     * @var array
     */
    protected $generated_fields = [];

    /**
     * {@inheritdoc}
     */
    private $associated_entities_managers;

    /**
     * {@inheritdoc}
     */
    protected function getAssociatedEntitiesManagers(): array
    {
        if ($this->associated_entities_managers === null) {
            $this->associated_entities_managers  = [
            ];
        }

        return $this->associated_entities_managers;
    }

    /**
     * Return book writer.
     *
     * @return \ActiveCollab\DatabaseStructure\Test\Fixtures\Association\WriterHasManyBookIds\Writer|null
     */
    public function getWriter(): ?\ActiveCollab\DatabaseStructure\Test\Fixtures\Association\WriterHasManyBookIds\Writer
    {
        return $this->getWriterId() ?
            $this->pool->getById('\\ActiveCollab\\DatabaseStructure\\Test\\Fixtures\\Association\\WriterHasManyBookIds\\Writer', $this->getWriterId()) :
            null;
    }

    /**
     * Set book writer.
     *
     * @param  \ActiveCollab\DatabaseStructure\Test\Fixtures\Association\WriterHasManyBookIds\Writer|null $value
     * @return $this
     */
    public function &setWriter(\ActiveCollab\DatabaseStructure\Test\Fixtures\Association\WriterHasManyBookIds\Writer $value = null)
    {
        if (empty($value)) {
            $this->setWriterId(null);
        } else {
            $this->setWriterId($value->getId());
        }

        return $this;
    }

    /**
     * Return value of writer_id field.
     *
     * @return int|null
     */
    public function getWriterId(): ?int
    {
        return $this->getFieldValue('writer_id');
    }

    /**
     * Set value of writer_id field.
     *
     * @param  int|null $value
     * @return $this
     */
    public function &setWriterId(?int $value)
    {
        $this->setFieldValue('writer_id', $value);

        return $this;
    }

    /**
     * Return value of name field.
     *
     * @return string
     */
    public function getName(): string
    {
        $field_value = $this->getFieldValue('name');

        if ($field_value === null) {
            throw new \LogicException("Value of 'name' should not be accessed prior to being set.");
        }

        return $field_value;
    }

    /**
     * Set value of name field.
     *
     * @param  string $value
     * @return $this
     */
    public function &setName(string $value)
    {
        $this->setFieldValue('name', $value);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function &setFieldValue($name, $value)
    {
        if ($value === null) {
            parent::setFieldValue($name, null);
        } else {
            switch ($name) {
                case 'id':
                case 'writer_id':
                    return parent::setFieldValue($name, (int) $value);
                case 'name':
                    return parent::setFieldValue($name, (string) $value);
                default:
                    if ($this->isLoading()) {
                        return parent::setFieldValue($name, $value);
                    } else {
                        if ($this->isGeneratedField($name)) {
                            throw new \LogicException("Generated field $name cannot be set by directly assigning a value");
                        } else {
                            throw new \InvalidArgumentException("Field $name does not exist in this table");
                        }
                    }
            }
        }

        return $this;
    }

    /**
     * Prepare object properties so they can be serialized to JSON.
     *
     * @return array
     */
    public function jsonSerialize()
    {
        return array_merge(parent::jsonSerialize(), [
            'name' => $this->getName(),
        ]);
    }

    /**
     * Validate object properties before object is saved.
     *
     * @param \ActiveCollab\DatabaseObject\ValidatorInterface $validator
     */
    public function validate(\ActiveCollab\DatabaseObject\ValidatorInterface &$validator)
    {
        $validator->present('name');

        parent::validate($validator);
    }
}
