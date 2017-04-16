@watch
Feature: Navigation
  In order to find my way around the application
  As a web user
  I need to have navigation on all the different pages

  Scenario: Logged in on homepage
    Given a user "navigator" with password "navigator"
    And I am logged in as "navigator" with password "navigator"
    And I am on the homepage
    Then I should see "Write it down"
    And I should see "New post"
