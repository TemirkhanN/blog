<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20210627224644 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Fixed schema';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE comments ALTER post_slug SET NOT NULL');
        $this->addSql('ALTER TABLE posts ALTER preview DROP DEFAULT');
    }

    public function down(Schema $schema): void
    {
    }
}
