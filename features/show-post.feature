Feature: View post
  In order to read comfortably and share my thoughts
  As a logged-in web user
  I need to be able to view my posts

  Scenario: Post exists
    Given a post with markdown-formatted body
    And I am viewing the given post
    Then I should see the content correctly formatted as HTML

  @watch
  Scenario: Post I don't own
    Given a user "bob" with password "bob"
    And a user "alice" with password "alice"
    And I am logged in as "bob" with password "bob"
    And a post which belongs to "alice"
    And I am viewing the given post
    Then the response status code should be 403
