<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

class Version20171029202421 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('UPDATE user u SET u.sex = \'M\' WHERE u.sex = \'Чоловік\'');
        $this->addSql('UPDATE user u SET u.sex = \'F\' WHERE u.sex = \'Жінка\'');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
    }
}
