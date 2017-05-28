Feature: Product Admin Area
  In order to maintain the products shown on the site
  As an admin user
  I need to be able to add/edit/delete products

  Background:
    Given I am logged in as an admin
  
  Scenario: List available products
    Given there are 5 products
    And I am on "/admin" 
    When I click "Products"
    Then I should see 5 products

  Scenario: Products show author
    Given I author 5 products
    When I go to "/admin/products"
    Then I should not see "Anonymous"

  Scenario: Show published/unpublished
    Given the following products exist:
      | name | is published |
      | foo1 |     yes      |
      | foo2 |     no       |
    When I go to "/admin/products"
    Then the "foo1" row should have a check mark

  @javascript
  Scenario: Add a new product
    Given I am on "/admin/products"
    When I click "New Product"
    And I wait for the modal to load
    And I save a screenshot to "shot.png"
    And I fill in "Name" with "Veloci-chew toy"
    And I fill in "Price" with "20"
    And I fill in "Description" with "Have your raptor chew on this instead!"
    And I press "Save"
    Then I should see "Product created FTW!"
    And I should see "Veloci-chew toy"
    And I should not see "Anonymous"
