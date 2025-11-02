<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20251101210803 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Создание таблицы папок пользователей';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE folder (id SERIAL NOT NULL, user_id BIGINT NOT NULL, parent_id BIGINT DEFAULT NULL, name TEXT NOT NULL, is_root BOOLEAN NOT NULL DEFAULT false, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_ECA209CDA76ED395 ON folder (user_id)');
        $this->addSql('CREATE INDEX IDX_ECA209CD727ACA70 ON folder (parent_id)');
        $this->addSql('ALTER TABLE folder ADD CONSTRAINT FK_ECA209CDA76ED395 FOREIGN KEY (user_id) REFERENCES "user" (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE folder ADD CONSTRAINT FK_ECA209CD727ACA70 FOREIGN KEY (parent_id) REFERENCES folder (id) ON DELETE CASCADE');
        $this->addSql('CREATE UNIQUE INDEX IF NOT EXISTS idx_unique_folder_user_parent_name ON folder (user_id, parent_id, name)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE folder DROP CONSTRAINT FK_ECA209CDA76ED395');
        $this->addSql('ALTER TABLE folder DROP CONSTRAINT FK_ECA209CD727ACA70');
        $this->addSql('DROP TABLE folder');
    }
}
