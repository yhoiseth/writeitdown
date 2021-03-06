
Feature: Navigation
  In order to find my way around the application
  As a web user
  I need to have navigation on all the different pages

  Scenario: Logged in on homepage
    Given a user "navigator"
    And I am logged in as "navigator"
    And I am on the homepage
    Then I should see "Write it down"
    And I should see "New post"

  Scenario Outline: All pages, logged in
    Given a user "navigator"
    And I am logged in as "navigator"
    And that "navigator" has a post with title "Navigator's post"
    When I am on "<path>"
    Then I should see "Write it down"
    And I should see "New post"
    And I should see "Logout"
    And I should see "My posts"
    And the "title" element should contain "<title> Write it down"

    Examples:
      | path                             | title                                       |
      | /profile                         |                                             |
      | /profile/edit                    |                                             |
      | /profile/change-password         |                                             |
      | /register/confirmed              | Registration confirmed \|                   |
      | /navigator/navigator-s-post/edit | Navigator's post \| Editing \| navigator \| |
      | /navigator/navigator-s-post      | Navigator's post \| navigator \|            |
      | /navigator                       | navigator \|                                |

  Scenario Outline: Public routes, not logged in
    When I am on "<path>"
    Then I should see "Write it down"
    And I should not see "New post"
    And I should see "Login"
    And I should see "Register"
    And the "title" element should contain "<title> Write it down"

    Examples:
      | path               | title             |
      | /                  | Home \|           |
      | /register          | Register \|       |
      | /login             | Login \|          |
      | /resetting/request | Reset password \| |
