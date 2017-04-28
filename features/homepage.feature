Feature: Homepage

  Scenario: Not logged in
    Given I am on the homepage
    Then I should see "Write it down"
    And I should see "Write and publish with Markdown"

  Scenario: Logged in
    Given I am logged in as "john"
    When I visit "/"
    Then I should be redirected to "/john"

