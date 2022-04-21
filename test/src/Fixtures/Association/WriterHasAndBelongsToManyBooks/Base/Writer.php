<?php

declare(strict_types=1);

namespace ActiveCollab\DatabaseStructure\Test\Fixtures\Association\WriterHasAndBelongsToManyBooks\Base;

use ActiveCollab\DatabaseStructure\Test\Fixtures\Association\WriterHasAndBelongsToManyBooks\WriterInterface;
use ActiveCollab\DatabaseStructure\Entity\Entity as BaseEntity;

abstract class Writer extends BaseEntity implements WriterInterface
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
                'books' => new \ActiveCollab\DatabaseStructure\Association\AssociatedEntitiesManager\HasAndBelongsToManyAssociatedEntitiesManager(
                    $this->connection,
                    $this->pool,
                    'books_writers',
                    'writer_id',
                    'book_id',
                    '\\ActiveCollab\\DatabaseStructure\\Test\\Fixtures\\Association\\WriterHasAndBelongsToManyBooks\\Book',
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
            ->find('\\ActiveCollab\\DatabaseStructure\\Test\\Fixtures\\Association\\WriterHasAndBelongsToManyBooks\\Book')
            ->joinTable('books_writers')
            ->where('`books_writers`.`writer_id` = ?', $this->getId());
    }

    /**
     * Return writer books.
     *
     * @return iterable|null|\ActiveCollab\DatabaseStructure\Test\Fixtures\Association\WriterHasAndBelongsToManyBooks\Book[]
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
     * Create connection between this writer and one or more $objects_to_add.
     *
     * @param  \ActiveCollab\DatabaseStructure\Test\Fixtures\Association\WriterHasAndBelongsToManyBooks\Book[] $objects_to_add
     * @return $this
     */
    public function &addBooks(\ActiveCollab\DatabaseStructure\Test\Fixtures\Association\WriterHasAndBelongsToManyBooks\Book ...$objects_to_add)
    {
        if ($this->isNew()) {
            throw new \RuntimeException('Writer needs to be saved first');
        }

        $batch = new \ActiveCollab\DatabaseConnection\BatchInsert\BatchInsert(
            $this->connection,
            'books_writers',
            ['writer_id', 'book_id'],
            50,
            \ActiveCollab\DatabaseConnection\ConnectionInterface::REPLACE
        );

        foreach ($objects_to_add as $object_to_add) {
            if ($object_to_add->isNew()) {
                throw new \RuntimeException('All book needs to be saved first');
            }

            $batch->insert($this->getId(), $object_to_add->getId());
        }

        $batch->done();

        return $this;
    }

    /**
     * Drop connection between this writer and $object_to_remove.
     *
     * @param  \ActiveCollab\DatabaseStructure\Test\Fixtures\Association\WriterHasAndBelongsToManyBooks\Book[] $objects_to_remove
     * @return $this
     */
    public function &removeBooks(\ActiveCollab\DatabaseStructure\Test\Fixtures\Association\WriterHasAndBelongsToManyBooks\Book ...$objects_to_remove)
    {
        if ($this->isNew()) {
            throw new \RuntimeException('Writer needs to be saved first');
        }

        $ids_to_remove = [];

        foreach ($objects_to_remove as $object_to_remove) {
            if ($object_to_remove->isNew()) {
                throw new \RuntimeException('All book needs to be saved first');
            }

            $ids_to_remove[] = $object_to_remove->getId();
        }

        if (!empty($ids_to_remove)) {
            $this->connection->execute('DELETE FROM `books_writers` WHERE `writer_id` = ? AND `book_id` IN ?', $this->getId(), $ids_to_remove);
        }

        return $this;
    }

    /**
     * Drop all connections between books and this writer.
     *
     * @return $this
     */
    public function &clearBooks()
    {
        if ($this->isNew()) {
            throw new \RuntimeException('Writer needs to be saved first');
        }

        $this->connection->execute('DELETE FROM `books_writers` WHERE `writer_id` = ?', $this->getId());

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
