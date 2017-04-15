Feature: New post
  In order to remember my thoughts
  As an authenticated web user
  I need to be able to create new posts

  Scenario: With title
    Given I have already logged in
    And I am on "/new"
    And I fill in "Title" with "My first post"
    And I press "Save"
    Then my post should be saved
