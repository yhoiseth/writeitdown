Feature: Edit slug
  In order to make my post URLs look better
  As a web user
  I need to be able to edit post slugs

  Background:
    Given I am logged in as "slugger"

  Scenario: Other user uses slug
    Given I have a post with title "Untitled" and slug "untitled"
    And I visit "/slugger"
    And I click the "Untitled" link
    And I click the "Edit" link
    And I click the "Edit slug" link
    And I should be redirected to "/slugger/untitled/slug/edit"
    And I fill in "Slug" with "popular-slug"
    And I press "Save"
    Then the post that used to have the slug "untitled" should now have the slug "popular-slug"
    And I should be redirected to "/slugger/popular-slug/edit"
    And I should see "Slug updated"

  Scenario: Colliding slug with own post
    Given I have a post with title "Untitled" and slug "untitled"
    And I have a post with title "Other slugger post" and slug "other-slugger-post"
    And I visit "/slugger"
    And I click the "Untitled" link
    And I click the "Edit" link
    And I click the "Edit slug" link
    Then I should be redirected to "/slugger/untitled/slug/edit"
    When I fill in "Slug" with "other-slugger-post"
    And I press "Save"
    Then I should see "You have to choose a unique slug"

  Scenario: Other user's post
    Given a user "other-slugger"
    And "other-slugger" has a post with slug "popular-slug"
    When I visit "other-slugger/popular-slug/slug/edit"
    Then the response status code should be 403

  Scenario Outline: Invalid characters
    Given I have a post with title "Untitled" and slug "untitled"
    And I visit "/slugger"
    And I click the "Untitled" link
    And I click the "Edit" link
    And I click the "Edit slug" link
    And I should be redirected to "/slugger/untitled/slug/edit"
    And I fill in "Slug" with "<input>"
    And I press "Save"
    Then the post that used to have the slug "untitled" should now have the slug "<output>"

    Examples:
      | input       | output      |
      | white space | white-space |
      | UPPERCASE   | uppercase   |
