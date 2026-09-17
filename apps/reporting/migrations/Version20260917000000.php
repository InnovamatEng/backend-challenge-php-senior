<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260917000000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Activity stats read model';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE activity_stats (
            activity_id VARCHAR(50) NOT NULL,
            itinerary VARCHAR(50) NOT NULL,
            attempts_count INT NOT NULL,
            passed_count INT NOT NULL,
            score_sum DOUBLE PRECISION NOT NULL,
            average_score DOUBLE PRECISION NOT NULL,
            time_spent_sum INT NOT NULL,
            last_attempt_at DATETIME NOT NULL,
            PRIMARY KEY(activity_id)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE activity_stats');
    }
}
