Feature: Edit post
  In order to improve posts
  As a logged-in web user
  I need to be able to edit posts

  @watch
  Scenario: Post exists
    Given a post with title "My old post"
    And I am on the edit page for "My old post"
    When I fill in "Title" with "My old post has now been edited"
    And I press "Save"
    Then the title is updated to "My old post has now been edited"
