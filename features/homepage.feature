@watch
Feature: Homepage
  In order to be exposed to other people's posts
  As a visitor or logged-in user
  I need to be able to see new posts

  Background:
    Given we have loaded the sample data

  Scenario: Not logged in
    Given I am on the homepage
    Then I should see "10" posts
    And I should see "Write it down"
    And I should see "Write and publish with Markdown"

  Scenario: Logged in
    Given I am logged in as "john"
    When I visit "/"
    Then I should be redirected to "/john"

