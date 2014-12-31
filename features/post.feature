Feature: post
  In order to test AJAX calls quickly
  As a develoepr
  I need to call and test POST requests

  Scenario: I should see no suggest hints without typing something
    Given I have the payload:
    """
    {
      "search":"",
      "action":"search"
    }
    """
    When I request "POST /"
    Then I get a "200" response
    And data is empty
