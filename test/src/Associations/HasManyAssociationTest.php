<?php

/*
 * This file is part of the Active Collab DatabaseStructure project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\DatabaseStructure\Test\Associations;

use ActiveCollab\DatabaseStructure\Association\HasManyAssociation;
use ActiveCollab\DatabaseStructure\Test\TestCase;

class HasManyAssociationTest extends TestCase
{
    public function testAssociationNameIsTargetTypeNameByDefault()
    {
        $this->assertEquals('books', (new HasManyAssociation('books'))->getTargetTypeName());
    }

    public function testTargetTypeNameCanBeSpecified()
    {
        $this->assertEquals(
            'books',
            (new HasManyAssociation('awesome_books', 'books'))
                ->getTargetTypeName()
        );
    }
}
