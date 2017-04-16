Feature: Show post
  In order to read comfortably and share my thoughts
  As a logged-in web user
  I need to be able to view my posts

  Background:
    Given a user "bob"
    And a user "alice"
    And I am logged in as "bob" with password "bob"

  Scenario: Post exists
    Given a post with title "Bob's post"
    And the post belongs to "bob"
    And the post has body "# Heading 1"
    And I am viewing the given post
    Then I should see the content correctly formatted as HTML

  Scenario: Post I don't own
    And a post which belongs to "alice"
    And I am viewing the given post
    Then the response status code should be 403
