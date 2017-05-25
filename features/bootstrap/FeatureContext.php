<?php

use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;
use Behat\MinkExtension\Context\MinkContext;
use Behat\Behat\Context\SnippetAcceptingContext;

/**
 * Defines application features from the specific context.
 */
class FeatureContext extends MinkContext implements Context, SnippetAcceptingContext
{
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
    shell_exec($command);
  }

  /**
   * @Then I should see :arg1 in the output
   */
  public function iShouldSeeInTheOutput($arg1)
  {
      throw new PendingException();
  }
    
}
