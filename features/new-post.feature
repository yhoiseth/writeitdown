Feature: New post
  In order to remember my thoughts
  As an authenticated web user
  I need to be able to create new posts

  Background:
    Given a user "writer"

    @watch
  Scenario: Logged in, create one post
    Given I am logged in as "writer"
    And I am on the homepage
    And I click the "New post" link
#    And I fill in "Title" with "My first post"
#    And I fill in "Body" with "Some text"
#    And I press "Save"
    Then I should be redirected to "/writer/untitled/edit"
    And the response status code should be 200
    And I wait for "1" seconds
    And the system should have recorded that the post "Untitled" was created and updated just now

    When I click the "New post" link
    Then I should be redirected to "/writer/untitled-2/edit"
    And the response status code should be 200
    And the system should have recorded that the post with slug "untitled-2" was created and updated just now

    When I click the "New post" link
    Then I should be redirected to "/writer/untitled-3/edit"
    And the response status code should be 200
    And the system should have recorded that the post with slug "untitled-2" was created and updated just now

    When I visit "/logout"
    And I visit "/writer/untitled/edit"
    Then I should be redirected to "/login"

#  Scenario: Logged in, colliding slugs
#    Given I am logged in as "writer"
#    And I am on the homepage
#
#    When I click the "New post" link
#    And I fill in "Title" with "My first post"
#    And I press "Save"
#    Then I should be redirected to "/writer/my-first-post/edit"
#
#    When I click the "New post" link
#    And I fill in "Title" with "My first post"
#    And I press "Save"
#    Then I should be redirected to "/writer/my-first-post-2/edit"
#
#    When I click the "New post" link
#    And I fill in "Title" with "My first post"
#    And I press "Save"
#    Then I should be redirected to "/writer/my-first-post-3/edit"
#
#    When I click the "New post" link
#    And I fill in "Title" with "Short"
#    And I press "Save"
#    Then I should be redirected to "/writer/short/edit"
#
#    When I click the "New post" link
#    And I fill in "Title" with "Short"
#    And I press "Save"
#    Then I should be redirected to "/writer/short-2/edit"
#
#    When I click the "New post" link
#    And I fill in "Title" with "Short"
#    And I press "Save"
#    Then I should be redirected to "/writer/short-3/edit"

  Scenario: Not logged in
    Given I am on "/new"
    Then I should be redirected to "/login"
