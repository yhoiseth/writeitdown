<?php

namespace AppBundle\Service;


use AppBundle\Entity\Post;
use AppBundle\Entity\PostRole;
use AppBundle\Entity\User;
use Cocur\Slugify\Slugify;
use Doctrine\ORM\EntityManager;
use function Stringy\create as s;
use Stringy\Stringy;

class PostService
{
    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var Slugify
     */
    private $slugify;

    /**
     * PostService constructor.
     * @param EntityManager $entityManager
     * @param Slugify $slugify
     */
    public function __construct(EntityManager $entityManager, Slugify $slugify)
    {
        $this->setEntityManager($entityManager);
        $this->setSlugify($slugify);
    }

    /**
     * @param User $user
     * @param string $title
     * @param string $body
     * @return Post
     */
    public function createPost(User $user, string $title = '', string $body = ''): Post
    {
        $post = new Post();
        $post->setTitle($title);
        $post->setBody($body);

        $slug = $this->getSlugify()->slugify($title);

        while ($this->userOwnsPostWithSameSlug($user, $slug)) {
            $slug = $this->incrementSlug($slug);
        }

        $post->setSlug($slug);

        $role = new PostRole();
        $role->setType(PostRole::TYPE_OWNER);
        $role->setPost($post);
        $role->setUser($user);

        $entityManager = $this->getEntityManager();

        $entityManager->persist($post);
        $entityManager->persist($role);
        $entityManager->flush();

        return $post;
    }

    /**
     * @param User $user
     * @param string $slug
     * @return bool
     */
    public function userOwnsPostWithSameSlug(User $user, string $slug): bool
    {
        $entityManager = $this->getEntityManager();

        $queryForPostsOwnedByUserWithSameSlug = $entityManager->getRepository('AppBundle:Post')
            ->createQueryBuilder('post')
            ->join('post.roles', 'role')
            ->where('post.slug = :slug')
            ->andWhere('role.user = :user')
            ->andWhere('role.type = :roleType')
            ->setParameter('slug', $slug)
            ->setParameter('user', $user)
            ->setParameter('roleType', PostRole::TYPE_OWNER)
            ->getQuery()
        ;

        $postsOwnedByUserWithSameSlug = $queryForPostsOwnedByUserWithSameSlug->getResult();

        if (count($postsOwnedByUserWithSameSlug) > 0) {
            return true;
        }

        return false;
    }

    /**
     * @param string $slug
     * @return string $slug
     */
    public function incrementSlug(string $slug): string
    {
        /** @var Stringy $parts */
        $parts = s($slug)->split('-');

        $numberOfParts = count($parts);

        $lastPartIndex = $numberOfParts - 1;

        $lastPart = $parts[$lastPartIndex];

        if (ctype_digit((string) $lastPart)) {
            $oldVersionNumber = (integer) (string) $lastPart;

            $incrementedVersionNumber = $oldVersionNumber + 1;

            $newLastPart = s((string) $incrementedVersionNumber);

            $parts[$lastPartIndex] = $newLastPart;

            $slug = implode('-', $parts);

            return $slug;
        }

        return $slug . '-2';
    }

    /**
     * @return EntityManager
     */
    public function getEntityManager(): EntityManager
    {
        return $this->entityManager;
    }

    /**
     * @param EntityManager $entityManager
     */
    public function setEntityManager(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @return Slugify
     */
    public function getSlugify(): Slugify
    {
        return $this->slugify;
    }

    /**
     * @param Slugify $slugify
     */
    public function setSlugify(Slugify $slugify)
    {
        $this->slugify = $slugify;
    }
}
