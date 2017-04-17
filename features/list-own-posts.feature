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
    And I should see "Second"
    And I should see "Third"
    And I should see "Fourth"
    And I should see that I am viewing my own posts

    When I click the "Fourth" link
    Then I should be redirected to "/listlover/fourth"

  Scenario: Other user's posts
    Given a user "other"
    And I am logged in as "other"
    And I am on "/listlover"
    Then the response status code should be 403
