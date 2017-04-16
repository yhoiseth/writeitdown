Feature: New post
  In order to remember my thoughts
  As an authenticated web user
  I need to be able to create new posts

  Background:
    Given a user "writer" with password "writer"

  Scenario: Logged in
    Given I am logged in as "writer" with password "writer"
    And I am on "/new"
    And I fill in "Title" with "My first post"
    And I press "Save"
    Then my post should be saved

  Scenario: Not logged in
    Given I am on "/new"
    Then I should be redirected to "/login"
