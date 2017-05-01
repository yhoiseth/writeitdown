Feature: Edit post
  In order to improve posts
  As a logged-in web user
  I need to be able to edit posts

  Background:
    Given a user "editor"
    And a user "malicious"
    And that "editor" has a post with title "Editor's existing post"

  @javascript
  Scenario: My post
    Given I am logged in as "editor"
    And I am on the edit page for "Editor's existing post"
    Then I should see "Your changes are saved automatically every 10 seconds"
    When I fill in "Title" with "My old post has now been edited"
    And I fill in "Body" with "Whatever's on my mind"
    And I wait for "13" seconds
    Then the title is updated to "My old post has now been edited"
    And the system has recorded that the post "My old post has now been edited" was updated after its creation
    And I should see "Your changes are saved automatically every 10 seconds"

  Scenario: Someone else's post
    Given I am logged in as "malicious"
    And I am on the edit page for "Editor's existing post"
    Then the response status code should be 403

  Scenario: Colliding slugs with other user
    Given a user "bob"
    And a user "alice"
    And that "bob" has a post with title "Post" and body "Written by Bob"
    And that "alice" has a post with title "Post" and body "Written by Alice"
    And I am logged in as "bob"
    When I am on "/bob/post/edit"
    Then I should see "Written by Bob"

    When I am logged in as "alice"
    And I am on "/alice/post/edit"
    Then I should see "Written by Alice"
