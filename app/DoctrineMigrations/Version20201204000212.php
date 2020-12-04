<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

class Version20201204000212 extends AbstractMigration
{
    public function up(Schema $schema) {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('UPDATE project SET project_type = \'SB\' WHERE project_type = \'NS\'');
        $this->addSql('UPDATE project SET project_type = \'LB\' WHERE project_type = \'NB\'');
        $this->addSql('UPDATE project SET project_type = \'SM\' WHERE project_type = \'PS\'');
        $this->addSql('UPDATE project SET project_type = \'LM\' WHERE project_type = \'PB\'');
    }

    public function down(Schema $schema) {
    }
}
