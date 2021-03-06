<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210305185312 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE comentario ADD videojuego_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE comentario ADD CONSTRAINT FK_4B91E70282925A85 FOREIGN KEY (videojuego_id) REFERENCES videojuego (id)');
        $this->addSql('CREATE INDEX IDX_4B91E70282925A85 ON comentario (videojuego_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE comentario DROP FOREIGN KEY FK_4B91E70282925A85');
        $this->addSql('DROP INDEX IDX_4B91E70282925A85 ON comentario');
        $this->addSql('ALTER TABLE comentario DROP videojuego_id');
    }
}
