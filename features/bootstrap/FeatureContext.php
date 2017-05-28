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
use Symfony\Bridge\Doctrine\DataFixtures\ContainerAwareLoader;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;

require_once __DIR__.'/../../vendor/phpunit/phpunit/src/Framework/Assert/Functions.php';

/**
 * Defines application features from the specific context.
 */
class FeatureContext extends RawMinkContext implements Context, SnippetAcceptingContext {
  private $currentuser;
  use KernelDictionary;
  
  public function __construct(){
    
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
   * @BeforeScenario @fixtures
   */
  public function loadFixtures(){
    $loader = new ContainerAwareLoader($this->getContainer());
    $loader->loadFromDirectory(__DIR__.'/../../src/AppBundle/DataFixtures');
    $executor = new ORMExecutor($this->getEntityManager());
    $executor->execute($loader->getFixtures(), true);
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
    
    return $user;
  }

  
  /**
     * @Given there is/are :count product(s)
     */
  public function thereAreProducts($count){
    $this->createProducts($count);
  }

  /**
   * @Given I author :count products
   */
  public function iAuthorProducts($count){
    $this->createProducts($count, $this->currentuser);
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
    $this->currentuser = $this->thereIsAnAdminUserWithPassword('admin', 'admin');
    //$this->getSession()->visit('/login');
    $this->visitPath('/login');
    //$this->getPage()->findField('Username')->setValue('admin)
    $this->getPage()->fillField('Username', 'admin');
    $this->getPage()->fillField('Password', 'admin');
    //$this->getPage()->findButton('Login')->press();
    $this->getPage()->pressButton('Login');
  }
  
  /**
   * @When I wait for the modal to load
   */
  public function iWaitForTheModalToLoad(){
    $this->getSession()->wait(
      5000,
      "$('.modal:visible').length > 0"
    );
  }

  /**
     * Pauses the scenario until the user presses a key. Useful when debugging a scenario.
     *
     * @Then (I )break
     */
  public function iPutABreakpoint(){
    fwrite(STDOUT, "\033[s    \033[93m[Breakpoint] Press \033[1;93m[RETURN]\033[0;93m to continue...\033[0m");
    while (fgets(STDIN, 1024) == '') {
    }
    fwrite(STDOUT, "\033[u");
    return;
  }

  /**
     * Saving a screenshot
     *
     * @When I save a screenshot to :filename
     */
  public function iSaveAScreenshotIn($filename){
    sleep(1);
    $this->saveScreenshot($filename, __DIR__.'/../../');
  }
  
  /**
     * @Given the following products exist:
     */
  public function theFollowingProductsExist(TableNode $table){
    $em = $this->getEntityManager();
    
    foreach ($table as $row){
      $product = new Product();
      $product->setName($row['name']);
      $product->setPrice(rand(10, 100));
      $product->setDescription('lorem');
      
      if (isset($row['is published']) && $row['is published'] == 'yes'){
        $product->setIsPublished(true);
      }
      
      $em->persist($product);
    };
    
    $em->flush();
  }

  /**
   * @Then the :rowText row should have a check mark
   */
  public function theRowShouldHaveACheckMark($rowText){
    $row = $this->findRowByText($rowText);
    
    assertContains('fa-check', $row->getHtml(), 'Did not find the check in the row');
  }
  
  /**
   * @When I press :buttonText in the :rowText row
   */
  public function iClickInTheRow($buttonText, $rowText){
    $row = $this->findRowByText($rowText)->pressButton($buttonText);
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
  
  private function createProducts($count, User $author = null){
    $em = $this->getEntityManager();
    for ($i=0; $i < $count; $i++){
      $product = new Product();
      $product->setName('Product '.$i);
      $product->setPrice(rand(10, 100));
      $product->setDescription('lorem');
      
      if($author){
        $product->setAuthor($author);
      }
      
      $em->persist($product);
    }
    $em->flush();
  }
  
  /**
   * 
   * @param $linkText
   * @return \Behat\Mink\Element\NodeElement|mixed|null
   */
  private function findRowByText($rowText) {
    $row = $this->getPage()->find('css', sprintf('table tr:contains("%s")', $rowText));
    assertNotNull($row, 'Could not find a row with text ' . $rowText); 
    
    return $row;
  }
}
