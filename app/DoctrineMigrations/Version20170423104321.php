<?php

namespace Application\Migrations;

use AppBundle\Entity\PostRole;
use AppBundle\MigrationUtilities\BaseMigration;
use AppBundle\Repository\PostRoleRepository;
use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170423104321 extends BaseMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $container = $this->getContainer();
        $entityManager = $container->get('doctrine')->getManager();

        /** @var PostRoleRepository $postRoleRepository */
        $postRoleRepository = $entityManager->getRepository('AppBundle:PostRole');

        try {
            /** @var PostRole[] $postRoles */
            $postRoles = $postRoleRepository->findAll();

            foreach ($postRoles as $postRole) {
                $now = new \DateTime();
                $postRole->setCreatedAt($now);
                $postRole->setUpdatedAt($now);

                $entityManager->persist($postRole);
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
}
