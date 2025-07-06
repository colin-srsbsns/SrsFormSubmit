<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250706223603 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE form_submission_file (id INT AUTO_INCREMENT NOT NULL, form_submission_id INT NOT NULL, original_name VARCHAR(255) DEFAULT NULL, storage_path VARCHAR(1024) NOT NULL, mime_type VARCHAR(255) NOT NULL, size INT DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_AEFF00A6422B0E0C (form_submission_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE form_submission_file ADD CONSTRAINT FK_AEFF00A6422B0E0C FOREIGN KEY (form_submission_id) REFERENCES form_submission (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE form_submission_file DROP FOREIGN KEY FK_AEFF00A6422B0E0C');
        $this->addSql('DROP TABLE form_submission_file');
    }
}
