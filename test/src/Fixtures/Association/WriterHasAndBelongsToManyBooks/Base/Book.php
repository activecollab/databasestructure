<?php

declare(strict_types=1);

namespace ActiveCollab\DatabaseStructure\Test\Fixtures\Association\WriterHasAndBelongsToManyBooks\Base;

use ActiveCollab\DatabaseStructure\Test\Fixtures\Association\WriterHasAndBelongsToManyBooks\BookInterface;
use ActiveCollab\DatabaseStructure\Entity\Entity as BaseEntity;

abstract class Book extends BaseEntity implements BookInterface
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
    protected $entity_fields = [
        'id',
        'name',
    ];

    /**
     * List of default field values.
     *
     * @var array
     */
    protected $default_entity_field_values = [
       'name' => '',
    ];

    /**
     * Generated fields that are loaded, but not managed by the entity..
     *
     * @var array
     */
    protected $generated_entity_fields = [];

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
                'writers' => new \ActiveCollab\DatabaseStructure\Association\AssociatedEntitiesManager\HasAndBelongsToManyAssociatedEntitiesManager(
                    $this->connection,
                    $this->pool,
                    'books_writers',
                    'book_id',
                    'writer_id',
                    '\\ActiveCollab\\DatabaseStructure\\Test\\Fixtures\\Association\\WriterHasAndBelongsToManyBooks\\Writer',
                    true
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
            case 'writers':
                $this->getAssociatedEntitiesManagers()['writers']->setAssociatedEntities($value);
                $this->recordModifiedAttribute('writers');

                return $this;
            case 'writer_ids':
                $this->getAssociatedEntitiesManagers()['writers']->setAssociatedEntityIds($value);
                $this->recordModifiedAttribute('writer_ids');

                return $this;
        }

        return parent::setAttribute($attribute, $value);
    }

    /**
     * Return book writers finder instance.
     *
     * @return \ActiveCollab\DatabaseObject\FinderInterface
     */
    protected function getWritersFinder(): \ActiveCollab\DatabaseObject\FinderInterface
    {
        return $this->pool
            ->find('\\ActiveCollab\\DatabaseStructure\\Test\\Fixtures\\Association\\WriterHasAndBelongsToManyBooks\\Writer')
            ->joinTable('books_writers')
            ->where('`books_writers`.`book_id` = ?', $this->getId());
    }

    /**
     * Return book writers.
     *
     * @return iterable|null|\ActiveCollab\DatabaseStructure\Test\Fixtures\Association\WriterHasAndBelongsToManyBooks\Writer[]
     */
    public function getWriters(): ?iterable
    {
        return $this->getWritersFinder()->all();
    }

    /**
     * Return book writer ID-s.
     *
     * @return iterable|null|int[]
     */
    public function getWriterIds(): ?iterable
    {
        return $this->getWritersFinder()->ids();
    }

    /**
     * Return number of book writers.
     *
     * @return int
     */
    public function countWriters(): int
    {
        return $this->getWritersFinder()->count();
    }

    /**
     * Create connection between this book and one or more $objects_to_add.
     *
     * @param  \ActiveCollab\DatabaseStructure\Test\Fixtures\Association\WriterHasAndBelongsToManyBooks\Writer[] $objects_to_add
     * @return $this
     */
    public function &addWriters(\ActiveCollab\DatabaseStructure\Test\Fixtures\Association\WriterHasAndBelongsToManyBooks\Writer ...$objects_to_add)
    {
        if ($this->isNew()) {
            throw new \RuntimeException('Book needs to be saved first');
        }

        $batch = new \ActiveCollab\DatabaseConnection\BatchInsert\BatchInsert(
            $this->connection,
            'books_writers',
            ['book_id', 'writer_id'],
            50,
            \ActiveCollab\DatabaseConnection\ConnectionInterface::REPLACE
        );

        foreach ($objects_to_add as $object_to_add) {
            if ($object_to_add->isNew()) {
                throw new \RuntimeException('All writer needs to be saved first');
            }

            $batch->insert($this->getId(), $object_to_add->getId());
        }

        $batch->done();

        return $this;
    }

    /**
     * Drop connection between this book and $object_to_remove.
     *
     * @param  \ActiveCollab\DatabaseStructure\Test\Fixtures\Association\WriterHasAndBelongsToManyBooks\Writer[] $objects_to_remove
     * @return $this
     */
    public function &removeWriters(\ActiveCollab\DatabaseStructure\Test\Fixtures\Association\WriterHasAndBelongsToManyBooks\Writer ...$objects_to_remove)
    {
        if ($this->isNew()) {
            throw new \RuntimeException('Book needs to be saved first');
        }

        $ids_to_remove = [];

        foreach ($objects_to_remove as $object_to_remove) {
            if ($object_to_remove->isNew()) {
                throw new \RuntimeException('All writer needs to be saved first');
            }

            $ids_to_remove[] = $object_to_remove->getId();
        }

        if (!empty($ids_to_remove)) {
            $this->connection->execute('DELETE FROM `books_writers` WHERE `book_id` = ? AND `writer_id` IN ?', $this->getId(), $ids_to_remove);
        }

        return $this;
    }

    /**
     * Drop all connections between writers and this book.
     *
     * @return $this
     */
    public function &clearWriters()
    {
        if ($this->isNew()) {
            throw new \RuntimeException('Book needs to be saved first');
        }

        $this->connection->execute('DELETE FROM `books_writers` WHERE `book_id` = ?', $this->getId());

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
    public function setName(string $value)
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

    public function jsonSerialize(): mixed
    {
        return array_merge(parent::jsonSerialize(), [
            'name' => $this->getName(),
        ]);
    }

    /**
     * Validate object properties before object is saved.
     */
    public function validate(\ActiveCollab\DatabaseObject\ValidatorInterface &$validator)
    {
        $validator->present('name');

        parent::validate($validator);
    }
}
