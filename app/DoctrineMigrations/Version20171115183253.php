<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

class Version20171115183253 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('
            CREATE TABLE gallery (
                id INT AUTO_INCREMENT NOT NULL, 
                project_id INT NOT NULL, 
                path VARCHAR(255) NOT NULL, 
                INDEX IDX_472B783A166D1F9C (project_id), 
                PRIMARY KEY(id)
            ) 
            DEFAULT CHARACTER SET utf8 
            COLLATE utf8_unicode_ci 
            ENGINE = InnoDB
        ');
        $this->addSql('
            ALTER TABLE gallery 
            ADD CONSTRAINT FK_472B783A166D1F9C 
            FOREIGN KEY (project_id) 
            REFERENCES project (id)
        ');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE gallery');
    }
}
