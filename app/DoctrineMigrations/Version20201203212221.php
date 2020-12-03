<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

class Version20201203212221 extends AbstractMigration
{
    public function up(Schema $schema) {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE project CHANGE type project_type VARCHAR(10) COMMENT "php_enum_project_type" DEFAULT NULL COMMENT \'(DC2Type:php_enum_project_type)\'');
    }

    public function down(Schema $schema) {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE project CHANGE project_type type VARCHAR(10) COMMENT "php_enum_project_type" DEFAULT NULL COLLATE utf8_unicode_ci COMMENT \'(DC2Type:php_enum_project_type)\'');
    }
}
