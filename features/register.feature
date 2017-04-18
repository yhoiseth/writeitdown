Feature: Register
  In order to start using the app
  As a web user
  I need to be able to register

  In order to not occupy a route that Write it down may later need
  As a web user
  I need to be prevented from choosing a reserved username

  Scenario Outline: Reserved usernames
    Given I am on "/register"
    When I fill in "Username" with "<username>"
    And I fill in "Email" with "example@email.com"
    And I fill in "Password" with "some characters"
    And I fill in "Repeat password" with "some characters"
    And I press "Register"
    And the user "<username>" should not exist
    Then I should see "This value should not be identical to string \"<username>\""

    Examples:
      | username      |
      | help          |
      | support       |
      | about         |
      | pricing       |
      | api           |
      | product       |
      | new           |
      | register      |
      | profile       |
      | login         |
      | logout        |
      | resetting     |
      | _wdt          |
      | _profiler     |
      | _error        |
      | login_check   |
      | jobs          |
      | integrations  |
      | add-ons       |
      | templates     |
      | themes        |
      | blog          |
      | news          |
      | downloads     |
      | press         |
      | social        |
      | documentation |
      | customers     |
      | case-studies  |
      | references    |
      | open-source   |
      | contact       |
      | privacy       |
      | terms         |
      | policy        |
      | careers       |
      | developers    |
      | team          |
      | app           |
      | system        |
      | dashboard     |
      | settings      |
      | preferences   |
      | analytics     |
