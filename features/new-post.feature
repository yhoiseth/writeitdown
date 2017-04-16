Feature: New post
  In order to remember my thoughts
  As an authenticated web user
  I need to be able to create new posts

  Background:
    Given a user "writer"

  Scenario: Logged in
    Given I am logged in as "writer" with password "writer"
    And I am on the homepage
    And I click the "New post" link
    And I fill in "Title" with "My first post"
    And I fill in "Body" with "Some text"
    And I press "Save"
    Then I should be redirected to "/edit/1"
    And the response status code should be 200

    When I am on "/logout"
    And I am on "edit/1"
    Then I should be redirected to "/login"

  Scenario: Not logged in
    Given I am on "/new"
    Then I should be redirected to "/login"
