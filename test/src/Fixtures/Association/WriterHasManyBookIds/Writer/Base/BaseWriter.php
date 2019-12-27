<?php

declare(strict_types=1);

namespace ActiveCollab\DatabaseStructure\Test\Fixtures\Association\WriterHasManyBookIds\Writer\Base;

abstract class BaseWriter extends \ActiveCollab\DatabaseStructure\Entity\Entity
{
    /**
     * Name of the table where records are stored.
     *
     * @var string
     */
    protected $table_name = 'writers';

    /**
     * Table fields that are managed by this entity.
     *
     * @var array
     */
    protected $fields = [
        'id',
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
                'books' => new \ActiveCollab\DatabaseStructure\Association\AssociatedEntitiesManager\HasManyAssociatedEntitiesManager(
                    $this->connection,
                    $this->pool,
                    'books',
                    'writer_id',
                    '\\ActiveCollab\\DatabaseStructure\\Test\\Fixtures\\Association\\WriterHasManyBookIds\\Book\\Book',
                    false
                ),
            ];
        }

        return $this->associated_entities_managers;
    }

    /**
     * {@inheritdoc}
     */
    public function &setAttribute($attribute, $value)
    {
        switch ($attribute) {
            case 'books':
                $this->getAssociatedEntitiesManagers()['books']->setAssociatedEntities($value);
                $this->recordModifiedAttribute('books');

                return $this;
            case 'book_ids':
                $this->getAssociatedEntitiesManagers()['books']->setAssociatedEntityIds($value);
                $this->recordModifiedAttribute('book_ids');

                return $this;
        }

        return parent::setAttribute($attribute, $value);
    }

    /**
     * Return writer books finder instance.
     *
     * @return \ActiveCollab\DatabaseObject\FinderInterface
     */
    protected function getBooksFinder(): \ActiveCollab\DatabaseObject\FinderInterface
    {
        return $this->pool
            ->find('\\ActiveCollab\\DatabaseStructure\\Test\\Fixtures\\Association\\WriterHasManyBookIds\\Book\\Book')
            ->where('`writer_id` = ?', $this->getId());
    }

    /**
     * Return writer books.
     *
     * @return iterable|null|\ActiveCollab\DatabaseStructure\Test\Fixtures\Association\WriterHasManyBookIds\Book\Book[]
     */
    public function getBooks(): ?iterable
    {
        return $this->getBooksFinder()->all();
    }

    /**
     * Return writer book ID-s.
     *
     * @return iterable|null|int[]
     */
    public function getBookIds(): ?iterable
    {
        return $this->getBooksFinder()->ids();
    }

    /**
     * Return number of writer books.
     *
     * @return int
     */
    public function countBooks(): int
    {
        return $this->getBooksFinder()->count();
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
