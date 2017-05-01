@watch
Feature: Edit slug
  In order to make my post URLs look better
  As a web user
  I need to be able to edit post slugs

  Background:
    Given I am logged in as "slugger"
    And I have a post with title "Untitled" and slug "untitled"
    And I visit "/"
    And I click the "Untitled" link
    And I click the "Edit slug" link
    And I should be redirected to "/slugger/untitled/slug/edit"

  Scenario: Other user uses slug
    Given a user "other-slugger"
    And "other-slugger" has a post with slug "popular-slug"
    Given I fill in "Slug" with "popular-slug"
    And I press "Save"
    Then the post that used to have the slug "untitled" should now have the slug "popular-slug"
    And I should see "Slug updated"

  Scenario: Colliding slug with own post
    Given I have a post with title "Other slugger post" and slug "other-slugger-post"
    When I fill in "Slug" with "other-slugger-post"
    Then I should see "You have to choose a unique slug"
    And the post with slug "other-slugger-post" should not have been changed
