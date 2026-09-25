<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20240101000000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Initial schema: students, itineraries, activities, student_progress';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE students (
            id INT AUTO_INCREMENT NOT NULL,
            email VARCHAR(180) NOT NULL,
            name VARCHAR(255) NOT NULL,
            roles JSON NOT NULL,
            password VARCHAR(255) NOT NULL,
            UNIQUE INDEX UNIQ_A4698DB2E7927C74 (email),
            PRIMARY KEY(id)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');

        $this->addSql('CREATE TABLE itineraries (
            id INT AUTO_INCREMENT NOT NULL,
            name VARCHAR(100) NOT NULL,
            slug VARCHAR(50) NOT NULL,
            UNIQUE INDEX UNIQ_C6C0BBA65E237E06 (name),
            UNIQUE INDEX UNIQ_C6C0BBA6989D9B62 (slug),
            PRIMARY KEY(id)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');

        $this->addSql('CREATE TABLE activities (
            id INT AUTO_INCREMENT NOT NULL,
            itinerary_id INT NOT NULL,
            identifier VARCHAR(50) NOT NULL,
            name VARCHAR(255) NOT NULL,
            difficulty INT NOT NULL,
            position INT NOT NULL,
            estimated_time INT NOT NULL,
            solution VARCHAR(500) NOT NULL,
            UNIQUE INDEX UNIQ_B5F1AFE772E13D52 (identifier),
            INDEX IDX_B5F1AFE7849B2238 (itinerary_id),
            PRIMARY KEY(id)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');

        $this->addSql('CREATE TABLE student_progress (
            id INT AUTO_INCREMENT NOT NULL,
            student_id INT NOT NULL,
            itinerary_id INT NOT NULL,
            current_activity_id INT DEFAULT NULL,
            completed TINYINT(1) NOT NULL,
            last_score DOUBLE PRECISION DEFAULT NULL,
            started_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\',
            completed_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\',
            INDEX IDX_9E9DC2ACBA03A1B7 (student_id),
            INDEX IDX_9E9DC2AC849B2238 (itinerary_id),
            INDEX IDX_9E9DC2AC28A1A5BF (current_activity_id),
            PRIMARY KEY(id)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');

        $this->addSql('ALTER TABLE activities ADD CONSTRAINT FK_B5F1AFE7849B2238 FOREIGN KEY (itinerary_id) REFERENCES itineraries (id)');
        $this->addSql('ALTER TABLE student_progress ADD CONSTRAINT FK_9E9DC2ACBA03A1B7 FOREIGN KEY (student_id) REFERENCES students (id)');
        $this->addSql('ALTER TABLE student_progress ADD CONSTRAINT FK_9E9DC2AC849B2238 FOREIGN KEY (itinerary_id) REFERENCES itineraries (id)');
        $this->addSql('ALTER TABLE student_progress ADD CONSTRAINT FK_9E9DC2AC28A1A5BF FOREIGN KEY (current_activity_id) REFERENCES activities (id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE activities DROP FOREIGN KEY FK_B5F1AFE7849B2238');
        $this->addSql('ALTER TABLE student_progress DROP FOREIGN KEY FK_9E9DC2ACBA03A1B7');
        $this->addSql('ALTER TABLE student_progress DROP FOREIGN KEY FK_9E9DC2AC849B2238');
        $this->addSql('ALTER TABLE student_progress DROP FOREIGN KEY FK_9E9DC2AC28A1A5BF');
        $this->addSql('DROP TABLE student_progress');
        $this->addSql('DROP TABLE activities');
        $this->addSql('DROP TABLE itineraries');
        $this->addSql('DROP TABLE students');
    }
}
