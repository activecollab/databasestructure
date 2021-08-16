<?php

/*
 * This file is part of the Active Collab DatabaseStructure project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\DatabaseStructure\Test\ScalarFields;

use ActiveCollab\DatabaseConnection\Record\ValueCasterInterface;
use ActiveCollab\DatabaseStructure\Field\Scalar\JsonField;
use ActiveCollab\DatabaseStructure\Field\Scalar\JsonField\BoolValueExtractor;
use ActiveCollab\DatabaseStructure\Field\Scalar\JsonField\IntValueExtractor;
use ActiveCollab\DatabaseStructure\Test\TestCase;

/**
 * @package ActiveCollab\DatabaseStructure\Test
 */
class JsonFieldTest extends TestCase
{
    /**
     * Test if native type is mixed.
     */
    public function testMixedNativeType()
    {
        $this->assertEquals('mixed', (new JsonField('test_field'))->getNativeType());
    }

    /**
     * Test if json_encode() is used for data serialization.
     */
    public function testCastingUsesJsonDecode()
    {
        $this->assertStringContainsString('json_encode', (new JsonField('test_field'))->getCastingCode('value'));
    }

    public function testGetValueExtractors()
    {
        $field = (new JsonField('stats'))
            ->extractValue('number_of_users', '$.users')
            ->extractValue('number_of_projects', '$.projects');

        $this->assertCount(2, $field->getValueExtractors());
        $this->assertSame('number_of_users', $field->getValueExtractors()[0]->getFieldName());
        $this->assertSame('number_of_projects', $field->getValueExtractors()[1]->getFieldName());
    }

    public function testGetGeneratedFieldsFromExtractors()
    {
        $field = (new JsonField('stats'))
            ->extractValue('plan_name', '$.plan_name')
            ->extractValue('number_of_projects', '$.projects', null, IntValueExtractor::class)
            ->extractValue('is_used_on_day', '$.is_used', null, BoolValueExtractor::class);

        $generated_fields = $field->getGeneratedFields();

        $this->assertIsArray($generated_fields);
        $this->assertCount(3, $generated_fields);
        $this->assertArrayHasKey('plan_name', $generated_fields);
        $this->assertContains(ValueCasterInterface::CAST_STRING, $generated_fields);

        $this->assertArrayHasKey('number_of_projects', $generated_fields);
        $this->assertContains(ValueCasterInterface::CAST_INT, $generated_fields);

        $this->assertArrayHasKey('is_used_on_day', $generated_fields);
        $this->assertContains(ValueCasterInterface::CAST_BOOL, $generated_fields);
    }
}
