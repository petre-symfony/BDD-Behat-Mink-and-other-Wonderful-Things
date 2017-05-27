Feature: Search
  In order to find products dinosaurs love
  As a web user
  I need to be able to search for products

  Background:
    Given I am on "/" 

  Scenario: Searching for a product that exists
    When I fill in "searchTerm" with "Samsung"
    And I press "search_submit"
    Then I should see "Samsung Galaxy"

  Scenario: Searching for a product that does not exist
    When I fill in "searchTerm" with "Xbox"
    And I press "search_submit"
    Then I should see "No products found"