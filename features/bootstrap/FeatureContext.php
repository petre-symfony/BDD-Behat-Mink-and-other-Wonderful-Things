<?php

use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;
use Behat\MinkExtension\Context\MinkContext;
use Behat\Behat\Context\SnippetAcceptingContext;
use Behat\Behat\Hook\Call\AfterScenario;
use Behat\Behat\Hook\Call\BeforeScenario;

require_once __DIR__.'/../../vendor/phpunit/phpunit/src/Framework/Assert/Functions.php';

/**
 * Defines application features from the specific context.
 */
class FeatureContext extends MinkContext implements Context, SnippetAcceptingContext {
  private $output;
  /**
   * Initializes context.
   *
   * Every scenario gets its own context instance.
   * You can also pass arbitrary arguments to the
   * context constructor through behat.yml.
   */
  public function __construct()
  {
    
  }
  
  /**
   * @BeforeScenario
   */
  public function moveIntoTestDir(){
    if(!is_dir('test')){
      mkdir('test'); 
    }
    chdir('test'); 
  }


  /**
   * @AfterScenario
   */
  public function moveOutOfTestDir() {
    chdir('..');
    if (is_dir('test')){
      system('rm -r '.realpath('test'));
    }
  }
  
  /**
   * @Given there is a file named :filename
   */
  public function thereIsAFileNamed($filename)
  {
    touch($filename);
  }

  /**
   * @When I run :command
   */
  public function iRun($command)
  {
    $this->output = shell_exec($command);
  }

  /**
   * @Then I should see :string in the output
   */
  public function iShouldSeeInTheOutput($string)
  { 
    assertContains(
      $string, 
      $this->output,
      sprintf('Did not see "%s" in output "%s"', $string, $this->output)      
    );
  }
    
}
