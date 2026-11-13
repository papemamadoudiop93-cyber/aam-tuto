<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20261113004154 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Schéma initial : agents, formations, inscriptions (SQLite)';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE agent (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, email VARCHAR(180) NOT NULL, nom_complet VARCHAR(120) NOT NULL, roles CLOB NOT NULL, password VARCHAR(255) NOT NULL)');
        $this->addSql('CREATE UNIQUE INDEX uniq_agent_email ON agent (email)');
        $this->addSql('CREATE TABLE formation (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, titre VARCHAR(160) NOT NULL, description CLOB NOT NULL, lieu VARCHAR(80) NOT NULL, date_debut DATETIME NOT NULL, places INTEGER NOT NULL)');
        $this->addSql('CREATE TABLE inscription (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, motivation CLOB NOT NULL, statut VARCHAR(255) NOT NULL, cree_le DATETIME NOT NULL, formation_id INTEGER NOT NULL, agent_id INTEGER NOT NULL, CONSTRAINT FK_5E90F6D65200282E FOREIGN KEY (formation_id) REFERENCES formation (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_5E90F6D63414710B FOREIGN KEY (agent_id) REFERENCES agent (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE UNIQUE INDEX uniq_inscription_agent_formation ON inscription (agent_id, formation_id)');
        $this->addSql('CREATE INDEX IDX_5E90F6D65200282E ON inscription (formation_id)');
        $this->addSql('CREATE INDEX IDX_5E90F6D63414710B ON inscription (agent_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE agent');
        $this->addSql('DROP TABLE formation');
        $this->addSql('DROP TABLE inscription');
    }
}
