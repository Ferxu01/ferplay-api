<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210303193722 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE usuario DROP FOREIGN KEY FK_2265B05D4E7121AF');
        $this->addSql('ALTER TABLE usuario CHANGE username nickname VARCHAR(255) NOT NULL');
        $this->addSql('DROP INDEX fk_2265b05d4e7121af ON usuario');
        $this->addSql('CREATE INDEX IDX_2265B05D4E7121AF ON usuario (provincia_id)');
        $this->addSql('ALTER TABLE usuario ADD CONSTRAINT FK_2265B05D4E7121AF FOREIGN KEY (provincia_id) REFERENCES provincia (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE usuario DROP FOREIGN KEY FK_2265B05D4E7121AF');
        $this->addSql('ALTER TABLE usuario CHANGE nickname username VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('DROP INDEX idx_2265b05d4e7121af ON usuario');
        $this->addSql('CREATE INDEX FK_2265B05D4E7121AF ON usuario (provincia_id)');
        $this->addSql('ALTER TABLE usuario ADD CONSTRAINT FK_2265B05D4E7121AF FOREIGN KEY (provincia_id) REFERENCES provincia (id)');
    }
}
