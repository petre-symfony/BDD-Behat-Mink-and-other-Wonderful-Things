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
    $em->createQuery('DELETE FROM AppBundle:Product')->execute();
    $em->createQuery('DELETE FROM AppBundle:User')->execute();
  }

  /**
   * @return \Behat\Mink\Element\DocumentElement
   */
  private function getPage(){
    return $this->getSession()->getPage();
  }
 
}
