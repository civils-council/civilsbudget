<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160927103931 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE user_project (user_id INT NOT NULL, project_id INT NOT NULL, INDEX IDX_77BECEE4A76ED395 (user_id), INDEX IDX_77BECEE4166D1F9C (project_id), PRIMARY KEY(user_id, project_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE user_project ADD CONSTRAINT FK_77BECEE4A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_project ADD CONSTRAINT FK_77BECEE4166D1F9C FOREIGN KEY (project_id) REFERENCES project (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE project ADD approved TINYINT(1) DEFAULT \'1\' NOT NULL, ADD last_date_of_votes DATETIME DEFAULT NULL, CHANGE confirm city VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D649AA6084EE');
        $this->addSql('DROP INDEX IDX_8D93D649AA6084EE ON user');
        $this->addSql('ALTER TABLE user ADD count_votes INT DEFAULT 0, ADD is_subscribe TINYINT(1) DEFAULT \'1\', DROP liked_projects_id');
        $this->addSql('ALTER TABLE admin ADD avatar VARCHAR(255) DEFAULT NULL');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE user_project');
        $this->addSql('ALTER TABLE admin DROP avatar');
        $this->addSql('ALTER TABLE project DROP approved, DROP last_date_of_votes, CHANGE city confirm VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci');
        $this->addSql('ALTER TABLE user ADD liked_projects_id INT DEFAULT NULL, DROP count_votes, DROP is_subscribe');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D649AA6084EE FOREIGN KEY (liked_projects_id) REFERENCES project (id)');
        $this->addSql('CREATE INDEX IDX_8D93D649AA6084EE ON user (liked_projects_id)');
    }
}
