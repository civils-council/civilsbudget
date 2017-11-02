<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

class Version20171102230037 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->addSql('UPDATE `location` l, `user` u SET l.`user_id` = u.`id` WHERE u.`location_id` = l.`id`;');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
    }
}
