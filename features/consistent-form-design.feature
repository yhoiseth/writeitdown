Feature: Consistent form design
  In order to enjoy using the application
  As web user
  I should not experience any inconsistent form designs

  Scenario Outline: All forms available when logged in
    Given a user "design-conscious"
    And I am logged in as "design-conscious"
    And that "design-conscious" has a post with title "Example post"
    When I am on "<path>"
    Then the form should be styled using Twitter Bootstrap

    Examples:
      | path                                |
      | /register                           |
      | /login                              |
      | /new                                |
      | /profile/edit                       |
      | /profile/change-password            |
      | /resetting/request                  |
      | /design-conscious/example-post/edit |