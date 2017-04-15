Feature: List posts
  In order to find specific posts
  As a logged-in web user
  I need to see a list of my posts

  @watch
  Scenario: A few posts
    Given I am on the homepage
    And I have "3" posts
    Then I should see a list with these posts
