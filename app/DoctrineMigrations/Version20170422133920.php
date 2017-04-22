<?php

namespace Application\Migrations;

use AppBundle\Entity\User;
use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170422133920 extends AbstractMigration implements ContainerAwareInterface
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
        $userManager = $this->getContainer()->get('fos_user.user_manager');
        $entityManager = $this->getContainer()->get('doctrine')->getManager();

        try {
            /** @var User[] $users */
            $users = $userManager->findUsers();

            foreach ($users as $user) {
                $now = new \DateTime();

                $user->setCreatedAt($now);
                $user->setUpdatedAt($now);

                $entityManager->persist($user);
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
