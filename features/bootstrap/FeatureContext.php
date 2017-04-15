<?php

use Behat\Behat\Tester\Exception\PendingException;
use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;
use Behat\MinkExtension\Context\MinkContext;
use Behat\Symfony2Extension\Context\KernelDictionary;

/**
 * Defines application features from the specific context.
 */
class FeatureContext extends MinkContext implements Context
{
    use KernelDictionary;

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
     * @Given a user :arg1 with password :arg2
     */
    public function aUserWithPassword($arg1, $arg2)
    {
        $userManager = $this->getContainer()->get('fos_user.user_manager');



        throw new PendingException();
    }

    /**
     * @Then I am logged in
     */
    public function iAmLoggedIn()
    {
        throw new PendingException();
    }
}
