<?php

use Behat\Behat\Context\Context;
use Behat\Behat\Context\SnippetAcceptingContext;
use Behat\Behat\Hook\Call\AfterScenario;
use Behat\Behat\Hook\Call\BeforeScenario;

require_once __DIR__.'/../../vendor/phpunit/phpunit/src/Framework/Assert/Functions.php';

class CommandLineProcessContext implements Context, SnippetAcceptingContext{
  private $output;
  
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
  public function thereIsAFileNamed($filename){
    touch($filename);
  }

  /**
   * @When I run :command
   */
  public function iRun($command){
    $this->output = shell_exec($command);
  }

  /**
   * @Then I should see :string in the output
   */
  public function iShouldSeeInTheOutput($string){ 
    assertContains(
      $string, 
      $this->output,
      sprintf('Did not see "%s" in output "%s"', $string, $this->output)      
    );
  }
  
  /**
   * @Given there is a dir named :dir
   */
  public function thereIsADirNamed($dir){
    mkdir($dir);    
  }

}
