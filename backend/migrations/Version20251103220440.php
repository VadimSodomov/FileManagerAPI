<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20251103220440 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Файлы пользователя';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE IF NOT EXISTS file (id BIGSERIAL NOT NULL, user_id BIGINT NOT NULL, folder_id BIGINT NOT NULL, name TEXT NOT NULL, server_name TEXT NOT NULL, cdate TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, mime_type TEXT NOT NULL, size BIGINT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IF NOT EXISTS IDX_8C9F3610A76ED395 ON file (user_id)');
        $this->addSql('CREATE INDEX IF NOT EXISTS IDX_8C9F3610162CB942 ON file (folder_id)');
        $this->addSql('ALTER TABLE file ADD CONSTRAINT FK_8C9F3610A76ED395 FOREIGN KEY (user_id) REFERENCES "user" (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE file ADD CONSTRAINT FK_8C9F3610162CB942 FOREIGN KEY (folder_id) REFERENCES folder (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE file DROP CONSTRAINT FK_8C9F3610A76ED395');
        $this->addSql('ALTER TABLE file DROP CONSTRAINT FK_8C9F3610162CB942');
        $this->addSql('DROP TABLE file');
    }
}
