Feature: Entity metadata
  In order to record information we may need later
  As the application owner
  I want to make sure that we record when entities are created and modified and by whom

  Scenario: Create and update user
    Given I am on "/register"
    When I fill in "Email" with "billy@bob.com"
    And I fill in "Username" with "billy-bob"
    And I fill in "Password" with "billy-bob"
    And I fill in "Repeat password" with "billy-bob"
    And I press "Register"
    Then we have recorded that "billy-bob" was created and updated just now

    When I wait for "10" seconds
    And I go to "/profile/edit"
    And I fill in "Username" with "billy-bo"
    And I fill in "Current password" with "billy-bob"
    And I press "Update"
    Then we have recorded that "billy-bo" was updated after creation
