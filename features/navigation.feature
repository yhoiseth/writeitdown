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

  @watch
  Scenario Outline: All pages, logged in
    Given a user "navigator" with password "navigator"
    And I am logged in as "navigator" with password "navigator"
    And a post with title "Navigator's post"
    And the post belongs to "navigator"
    When I am on "<path>"
    Then I should see "Write it down"
    And I should see "New post"

    Examples:
      | path                     |
      | /                        |
      | /register                |
      | /login                   |
      | /new                     |
      | /profile                 |
      | /profile/edit            |
      | /profile/change-password |
      | /resetting/request       |
      | /register/confirmed      |
      | /edit/1                  |
      | /1                       |
