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
     * @Given a user :username with password :password
     */
    public function aUserWithPassword($username, $password)
    {
        $userManager = $this->getContainer()->get('fos_user.user_manager');

        $user = $userManager->createUser();
        $user->setUsername($username);
        $user->setPlainPassword($password);
        $user->setEmail($username . '@example.com');
        $user->setEnabled(true);

        $entityManager = $this->getContainer()->get('doctrine')->getManager();

        $entityManager->persist($user);
        $entityManager->flush();
    }

    /**
     * @Then I am logged in
     */
    public function iAmLoggedIn()
    {
        $this->visit('/profile');
        $this->assertPageContainsText('Logged in as marcus');
    }

    /**
     * @BeforeScenario
     */
    public function clearData()
    {
        exec('bin/console doctrine:database:create --env=test');
        exec('bin/console doctrine:database:drop --env=test --force');
        exec('bin/console doctrine:database:create --env=test');
        exec('bin/console doctrine:schema:update --env=test --force');
    }
}
