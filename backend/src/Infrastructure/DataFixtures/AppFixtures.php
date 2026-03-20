<?php

namespace App\Infrastructure\DataFixtures;

use App\Domain\Model\Activity;
use App\Domain\Model\Itinerary;
use App\Domain\Model\Student;
use App\Domain\Model\StudentProgress;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    public function __construct(
        private readonly UserPasswordHasherInterface $passwordHasher,
    ) {
    }

    public function load(ObjectManager $manager): void
    {
        // Create itinerary
        $itinerary = new Itinerary();
        $itinerary->setName('Additions');
        $itinerary->setSlug('additions');
        $manager->persist($itinerary);

        // Create 15 activities matching the README example exactly
        $activitiesData = [
            ['A1',  'Activity 1',  1,  1, 120, '1_0_2'],
            ['A2',  'Activity 2',  2,  1,  60, '-2_40_56'],
            ['A3',  'Activity 3',  3,  1, 120, '1_0'],
            ['A4',  'Activity 4',  4,  1, 180, '1*0_2*-5_9'],
            ['A5',  'Activity 5',  5,  2, 120, '1_0_2'],
            ['A6',  'Activity 6',  6,  2, 120, '1_0_2'],
            ['A7',  'Activity 7',  7,  3, 120, "1*-1*'Yes'_34_-6"],
            ['A8',  'Activity 8',  8,  3, 120, '1_2'],
            ['A9',  'Activity 9',  9,  4, 120, '1_0_2'],
            ['A10', 'Activity 10', 10, 5, 120, '1_0_2'],
            ['A11', 'Activity 11', 11, 6, 120, '1_0_2'],
            ['A12', 'Activity 12', 12, 7, 120, '1_0_2'],
            ['A13', 'Activity 13', 13, 8, 120, '1_0_2'],
            ['A14', 'Activity 14', 14, 9, 120, '1_0_2'],
            ['A15', 'Activity 15', 15, 10, 120, '1_0_2'],
        ];

        $activities = [];
        foreach ($activitiesData as [$identifier, $name, $position, $difficulty, $estimatedTime, $solution]) {
            $activity = new Activity();
            $activity->setIdentifier($identifier);
            $activity->setName($name);
            $activity->setPosition($position);
            $activity->setDifficulty($difficulty);
            $activity->setEstimatedTime($estimatedTime);
            $activity->setSolution($solution);
            $activity->setItinerary($itinerary);
            $manager->persist($activity);
            $activities[$identifier] = $activity;
        }

        // Student 1: Alice - has not started the itinerary yet
        $alice = new Student();
        $alice->setName('Alice Smith');
        $alice->setEmail('alice@innovamat.com');
        $alice->setPassword($this->passwordHasher->hashPassword($alice, 'password123'));
        $manager->persist($alice);

        // Student 2: Bob - has already started the itinerary (completed A1, currently on A2)
        $bob = new Student();
        $bob->setName('Bob Jones');
        $bob->setEmail('bob@innovamat.com');
        $bob->setPassword($this->passwordHasher->hashPassword($bob, 'password123'));
        $manager->persist($bob);

        $manager->flush();

        // Create progress for Bob (has completed A1, now on A2)
        $bobProgress = new StudentProgress();
        $bobProgress->setStudent($bob);
        $bobProgress->setItinerary($itinerary);
        $bobProgress->setCurrentActivity($activities['A2']);
        $bobProgress->setLastScore(1.0);
        $manager->persist($bobProgress);

        $manager->flush();
    }
}
