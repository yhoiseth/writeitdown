<?php

use AppBundle\Entity\Post;
use AppBundle\Entity\PostRole;
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

        $entityManager = $this->getEntityManager();

        $entityManager->persist($user);
        $entityManager->flush();
    }

    /**
     * @Then I am logged in
     */
    public function iAmLoggedIn()
    {
        $this->visit('/profile');
        $this->assertPageContainsText('Logout');
    }


    /**
     * @Given I have already logged in
     */
    public function iHaveAlreadyLoggedIn()
    {
        $this->aUserWithPassword('marcus', 'aurelius');
        $this->login('marcus', 'aurelius');
    }

    /**
     * @Then my post should be saved
     */
    public function myPostShouldBeSaved()
    {
        $postRepository = $this->getDoctrine()->getRepository('AppBundle:Post');

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
        $entityManager = $this->getEntityManager();
        $entityManager->persist($post);
        $entityManager->flush();

        $this->setScenarioArgument('post', $post);
    }

    /**
     * @Given the post belongs to :username
     * @param string $username
     */
    public function thePostBelongsTo(string $username)
    {
        $entityManager = $this->getEntityManager();
        $post = $this->getScenarioArgument('post');

        /** @var \AppBundle\Repository\PostRepository $postRepository */
        $postRepository = $this->getDoctrine()->getRepository('AppBundle:Post');
        $userManager = $this->getContainer()->get('fos_user.user_manager');

        $postRepository->addRole(PostRole::TYPE_OWNER, $post, $userManager->findUserByUsername($username));
        $this->setScenarioArgument('post', $post);
    }

    /**
     * @Given the post has body :body
     * @param string $body
     */
    public function thePostHasBody(string $body)
    {
        $entityManager = $this->getEntityManager();

        /** @var Post $post */
        $post = $this->getScenarioArgument('post');
        $post->setBody($body);
        $entityManager->persist($post);
        $entityManager->flush();

        $this->setScenarioArgument('post', $post);
    }

    /**
     * @Given I am on the edit page for :postTitle
     */
    public function iAmOnTheEditPageFor($postTitle)
    {
        $post = $this->getDoctrine()
            ->getRepository('AppBundle:Post')
            ->findOneBy([
                'title' => $postTitle,
        ]);

        $this->visit('/edit/' . $post->getId());
    }

    /**
     * @Then the title is updated to :editedTitle
     */
    public function theTitleIsUpdatedTo($editedTitle)
    {
        $postRepository = $this->getDoctrine()->getRepository('AppBundle:Post');

        Assert::assertNull($postRepository->findOneBy([
            'title' => $this->getScenarioArgument('post')->getTitle(),
        ]));

        Assert::assertInstanceOf('\AppBundle\Entity\Post', $postRepository->findOneBy([
            'title' => $editedTitle,
        ]));

    }

    /**
     * @Given I have :count posts
     */
    public function iHavePosts(string $count)
    {
        $entityManager = $this->getEntityManager();

        $posts = [];

        for ($index = 0; $index < $count;  $index++) {
            $post = new Post();
            $post->setTitle('Title for post ' . $index);
            $entityManager->persist($post);
            $posts[] = $post;
        }

        $entityManager->flush();

        $this->setScenarioArgument('posts', $posts);
    }

    /**
     * @Then I should see a list with these posts
     */
    public function iShouldSeeAListWithThesePosts()
    {
        $this->visit('');

        /** @var Post[] $posts */
        $posts = $this->getScenarioArgument('posts');

        foreach ($posts as $post) {
            $this->assertPageContainsText($post->getTitle());
        }
    }

    /**
     * @Given a post with markdown-formatted body belonging to :username
     * @param string $username
     */
    public function aPostWithMarkdownFormattedBodyBelongingTo(string $username)
    {
        $post = new Post();
        $post->setTitle('Post with markdown-formatted content');
        $post->setBody('# Heading 1');
        $entityManager = $this->getEntityManager();
        $entityManager->persist($post);
        $entityManager->flush();
        $this->setScenarioArgument('post', $post);
    }

    /**
     * @Given I am viewing the given post
     */
    public function iAmViewingTheGivenPost()
    {
        $this->visit('/' . $this->getScenarioArgument('post')->getId());
    }

    /**
     * @Then I should see the content correctly formatted as HTML
     */
    public function iShouldSeeTheContentCorrectlyFormattedAsHtml()
    {
        $this->assertResponseStatus(200);
        $this->assertElementContainsText('h1', 'Heading 1');
    }

    /**
     * @Given I am logged in as :username with password :password
     * @param string $username
     * @param string $password
     */
    public function iAmLoggedInAsWithPassword(string $username, string $password)
    {
        $this->login($username, $password);
    }

    /**
     * @Given a post which belongs to :username
     */
    public function aPostWhichBelongsTo($username)
    {
        $entityManager = $this->getEntityManager();
        $userManager = $this->getContainer()->get('fos_user.user_manager');

        $post = new Post();
        $post->setTitle('Belongs to Alice');
        $entityManager->persist($post);

        $postRole = new PostRole();
        $postRole->setPost($post);
        $postRole->setUser($userManager->findUserByUsername($username));
        $postRole->setType(PostRole::TYPE_OWNER);
        $entityManager->persist($postRole);
        $entityManager->flush();
        $this->setScenarioArgument('post', $post);
    }

    /**
     * @Then I should be redirected to :path
     * @param string $path
     */
    public function iShouldBeRedirectedTo(string $path)
    {
        $this->assertUrlRegExp('#' . $path . '#');
    }

    /**
     * @Given I click the :linkText link
     */
    public function iClickTheLink($linkText)
    {
        $this->clickLink($linkText);
    }

    /**
     * @Then the form should be styled using Twitter Bootstrap
     */
    public function theFormShouldBeStyledUsingTwitterBootstrap()
    {
        $this->assertElementOnPage('div.form-group');
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
    private function setScenarioArgument(string $key, $value)
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

    /**
     * @return \Doctrine\Bundle\DoctrineBundle\Registry|object
     */
    private function getDoctrine()
    {
        return $this->getContainer()->get('doctrine');
    }

    /**
     * @return \Doctrine\Common\Persistence\ObjectManager|object
     */
    private function getEntityManager()
    {
        return $this->getDoctrine()->getManager();
    }

    /**
     * @param string $username
     * @param string $password
     */
    private function login(string $username, string $password): void
    {
        $this->visit("/login");
        $this->fillField('Username', $username);
        $this->fillField('Password', $password);
        $this->pressButton('Log in');
    }
}
