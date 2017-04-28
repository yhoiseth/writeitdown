Feature: Edit post
  In order to improve posts
  As a logged-in web user
  I need to be able to edit posts

  Background:
    Given a user "editor"
    And a user "malicious"
    And that "editor" has a post with title "Editor's existing post"

    @watch @javascript
  Scenario: My post
    Given I am logged in as "editor"
    And I am on the edit page for "Editor's existing post"
    When I fill in "Title" with "My old post has now been edited"
    And I fill in "Body" with "Whatever's on my mind"
    And I wait for "1" seconds
#    And I press "Save"
    Then the title is updated to "My old post has now been edited"
    And the system has recorded that the post "My old post has now been edited" was updated after its creation

  Scenario: Someone else's post
    Given I am logged in as "malicious"
    And I am on the edit page for "Editor's existing post"
    Then the response status code should be 403

#  Scenario: Colliding slugs
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
