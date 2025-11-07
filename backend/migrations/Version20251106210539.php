<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251106210539 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Добавление кода для доступа не автора';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE file ADD code TEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE folder ADD code TEXT DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE folder DROP code');
        $this->addSql('ALTER TABLE file DROP code');
    }
}
