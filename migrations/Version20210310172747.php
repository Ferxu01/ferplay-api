<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210310172747 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE videojuego ADD liked TINYINT(1) NOT NULL');
        $this->addSql('ALTER TABLE videojuego ADD CONSTRAINT FK_AA5E6DFADB38439E FOREIGN KEY (usuario_id) REFERENCES usuario (id)');
        $this->addSql('CREATE INDEX IDX_AA5E6DFADB38439E ON videojuego (usuario_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE videojuego DROP FOREIGN KEY FK_AA5E6DFADB38439E');
        $this->addSql('DROP INDEX IDX_AA5E6DFADB38439E ON videojuego');
        $this->addSql('ALTER TABLE videojuego DROP liked');
    }
}
