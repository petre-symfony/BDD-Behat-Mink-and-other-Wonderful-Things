Feature: Product Admin Area
  In order to maintain the products shown on the site
  As an admin user
  I need to be able to add/edit/delete products

  Scenario: List available products
    Given there are 5 products
    And I am on "/admin"
    When I click "Products"
    Then I should see 5 products
