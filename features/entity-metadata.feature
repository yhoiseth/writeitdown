Feature: Entity metadata
  In order to record information we may need later
  As the application owner
  I want to make sure that we record when entities are created and modified and by whom

  @watch
  Scenario: User registers themselves
    Given I am on "/register"
    When I fill in "Email" with "billy@bob.com"
    And I fill in "Username" with "billy-bob"
    And I fill in "Password" with "billy-bob"
    And I fill in "Repeat password" with "billy-bob"

    Then we have recorded that "billy-bob" created their own user account just now
    And we have recorded that "billy-bob" updated their own user account just now
