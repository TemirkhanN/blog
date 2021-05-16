<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210516095759 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE posts(
                                slug VARCHAR(255) NOT NULL,
                                title VARCHAR(200) NOT NULL,
                                content TEXT NOT NULL,
                                published_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
                           PRIMARY KEY(slug))
        ');
        $this->addSql('COMMENT ON COLUMN posts.published_at IS \'(DC2Type:datetime_immutable)\'');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE posts');
    }
}
