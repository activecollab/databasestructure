<?php

/*
 * This file is part of the Active Collab DatabaseStructure project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\DatabaseStructure\Test\Fixtures\Records;

use ActiveCollab\DatabaseStructure\Field\Composite\NameField;
use ActiveCollab\DatabaseStructure\Field\Scalar\BooleanField;
use ActiveCollab\DatabaseStructure\Field\Scalar\DateField;
use ActiveCollab\DatabaseStructure\Structure;
use ActiveCollab\DateValue\DateValue;

/**
 * @package ActiveCollab\DatabaseStructure\Test\Fixtures\PositionTail
 */
class RecordsStructure extends Structure
{
    /**
     * Configure the structure.
     */
    public function configure()
    {
        $this->addType('records')->addFields([
            new NameField('name'),
            new DateField('birthday'),
            new BooleanField('was_awesome'),
        ]);

        $this->addRecord('records', [
            'name' => 'Leo Tolstoy',
            'birthday' => new DateValue('1828-09-09'),
            'was_awesome' => true,
        ]);

        $this->addRecords('records', ['name', 'birthday', 'was_awesome'], [
            ['Alexander Pushkin', new DateValue('1799-06-06'), true],
            ['Fyodor Dostoyevsky', new DateValue('1821-11-11'), true],
        ]);
    }
}
