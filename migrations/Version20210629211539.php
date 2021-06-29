<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20210629211539 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add creation date to comments';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('
                ALTER TABLE comments ADD created_at
                    TIMESTAMP(0) WITHOUT TIME ZONE
                    DEFAULT CURRENT_TIMESTAMP
                    NOT NULL
        ');
        $this->addSql('COMMENT ON COLUMN comments.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE INDEX created_at_idx ON comments (created_at)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP INDEX created_at_idx');
        $this->addSql('ALTER TABLE comments DROP created_at');
    }
}
