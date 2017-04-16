@watch
Feature: Edit post
  In order to improve posts
  As a logged-in web user
  I need to be able to edit posts

  Background:
    Given a user "editor"
    And a user "malicious"
    And that "editor" has a post with title "Editor's existing post"

  Scenario: My post
    Given I am logged in as "editor"
    And I am on the edit page for "Editor's existing post"
    When I fill in "Title" with "My old post has now been edited"
    And I fill in "Body" with "Whatever's on my mind"
    And I press "Save"
    Then the title is updated to "My old post has now been edited"

  Scenario: Someone else's post
    Given I am logged in as "malicious"
    And I am on the edit page for "Editor's existing post"
    Then the response status code should be 403
