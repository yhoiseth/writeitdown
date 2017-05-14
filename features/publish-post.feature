Feature: Publish post
  In order to share my thoughts with anyone
  As a web user
  I need to be able to publish my posts

  Background:
    Given a user "shakespeare"
    And I visit "/shakespeare"

  Scenario: No public posts
    Then I should see "No posts yet"

  Scenario: Two public posts
    Given "shakespeare" has a public post with title "Love"
    And "shakespeare" has a public post with title "Hate"
    Then I should see "Love"
    And I should see "Hate"

    When I click the "Love" link
    Then I should see the contents of the posts
