Feature: view
  In order to find bookmarks
  As a GUEST user
  I need to see a search input

  @javascript
  Scenario: Search field is provided
    Given I am on "http://links.local/"
    Then I should see an "input#search" element
    And I should see an "div.menu" element
