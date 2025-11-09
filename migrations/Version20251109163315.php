<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251109163315 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TEMPORARY TABLE __temp__lessons AS SELECT id, course_id, title, description, difficulty, type, resource_url, created_at, updated_at FROM lessons');
        $this->addSql('DROP TABLE lessons');
        $this->addSql('CREATE TABLE lessons (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, course_id INTEGER NOT NULL, title VARCHAR(255) NOT NULL, description CLOB DEFAULT NULL, difficulty INTEGER NOT NULL, type VARCHAR(50) NOT NULL, resource_url VARCHAR(255) DEFAULT NULL, created_at DATETIME DEFAULT NULL --(DC2Type:datetime_immutable)
        , updated_at DATETIME DEFAULT NULL --(DC2Type:datetime_immutable)
        , CONSTRAINT FK_3F4218D9591CC992 FOREIGN KEY (course_id) REFERENCES courses (id) ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO lessons (id, course_id, title, description, difficulty, type, resource_url, created_at, updated_at) SELECT id, course_id, title, description, difficulty, type, resource_url, created_at, updated_at FROM __temp__lessons');
        $this->addSql('DROP TABLE __temp__lessons');
        $this->addSql('CREATE INDEX IDX_3F4218D9591CC992 ON lessons (course_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TEMPORARY TABLE __temp__lessons AS SELECT id, course_id, title, description, difficulty, type, resource_url, created_at, updated_at FROM lessons');
        $this->addSql('DROP TABLE lessons');
        $this->addSql('CREATE TABLE lessons (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, course_id INTEGER NOT NULL, title VARCHAR(255) NOT NULL, description CLOB DEFAULT NULL, difficulty INTEGER NOT NULL, type VARCHAR(50) NOT NULL, resource_url VARCHAR(255) DEFAULT NULL, created_at DATETIME NOT NULL --(DC2Type:datetime_immutable)
        , updated_at DATETIME DEFAULT NULL --(DC2Type:datetime_immutable)
        , CONSTRAINT FK_3F4218D9591CC992 FOREIGN KEY (course_id) REFERENCES courses (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO lessons (id, course_id, title, description, difficulty, type, resource_url, created_at, updated_at) SELECT id, course_id, title, description, difficulty, type, resource_url, created_at, updated_at FROM __temp__lessons');
        $this->addSql('DROP TABLE __temp__lessons');
        $this->addSql('CREATE INDEX IDX_3F4218D9591CC992 ON lessons (course_id)');
    }
}
