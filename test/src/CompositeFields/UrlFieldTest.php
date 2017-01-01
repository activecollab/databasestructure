<?php

/*
 * This file is part of the Active Collab DatabaseStructure project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\DatabaseStructure\Test\CompositeFields;

use ActiveCollab\DatabaseStructure\Field\Composite\UrlField;
use ActiveCollab\DatabaseStructure\Field\Scalar\StringField;
use ActiveCollab\DatabaseStructure\IndexInterface;
use ActiveCollab\DatabaseStructure\Test\TestCase;
use ActiveCollab\DatabaseStructure\Type;

/**
 * @package ActiveCollab\DatabaseStructure\Test
 */
class UrlFieldTest extends TestCase
{
    /**
     * Test if name can be set.
     */
    public function testName()
    {
        $this->assertEquals('homepage_url', (new UrlField('homepage_url'))->getName());
    }

    /**
     * Test if NULL is the default value.
     */
    public function testNullIsDefaultValue()
    {
        $this->assertNull((new UrlField('homepage_url'))->getDefaultValue());
    }

    /**
     * Test no modifier when default URL is NULL.
     */
    public function testNoModifierWhenNullIsDefaultValue()
    {
        $this->assertNull((new UrlField('homepage_url'))->getModifier());
    }

    /**
     * Test if URL is trimmed when not null by default.
     */
    public function testValueIsTrimmedByDefault()
    {
        $this->assertEquals('trim', (new UrlField('homepage_url', 'not null'))->getModifier());
    }

    /**
     * Test if URL can be added to a type.
     */
    public function testUrlCanBeAddedToType()
    {
        $type = (new Type('writers'))->addField(new UrlField('homepage_url'));

        $this->assertArrayHasKey('homepage_url', $type->getFields());
        $this->assertInstanceOf(UrlField::class, $type->getFields()['homepage_url']);
    }

    /**
     * Test if URL field does not add an index by default.
     */
    public function testUrlFieldDoesNotAddIndexByDefault()
    {
        $type = (new Type('writers'))->addField(new UrlField('homepage_url'));

        $this->assertCount(0, $type->getIndexes());
    }

    /**
     * Test if URL index is added to the type when requested.
     */
    public function testUrlFieldAddsIndexWhenRequested()
    {
        $type = (new Type('writers'))->addField(new UrlField('homepage_url', null, true));

        $this->assertArrayHasKey('homepage_url', $type->getIndexes());

        $url_index = $type->getIndexes()['homepage_url'];

        $this->assertInstanceOf(IndexInterface::class, $url_index);
        $this->assertEquals(IndexInterface::INDEX, $url_index->getIndexType());
    }

    /**
     * Test if unique URL index is added to the type when requested.
     */
    public function testUrlFieldAddsUniqueIndexWhenRequested()
    {
        $type = (new Type('writers'))->addField((new UrlField('homepage_url', null, true))->unique());

        $this->assertArrayHasKey('homepage_url', $type->getIndexes());

        $url_index = $type->getIndexes()['homepage_url'];

        $this->assertInstanceOf(IndexInterface::class, $url_index);
        $this->assertEquals(IndexInterface::UNIQUE, $url_index->getIndexType());
    }

    /**
     * Test if unique index properly alters index added by the URL field.
     */
    public function testUniqueContextAddsFieldsToTheIndex()
    {
        $type = (new Type('applications'))->addField((new UrlField('homepage_url', null, true))->unique('application_id', 'shard_id'));

        $this->assertArrayHasKey('homepage_url', $type->getIndexes());

        $name_index = $type->getIndexes()['homepage_url'];

        $this->assertInstanceOf(IndexInterface::class, $name_index);

        $this->assertEquals('homepage_url', $name_index->getName());
        $this->assertEquals(['homepage_url', 'application_id', 'shard_id'], $name_index->getFields());
    }

    /**
     * Make sure that resulting string field is required and / or unique when name is required and / or unique.
     */
    public function testUrlProducesRequiredAndUniqueStringWhenRequiredAndUnique()
    {
        $url_field = (new UrlField('homepage_url', ''))
            ->required()
            ->unique('context_filed_1');

        /** @var StringField $string_field */
        $string_field = $url_field->getFields()[0];

        $this->assertInstanceOf(StringField::class, $string_field);

        $this->assertTrue($string_field->isRequired());
        $this->assertTrue($string_field->isUnique());
        $this->assertEquals(['context_filed_1'], $string_field->getUniquenessContext());
    }
}
