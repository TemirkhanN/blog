<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20210627194908 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create comments table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE comments (
                            guid VARCHAR(36) NOT NULL,
                            post_slug VARCHAR(255) DEFAULT NULL,
                            comment TEXT NOT NULL,
                            replied_to_comment_guid VARCHAR(36) DEFAULT NULL,
                            PRIMARY KEY(guid)
                      )
        ');
        $this->addSql('CREATE INDEX IDX_5F9E962A51C8FC69 ON comments (post_slug)');
        $this->addSql('ALTER TABLE comments
                                ADD CONSTRAINT FK_5F9E962A51C8FC69
                                    FOREIGN KEY (post_slug) REFERENCES posts (slug)
                                        ON DELETE CASCADE
                                        NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE comments');
    }
}
