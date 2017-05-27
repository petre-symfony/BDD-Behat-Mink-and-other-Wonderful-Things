<?php

use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;
use Behat\MinkExtension\Context\MinkContext;
use Behat\MinkExtension\Context\RawMinkContext;
use Behat\Behat\Context\SnippetAcceptingContext;
use Behat\Testwork\Hook\Call\BeforeSuite;
use Behat\Behat\Hook\Call\BeforeScenario;
use Behat\Symfony2Extension\Context\KernelDictionary;
use AppBundle\Entity\User;
use AppBundle\Entity\Product;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;

require_once __DIR__.'/../../vendor/phpunit/phpunit/src/Framework/Assert/Functions.php';

/**
 * Defines application features from the specific context.
 */
class FeatureContext extends RawMinkContext implements Context, SnippetAcceptingContext {
  use KernelDictionary;
  
  public function __construct(){
    
  }

  /**
   * @When I fill in the search box with :term
   */
  public function iFillInTheSearchBoxWith($term){
    // name="searchTerm";
    $searchBox = $this->assertSession()
      ->elementExists('css', '[name="searchTerm"]');
    
    $searchBox->setValue($term);
  }

  /**
   * @When I press the search button
   */
  public function iPressTheSearchButton(){
    $button = $this->assertSession()
      ->elementExists('css', '#search_submit');
    
    $button->press();
  }
  
  /**
     * @Given there is an admin user :username with password :password
     */
  public function thereIsAnAdminUserWithPassword($username, $password){
    $user = new User();
    $user->setUsername($username);
    $user->setPlainPassword($password);
    $user->setRoles(array('ROLE_ADMIN'));
    
    $em = $this->getContainer()->get('doctrine')->getManager();
    $em->persist($user);
    $em->flush();
  }

  /**
   * @BeforeScenario
   */
  public function clearData(){
    $em = $this->getContainer()->get('doctrine')->getManager();
    $purger = new ORMPurger($em);
    $purger->purge();
  }

  
  /**
     * @Given there are :count products
     */
  public function thereAreProducts($count){
    $em = $this->getEntityManager();
    for ($i=0; $i < $count; $i++){
      $product = new Product();
      $product->setName('Product '.$i);
      $product->setPrice(rand(10, 100));
      $product->setDescription('lorem');
      
      $em->persist($product);
    }
    $em->flush();
  }

  /**
   * @When I click :linkText
   */
  public function iClick($linkText){
    //$this->getPage()->findLink($linkText)->click();
    $this->getPage()->clickLink($linkText);
  }

  /**
   * @Then I should see :count products
   */
  public function iShouldSeeProducts($count){
    $table = $this->getPage()->find('css', 'table.table');
    assertNotNull($table, 'Could not found a table');
    
    assertCount(intval($count), $table->findAll('css', 'tbody tr'));
  }
  
  /**
   * @Given I am logged in as an admin
   */
  public function iAmLoggedInAsAnAdmin(){
    $this->thereIsAnAdminUserWithPassword('admin', 'admin');
    //$this->getSession()->visit('/login');
    $this->visitPath('/login');
    //$this->getPage()->findField('Username')->setValue('admin)
    $this->getPage()->fillField('Username', 'admin');
    $this->getPage()->fillField('Password', 'admin');
    //$this->getPage()->findButton('Login')->press();
    $this->getPage()->pressButton('Login');
  }

  /**
   * @return \Behat\Mink\Element\DocumentElement
   */
  private function getPage(){
    return $this->getSession()->getPage();
  }
  
  /**
   * @return \Doctrine\ORM\EntityManager
   */
  private function getEntityManager() {
    return $this->getContainer()->get('doctrine.orm.entity_manager');
  }

}
