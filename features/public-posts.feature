@watch
Feature: Public posts
  In order to share my thoughts with anyone
  As a web user
  I need to be able to publish my posts

  Background:
    Given I am logged in as "shakespeare"
    And I have a post with title "Love" and slug "love"
    And I have a post with title "Hate" and slug "hate"
    And I have a post with title "Ignorance" and slug "ignorance"

  Scenario: No public posts
    Given I click the "Logout" link
    And I visit "/shakespeare"
    Then I should see "No posts yet"

  Scenario: Two public posts
    Given I visit "/shakespeare/love/edit"
    And I click the "Publish post" link
    And I visit "/shakespeare/hate/edit"
    And I click the "Publish post" link

    When I click the "Logout" link
    And I visit "/shakespeare"
    Then I should see "Love"
    And I should see "Hate"
    But I should not see "Ignorance"

    When I click the "Love" link
    Then I should see the contents of the posts
