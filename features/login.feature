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

  Scenario: Remember me
    Given a user "elephant"
    And I am on "/login"
    When I fill in "Username" with "elephant"
    And I fill in "Password" with "elephant"
    And I check "Remember me"
    And I press "Log in"
    Then I am logged in
    When the sessions are deleted
    And I visit "/"
    Then I am logged in

  Scenario: Wrong password
    Given a user "crook"
    And I am on "/login"
    When I fill in "Username" with "crook"
    And I fill in "Password" with "wrong"
    And I press "Log in"
    Then I should see "Invalid credentials"
    And I should be redirected to "/login"
