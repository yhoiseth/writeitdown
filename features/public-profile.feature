@watch
Feature: Public profile
  In order to know who a writer is
  As a web visitor
  I need to be able to view users' public profiles

  Scenario: Gravatar does not exist
    Given a user "radioface"
    And the user does not have a gravatar
    When I visit "/radioface"
    Then I should see a default gravatar
    And I should see "radioface"

  Scenario: Gravatar exists
    Given a user "yhoiseth"
    And the user has a gravatar
    When I visit "/yhoiseth"
    Then I should see the gravatar
    And I should see "yhoiseth"

  Scenario: Private post
    Given a user "edward"
    And "edward" has a private post with title "Edward's private post" and slug "private"
    When I visit "/edward"
    Then I should not see "Edward's private post"
