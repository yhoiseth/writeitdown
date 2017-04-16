@watch
Feature: List own posts
  In order to know which posts I have and access them
  As a logged-in web user
  I need to be able to list my own posts

  Background:
    Given a user "listlover"
    And that "listlover" has a post with title "First"
    And that "listlover" has a post with title "Second"
    And that "listlover" has a post with title "Third"
    And that "listlover" has a post with title "Fourth"

  Scenario: Own posts
    Given I am logged in as "listlover"
    And I am on "/listlover"
    Then I should see "First"
    Then I should see "Second"
    Then I should see "Third"
    Then I should see "Fourth"
