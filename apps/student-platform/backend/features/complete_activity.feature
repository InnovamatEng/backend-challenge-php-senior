Feature: Complete activity
  As a student
  I want to submit my answers for an activity
  So that my progress is recorded

  Background:
    Given the database is clean
    And the fixtures are loaded

  Scenario: Activity is completed with correct answers
    Given I am authenticated as "alice@innovamat.com" with password "password123"
    And student 1 is on activity "A1" in itinerary "additions"
    When I complete activity "A1" for student 1 with answers "1_0_2" in 2 minutes
    Then the response status code should be 200
    And the response should contain "score"
    And the field "score" should equal 1.0
    And an attempt should be recorded for student 1 on activity "A1"

  Scenario: Activity is completed with partial correct answers
    Given I am authenticated as "alice@innovamat.com" with password "password123"
    And student 1 is on activity "A1" in itinerary "additions"
    When I complete activity "A1" for student 1 with answers "1_1_2" in 2 minutes
    Then the response status code should be 200
    And the field "passed" should be false

  Scenario: Score is calculated correctly for partial answers
    Given activity "A1" has solution "1_0_2"
    When I calculate the score for answers "1_1_2" against solution "1_0_2"
    Then the calculated score should be 0.6666666666666666

  Scenario: Perfect answers yield score of 1.0
    Given activity "A1" has solution "1_0_2"
    When I calculate the score for answers "1_0_2" against solution "1_0_2"
    Then the calculated score should be 1.0

