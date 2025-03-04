<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250302102651 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE IF EXISTS task');
        $this->addSql('DROP TABLE IF EXISTS project');
        $this->addSql('DROP TABLE IF EXISTS project_group');
        $this->addSql('CREATE TABLE project_group (id UUID NOT NULL, name VARCHAR(255) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL DEFAULT NOW(), updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN project_group.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN project_group.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE project (id UUID NOT NULL, project_group_id UUID NOT NULL, name VARCHAR(255) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL DEFAULT NOW(), updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN project.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN project.project_group_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN project.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE INDEX IDX_2FB3D0EE1C5A2B2 ON project (project_group_id)');
        $this->addSql('ALTER TABLE project ADD CONSTRAINT FK_2FB3D0EE1C5A2B2 FOREIGN KEY (project_group_id) REFERENCES project_group (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE TABLE task (id UUID NOT NULL, project_id UUID NOT NULL, name VARCHAR(255) NOT NULL, description VARCHAR(2048) DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL DEFAULT NOW(), updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN task.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN task.project_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN task.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE INDEX IDX_527EDB25166D1F9C ON task (project_id)');
        $this->addSql('ALTER TABLE task ADD CONSTRAINT FK_527EDB25166D1F9C FOREIGN KEY (project_id) REFERENCES project (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE task');
        $this->addSql('DROP TABLE project');
        $this->addSql('DROP TABLE project_group');
    }
}
