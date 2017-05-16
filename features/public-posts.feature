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
    And I fill in "Body" with "1. My first point about love"
    And I press "Save immediately"
    And I click the "Publish post" link
    And I should be redirected to "/shakespeare/love"
    And I should see "Post published"
    And I visit "/shakespeare/hate/edit"
    And I wait for "1" seconds
    And I click the "Publish post" link

    When I click the "Logout" link
    And I visit "/shakespeare"
    Then I should see "Love"
    And I should see "Hate"
    And "Hate" should be before "Love"
    But I should not see "Ignorance"

    When I click the "Love" link
    Then the "ol li" element should contain "My first point about love"
    And I should not see "Edit"

  Scenario: Try to publish other user's post
    Given I am logged in as "hacker"
    And I visit "/shakespeare/love/publish"
    Then the response status code should be 403

    When I visit "/shakespeare"
    Then I should not see "Love"
