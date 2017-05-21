@watch
Feature: Homepage
  In order to be exposed to other people's posts
  As a visitor or logged-in user
  I need to be able to see new posts

  Background:
    Given the following users:
      | username |
      | eia      |
      | johansen |
      | antonsen |
    And the following posts:
      | username | title      | public |
      | eia   | Skrukken   | true   |
      | eia   | Hjernevask | true   |
      | eia   | Påmfri     | false  |
      | johansen | Ørret   | false  |
      | antonsen | Opera   | true   |

  Scenario: Not logged in
    Given I am on the homepage
    Then I should see "3" posts
    And I should see "Skrukken"
    And I should see "Hjernevask"
    And I should see "Opera"
    And I should see "eia"
    And I should see "antonsen"
    And I should see "Write it down"
    And I should see "Write and publish with Markdown"
    But I should not see "Påmfri"
    And I should not see "Ørret"
    And I should not see "johansen"

  Scenario: Logged in
    Given I am logged in as "john"
    When I visit "/"
    Then I should be redirected to "/john"

