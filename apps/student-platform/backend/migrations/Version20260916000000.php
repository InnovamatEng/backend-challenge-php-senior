<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260916000000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add activity_attempts log';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE activity_attempts (
            id INT AUTO_INCREMENT NOT NULL,
            student_id INT NOT NULL,
            activity_identifier VARCHAR(50) NOT NULL,
            itinerary_slug VARCHAR(50) NOT NULL,
            score DOUBLE PRECISION NOT NULL,
            time_spent INT NOT NULL,
            answers VARCHAR(500) NOT NULL,
            completed_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\',
            PRIMARY KEY(id)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE activity_attempts');
    }
}
