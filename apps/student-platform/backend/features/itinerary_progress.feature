Feature: Itinerary progress
  As a student
  I want to progress through an itinerary
  So that I can learn at my own pace

  Background:
    Given the database is clean
    And the fixtures are loaded

  Scenario: Student gets first activity when starting itinerary for the first time
    Given I am authenticated as "alice@innovamat.com" with password "password123"
    When I request the next activity for itinerary "additions" and student 1
    Then the response status code should be 200
    And the response should contain activity with identifier "A1"

  Scenario: Student with existing progress gets their current activity
    Given I am authenticated as "bob@innovamat.com" with password "password123"
    When I request the next activity for itinerary "additions" and student 2
    Then the response status code should be 200
    And the response should contain activity with identifier "A2"

  Scenario: Student advances when score is at or above 75%
    Given I am authenticated as "alice@innovamat.com" with password "password123"
    And student 1 is on activity "A1" in itinerary "additions"
    When I complete activity "A1" for student 1 with answers "1_0_2" in 2 minutes
    Then the response status code should be 200
    And the field "passed" should be true
    And the field "itinerary_completed" should be false

  Scenario: Student repeats activity when score is below 75%
    Given I am authenticated as "alice@innovamat.com" with password "password123"
    And student 1 is on activity "A1" in itinerary "additions"
    When I complete activity "A1" for student 1 with answers "9_9_2" in 3 minutes
    Then the response status code should be 200
    And the field "passed" should be false
    When I request the next activity for itinerary "additions" and student 1
    Then the response should contain activity with identifier "A1"

  Scenario: Itinerary is marked completed when last activity is passed
    Given I am authenticated as "alice@innovamat.com" with password "password123"
    And student 1 is on activity "A15" in itinerary "additions"
    When I complete activity "A15" for student 1 with answers "1_0_2" in 2 minutes
    Then the response status code should be 200
    And the field "itinerary_completed" should be true
