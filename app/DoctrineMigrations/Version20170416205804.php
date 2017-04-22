<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use AppBundle\Repository\PostRepository;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;


/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170416205804 extends AbstractMigration implements ContainerAwareInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {

        $container = $this->getContainer();
        $entityManager = $container->get('doctrine')->getManager();

        /** @var PostRepository $postRepository */
        $postRepository = $entityManager->getRepository('AppBundle:Post');

        /**
         * This try/catch-block proved necessary when a property (column) was later added to the Post entity.
         * Running all the migrations from a blank database would then fail because a column was missing from Post,
         * throwing exceptions. (This migration is run before the one adding the missing
         * column – Version20170422124140.)
         */
        try {
            $posts = $postRepository->findAll();

            $postService = $container->get('app.post_service');

            foreach ($posts as $post) {
                $post = $postService->setSlug(
                    $postRepository->getOwner($post),
                    $post
                );

                $entityManager->persist($post);
                $entityManager->flush();
            }
        } catch (\Exception $exception) {

        }
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }


    /**
     * Sets the container.
     *
     * @param ContainerInterface|null $container A ContainerInterface instance or null
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     * @return ContainerInterface
     */
    public function getContainer(): ContainerInterface
    {
        return $this->container;
    }
}
