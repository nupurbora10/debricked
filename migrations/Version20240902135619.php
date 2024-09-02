<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240902135619 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE file_upload (id INT AUTO_INCREMENT NOT NULL, file_name VARCHAR(255) NOT NULL, file_path VARCHAR(255) NOT NULL, status VARCHAR(50) NOT NULL, uploaded_at DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE scan_result (id INT AUTO_INCREMENT NOT NULL, file_upload_id INT NOT NULL, vulnerabilities_count INT NOT NULL, status VARCHAR(255) NOT NULL, scanned_at DATETIME NOT NULL, INDEX IDX_CFDBE4ED42C00547 (file_upload_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE scan_result ADD CONSTRAINT FK_CFDBE4ED42C00547 FOREIGN KEY (file_upload_id) REFERENCES file_upload (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE scan_result DROP FOREIGN KEY FK_CFDBE4ED42C00547');
        $this->addSql('DROP TABLE file_upload');
        $this->addSql('DROP TABLE scan_result');
    }
}
