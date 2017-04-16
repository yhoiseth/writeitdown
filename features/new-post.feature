@watch
Feature: New post
  In order to remember my thoughts
  As an authenticated web user
  I need to be able to create new posts

  Background:
    Given a user "writer"

  Scenario: Logged in, create one post
    Given I am logged in as "writer"
    And I am on the homepage
    And I click the "New post" link
    And I fill in "Title" with "My first post"
    And I fill in "Body" with "Some text"
    And I press "Save"
    Then I should be redirected to "/edit/my-first-post"
    And the response status code should be 200

    When I am on "/logout"
    And I am on "edit/my-first-post"
    Then I should be redirected to "/login"

  Scenario: Logged in, colliding slugs
    Given I am logged in as "writer"
    And I am on the homepage

    When I click the "New post" link
    And I fill in "Title" with "My first post"
    And I press "Save"
    Then I should be redirected to "/edit/my-first-post"

    When I click the "New post" link
    And I fill in "Title" with "My first post"
    And I press "Save"
    Then I should be redirected to "/edit/my-first-post-2"

    When I click the "New post" link
    And I fill in "Title" with "My first post"
    And I press "Save"
    Then I should be redirected to "/edit/my-first-post-3"

    When I click the "New post" link
    And I fill in "Title" with "Short"
    And I press "Save"
    Then I should be redirected to "/edit/short"

    When I click the "New post" link
    And I fill in "Title" with "Short"
    And I press "Save"
    Then I should be redirected to "/edit/short-2"

    When I click the "New post" link
    And I fill in "Title" with "Short"
    And I press "Save"
    Then I should be redirected to "/edit/short-3"

  Scenario: Not logged in
    Given I am on "/new"
    Then I should be redirected to "/login"
