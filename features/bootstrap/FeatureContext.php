<?php

use AppBundle\Entity\Post;
use Behat\Behat\Tester\Exception\PendingException;
use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;
use Behat\MinkExtension\Context\MinkContext;
use Behat\Symfony2Extension\Context\KernelDictionary;
use PHPUnit\Framework\Assert;

/**
 * Defines application features from the specific context.
 */
class FeatureContext extends MinkContext implements Context
{
    /**
     * @var array $scenarioArguments
     */
    private $scenarioArguments = [];

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
     * @Given I have already logged in
     */
    public function iHaveAlreadyLoggedIn()
    {
        $this->aUserWithPassword('marcus', 'aurelius');
        $this->visit("/login");
        $this->fillField('Username', 'marcus');
        $this->fillField('Password', 'aurelius');
        $this->pressButton('Log in');
    }

    /**
     * @Then my post should be saved
     */
    public function myPostShouldBeSaved()
    {
        $postRepository = $this->getContainer()->get('doctrine')->getRepository('AppBundle:Post');

        $post = $postRepository->findOneBy([
            'title' => 'My first post',
        ]);

        Assert::assertNotNull($post, "Post wasn't created like expected.");
    }

    /**
     * @Given a post with title :title
     */
    public function aPostWithTitle($title)
    {
        $post = new Post();
        $post->setTitle($title);
        $entityManager = $this->getContainer()->get('doctrine')->getManager();
        $entityManager->persist($post);
        $entityManager->flush();

        $this->addScenarioArgument('postTitle', $title);
    }

    /**
     * @Given I am on the edit page for :postTitle
     */
    public function iAmOnTheEditPageFor($postTitle)
    {
        $post = $this
            ->getContainer()
            ->get('doctrine')
            ->getRepository('AppBundle:Post')
            ->findOneBy([
                'title' => $postTitle,
        ]);

        $this->visit('/edit/' . $post->getId());

        $this->assertResponseStatus(200);
    }

    /**
     * @Then the title is updated to :editedTitle
     */
    public function theTitleIsUpdatedTo($editedTitle)
    {
        $postRepository = $this->getContainer()->get('doctrine')->getRepository('AppBundle:Post');

        Assert::assertNull($postRepository->findOneBy([
            'title' => $this->getScenarioArgument('postTitle'),
        ]));

        Assert::assertInstanceOf('\AppBundle\Entity\Post', $postRepository->findOneBy([
            'title' => $editedTitle,
        ]));

    }

    /**
     * @BeforeScenario
     */
    public function prepareDatabase()
    {
        $commands = [
            'doctrine:database:create',
            'doctrine:database:drop --force',
            'doctrine:database:create',
            'doctrine:migrations:migrate --no-interaction',
        ];

        foreach ($commands as $command) {
            exec('bin/console ' . $command . ' --env=test');
        }
    }

    /**
     * @return array
     */
    private function getScenarioArguments(): array
    {
        return $this->scenarioArguments;
    }

    /**
     * @param array $scenarioArguments
     */
    private function setScenarioArguments(array $scenarioArguments)
    {
        $this->scenarioArguments = $scenarioArguments;
    }

    /**
     * @param string $key
     * @param mixed $value
     */
    private function addScenarioArgument(string $key, $value)
    {
        $scenarioArguments = $this->getScenarioArguments();
        $scenarioArguments[$key] = $value;
        $this->setScenarioArguments($scenarioArguments);
    }

    /**
     * @param string $key
     * @return mixed $value
     */
    private function getScenarioArgument(string $key)
    {
        $value = $this->getScenarioArguments()[$key];

        return $value;
    }
}
