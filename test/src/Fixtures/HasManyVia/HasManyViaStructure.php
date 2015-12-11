<?php

namespace ActiveCollab\DatabaseStructure\Test\Fixtures\HasManyVia;

use ActiveCollab\DatabaseStructure\Association\HasManyViaAssociation;
use ActiveCollab\DatabaseStructure\Field\Composite\PositionField;
use ActiveCollab\DatabaseStructure\Structure;

/**
 * @package ActiveCollab\DatabaseStructure\Test\Fixtures\HasManyVia
 */
class HasManyViaStructure extends Structure
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->addType('users')->addAssociations([
            (new HasManyViaAssociation('accounts', 'user_accounts'))->orderBy('user_accounts.position'),
        ]);

        $this->addType('accounts')->addAssociations([
            new HasManyViaAssociation('users', 'user_accounts'),
        ]);

        $this->addType('user_accounts')->addFields([
            new PositionField('position'),
        ]);
    }
}
