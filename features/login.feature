Feature: Login
  In order to write and publish posts
  As a web user
  I need to be able to login

  Scenario: Correct password
    Given a user "marcus"
    And I am on "/login"
    When I fill in "Username" with "marcus"
    And I fill in "Password" with "marcus"
    And I press "Log in"
    Then I am logged in

    @watch
  Scenario: Remember me
    Given a user "elephant"
    And I am on "/login"
    When I fill in "Username" with "elephant"
    And I fill in "Password" with "elephant"
    And I check "Remember me"
    And I press "Log in"
    Then I am logged in
    When the sessions are deleted
    Then I am logged in
