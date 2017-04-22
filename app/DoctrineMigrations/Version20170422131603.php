<?php

namespace Application\Migrations;

use AppBundle\Entity\Post;
use AppBundle\Repository\PostRepository;
use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170422131603 extends AbstractMigration implements ContainerAwareInterface
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

        try {
            /** @var Post[] $posts */
            $posts = $postRepository->findAll();

            foreach ($posts as $post) {
                $now = new \DateTime();
                $post->setCreatedAt($now);
                $post->setUpdatedAt($now);

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
