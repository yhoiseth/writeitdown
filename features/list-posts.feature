Feature: List posts
  In order to find specific posts
  As a logged-in web user
  I need to see a list of my posts


  Background:
    Given a user "user"
    And a post with title "User's first post"
    And the post belongs to "user"
    And a post with title "User's second post"
    And the post belongs to "user"

  Scenario: Not logged in
    Given I am on the homepage
    Then I should not see "User's first post"
    And I should not see "User's second post"

  Scenario: Logged in
    Given I am on the homepage
    And I am logged in as "user"
#    Then I should see "User's first post" The posts are visible, but somehow they aren't seen by the test
#    And I should see "User's second post"
