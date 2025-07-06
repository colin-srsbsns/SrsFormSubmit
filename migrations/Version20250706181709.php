<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250706181709 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE client (id VARCHAR(26) NOT NULL, name VARCHAR(100) NOT NULL, jwt_secret VARCHAR(64) NOT NULL, recipient VARCHAR(255) DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE form_submission (id INT AUTO_INCREMENT NOT NULL, client_id VARCHAR(26) NOT NULL, raw JSON NOT NULL, processed TINYINT(1) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_D2C2166719EB6921 (client_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE form_submission_field (id INT AUTO_INCREMENT NOT NULL, form_submission_id INT NOT NULL, field_name VARCHAR(100) NOT NULL, field_value LONGTEXT DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_79D533D7422B0E0C (form_submission_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE form_submission ADD CONSTRAINT FK_D2C2166719EB6921 FOREIGN KEY (client_id) REFERENCES client (id)');
        $this->addSql('ALTER TABLE form_submission_field ADD CONSTRAINT FK_79D533D7422B0E0C FOREIGN KEY (form_submission_id) REFERENCES form_submission (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE form_submission DROP FOREIGN KEY FK_D2C2166719EB6921');
        $this->addSql('ALTER TABLE form_submission_field DROP FOREIGN KEY FK_79D533D7422B0E0C');
        $this->addSql('DROP TABLE client');
        $this->addSql('DROP TABLE form_submission');
        $this->addSql('DROP TABLE form_submission_field');
    }
}
