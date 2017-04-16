Feature: List posts
  In order to find specific posts
  As a logged-in web user
  I need to see a list of my posts


  Background:
    Given a user "user"
    And that "user" has a post with title "User's first post"
    And that "user" has a post with title "User's second post"

  Scenario: Not logged in
    Given I am on the homepage
    Then I should not see "User's first post"
    And I should not see "User's second post"

  Scenario: Logged in
    Given I am on the homepage
    And I am logged in as "user"
    Then I should see "User's first post"
    And I should see "User's second post"
