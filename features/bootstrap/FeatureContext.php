<?php

use AppBundle\Entity\BaseEntity;
use AppBundle\Entity\Post;
use AppBundle\Entity\PostRole;
use AppBundle\Entity\User;
use AppBundle\Repository\PostRepository;
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
     * @Given I have a post with title :title and slug :slug
     * @param string $title
     * @param string $slug
     */
    public function iHaveAPostWithTitleAndSlug(string $title, string $slug)
    {
        $postService = $this->getContainer()->get('app.post_service');
        $post = $postService->createPost(
            $this->getScenarioArgument('user'),
            $title,
            'Something for the body'
        );

        if ($post->getSlug() !== $slug) {
            $post->setSlug($slug);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($post);
            $entityManager->flush();
        }
    }

    /**
     * @Given :username has a post with slug :slug
     * @param string $username
     * @param string $slug
     */
    public function hasAPostWithSlug(string $username, string $slug)
    {
        throw new PendingException();
    }

    /**
     * @Then the post that used to have the slug :oldSlug should now have the slug :newSlug
     * @param string $oldSlug
     * @param string $newSlug
     */
    public function thePostThatUsedToHaveTheSlugShouldNowHaveTheSlug(string $oldSlug, string $newSlug)
    {
        throw new PendingException();
    }

    /**
     * @Then the post with slug :slug should not have been changed
     * @param string $slug
     */
    public function thePostWithSlugShouldNotHaveBeenChanged(string $slug)
    {
        throw new PendingException();
    }

    /**
     * @Given a user :username
     * @param string $username
     */
    public function aUser(string $username)
    {
        $userManager = $this->getContainer()->get('fos_user.user_manager');

        $user = $userManager->createUser();
        $user->setUsername($username);
        $user->setPlainPassword($username);
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

        /** @var PostRepository $postRepository */
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
     * @param string $postTitle
     */
    public function iAmOnTheEditPageFor(string $postTitle)
    {
        /** @var PostRepository $postRepository */
        $postRepository = $this->getDoctrine()->getRepository('AppBundle:Post');

        /** @var Post $post */
        $post = $postRepository->findAll()[0];

        Assert::assertEquals($postTitle, $post->getTitle());

        $owner = $postRepository->getOwner($post);

        $this->visit('/' . $owner->getUsername() . '/' . $post->getSlug() . '/edit');
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
        $post = $this->getScenarioArgument('post');
        /** @var PostRepository $postRepository */
        $postRepository = $this->getEntityManager()->getRepository('AppBundle:Post');
        $owner = $postRepository->getOwner($post);

        $this->visit('/' . $owner->getUsername() . '/' . $post->getSlug());
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
     * @Given I am logged in as :username
     * @param string $username
     */
    public function iAmLoggedInAs(string $username)
    {
        if (!$this->getUserByUsername($username)) {
            $this->aUser($username);
        }

        $this->login($username, $username);
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
     * @Given that :username has a post with title :title
     * @param string $username
     * @param string $title
     */
    public function thatHasAPostWithTitle(string $username, string $title)
    {
        $postService = $this->getContainer()->get('app.post_service');
        $userManager = $this->getContainer()->get('fos_user.user_manager');

        $post = $postService->createPost(
            $userManager->findUserByUsername($username),
            $title
        );

        $this->setScenarioArgument('post', $post);
    }

    /**
     * @Then the user :username should not exist
     * @param string $username
     */
    public function theUserShouldNotExist(string $username)
    {
        $user = $this->getUserByUsername($username);

        Assert::assertNull($user, 'User was created with reserved username');
    }

    /**
     * @Then we have recorded that :username was created and updated just now
     * @param string $username
     */
    public function weHaveRecordedThatWasCreatedAndUpdatedJustNow(string $username)
    {
        $user = $this->getUserByUsername($username);
        $createdAt = $user->getCreatedAt();
        $updatedAt = $user->getUpdatedAt();
        $aFewSecondsAgo = new \DateTime('-3 seconds');

        Assert::assertInstanceOf('\DateTime', $createdAt);
        Assert::assertInstanceOf('\DateTime', $updatedAt);
        Assert::assertGreaterThan($aFewSecondsAgo, $createdAt);
        Assert::assertGreaterThan($aFewSecondsAgo, $updatedAt);
    }

    /**
     * @When I wait for :numberOfSeconds seconds
     * @param string $numberOfSeconds
     */
    public function iWaitForSeconds(string $numberOfSeconds)
    {
        sleep((integer) $numberOfSeconds);
    }

    /**
     * @Then we have recorded that :username was updated after creation
     */
    public function weHaveRecordedThatWasUpdatedAfterCreation($username)
    {
        $entityManager = $this->getDoctrine()->getManager();

        /** @var \Doctrine\DBAL\Connection $connection */
        $connection = $entityManager->getConnection();

        $statement = $connection->prepare('SELECT created_at, updated_at FROM user WHERE username = :username');
        $statement->bindValue('username', $username);
        $statement->execute();

        $datetimes = $statement->fetchAll()[0];

        $createdAt = DateTime::createFromFormat('Y-m-d H:i:s', $datetimes['created_at']);
        $updatedAt = DateTime::createFromFormat('Y-m-d H:i:s', $datetimes['updated_at']);

        Assert::assertGreaterThan(
            $createdAt->add(new \DateInterval('PT5S')),
            $updatedAt
        );
    }

    /**
     * @Then the system should have recorded that the post :title was created and updated just now
     * @param string $title
     */
    public function theSystemShouldHaveRecordedThatThePostWasCreatedAndUpdatedJustNow(string $title)
    {
        $post = $this->getPostByTitle($title);

        Assert::assertInstanceOf('\AppBundle\Entity\Post', $post);

        $this->assertMetaDatetimesForEntityCreatedJustNow($post);

        $postRoleRepository = $this
            ->getContainer()
            ->get('doctrine')
            ->getManager()
            ->getRepository('AppBundle:PostRole');

        $postRole = $postRoleRepository->findOneBy(['post' => $post]);
        Assert::assertInstanceOf('\AppBundle\Entity\PostRole', $postRole);

        $this->assertMetaDatetimesForEntityCreatedJustNow($postRole);
    }

    /**
     * @Then the system has recorded that the post :title was updated after its creation
     * @param string $title
     */
    public function theSystemHasRecordedThatThePostWasUpdatedAfterItsCreation(string $title)
    {
        $entityManager = $this->getDoctrine()->getManager();

        /** @var \Doctrine\DBAL\Connection $connection */
        $connection = $entityManager->getConnection();

        $statement = $connection->prepare('SELECT created_at, updated_at FROM post WHERE title = :title');
        $statement->bindValue('title', $title);
        $statement->execute();

        $datetimes = $statement->fetchAll()[0];

        $createdAt = DateTime::createFromFormat('Y-m-d H:i:s', $datetimes['created_at']);
        $updatedAt = DateTime::createFromFormat('Y-m-d H:i:s', $datetimes['updated_at']);

        Assert::assertInstanceOf('\DateTime', $createdAt);
        Assert::assertInstanceOf('\DateTime', $updatedAt);
        Assert::assertGreaterThan($createdAt, $updatedAt);
    }

    /**
     * @Then the system should have recorded that the post with slug :slug was created and updated just now
     * @param string $slug
     */
    public function theSystemShouldHaveRecordedThatThePostWithSlugWasCreatedAndUpdatedJustNow(string $slug)
    {
        $post = $this->findPostBySlug($slug);

        Assert::assertInstanceOf('\AppBundle\Entity\Post', $post);

        $this->assertMetaDatetimesForEntityCreatedJustNow($post);

        $postRoleRepository = $this
            ->getContainer()
            ->get('doctrine')
            ->getManager()
            ->getRepository('AppBundle:PostRole');

        $postRole = $postRoleRepository->findOneBy(['post' => $post]);
        Assert::assertInstanceOf('\AppBundle\Entity\PostRole', $postRole);

        $this->assertMetaDatetimesForEntityCreatedJustNow($postRole);
    }

    /**
     * @When I visit :path
     */
    public function iVisit($path)
    {
        $this->visit($path);
    }

    /**
     * @When the server goes down
     */
    public function theServerGoesDown()
    {
        exec('bin/console server:stop');

        $this->setScenarioArgument('webServerHasBeenStopped', true);
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
        $scenarioArguments = $this->getScenarioArguments();

        if (!key_exists($key, $this->getScenarioArguments())) {
            return null;
        }

        $value = $scenarioArguments[$key];

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

        $user = $this->getContainer()->get('fos_user.user_manager')->findUserByUsername($username);

        $this->setScenarioArgument('user', $user);
    }

    /**
     * @Given that :username has a post with title :title and body :body
     * @param string $username
     * @param string $title
     * @param string $body
     */
    public function thatHasAPostWithTitleAndBody(string $username, string $title, string $body)
    {
        $this->thatHasAPostWithTitle($username, $title);

        /** @var Post $post */
        $post = $this->getScenarioArgument('post');

        $post->setBody($body);

        $entityManager = $this->getEntityManager();
        $entityManager->persist($post);
        $entityManager->flush();

        $this->setScenarioArgument('post', $post);
    }

    /**
     * @Then I should see that I am viewing my own posts
     */
    public function iShouldSeeThatIAmViewingMyOwnPosts()
    {
        /** @var User $user */
        $user = $this->getScenarioArgument('user');
        $username = $user->getUsername();

        $this->assertElementOnPage("ul.nav li.active a[href='/$username']");
    }

    /**
     * @param string $username
     * @return User|null
     */
    private function getUserByUsername(string $username)
    {
        $userManager = $this->getContainer()->get('fos_user.user_manager');

        return $userManager->findUserByUsername($username);
    }

    private function debug(): void
    {
        $html = $this->getSession()->getPage()->getHtml();
        $currentUrl = $this->getSession()->getCurrentUrl();
        dump($html);
        dump($currentUrl);
    }

    /**
     * @param string $title
     * @return Post
     */
    private function getPostByTitle(string $title): Post
    {
        $postRepository = $this->getDoctrine()->getRepository('AppBundle:Post');

        /** @var Post $post */
        $post = $postRepository->findOneBy([
            'title' => $title
        ]);

        return $post;
    }

    /**
     * @param BaseEntity $entity
     */
    private function assertMetaDatetimesForEntityCreatedJustNow(BaseEntity $entity): void
    {
        $createdAt = $entity->getCreatedAt();
        $updatedAt = $entity->getUpdatedAt();
        $now = new \DateTime();
        $fiveSecondsAgo = new \DateTime('-5 seconds');

        Assert::assertGreaterThan($fiveSecondsAgo, $createdAt);
        Assert::assertGreaterThanOrEqual($createdAt, $now);
        Assert::assertEquals($createdAt, $updatedAt);
    }

    /**
     * @param string $slug
     * @return Post
     */
    private function findPostBySlug(string $slug)
    {
        $postRepository = $this->getDoctrine()->getRepository('AppBundle:Post');

        /** @var Post $post */
        $post = $postRepository->findOneBy([
            'slug' => $slug
        ]);

        return $post;
    }
}
