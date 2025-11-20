<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251118003505 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE covoiturage CHANGE statut statut VARCHAR(50) NOT NULL, CHANGE accepte_fumeur accepte_fumeur TINYINT(1) DEFAULT NULL, CHANGE accepte_animaux accepte_animaux TINYINT(1) DEFAULT NULL');
        $this->addSql('ALTER TABLE user CHANGE is_verified is_verified TINYINT(1) NOT NULL, CHANGE telephone telephone VARCHAR(255) NOT NULL, CHANGE adresse adresse VARCHAR(255) NOT NULL, CHANGE marque_creee marque_creee TINYINT(1) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user CHANGE is_verified is_verified TINYINT(1) DEFAULT NULL, CHANGE telephone telephone VARCHAR(255) DEFAULT NULL, CHANGE adresse adresse VARCHAR(255) DEFAULT NULL, CHANGE marque_creee marque_creee TINYINT(1) DEFAULT NULL');
        $this->addSql('ALTER TABLE covoiturage CHANGE statut statut VARCHAR(50) DEFAULT NULL, CHANGE accepte_fumeur accepte_fumeur INT NOT NULL, CHANGE accepte_animaux accepte_animaux INT DEFAULT NULL');
    }
}
