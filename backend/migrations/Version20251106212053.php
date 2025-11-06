<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20251106212053 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Дата кода';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE file ADD code_cdate TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL');
        $this->addSql('ALTER TABLE folder ADD code_cdate TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE file DROP code_cdate');
        $this->addSql('ALTER TABLE folder DROP code_cdate');
    }
}
