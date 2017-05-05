<?php

namespace AppBundle\Validator\Constraints;


use AppBundle\Entity\Post;
use AppBundle\Service\PostService;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class UniqueForUserValidator extends ConstraintValidator
{
    /**
     * @var Container
     */
    private $container;

    public function __construct(Container $container)
    {
        $this->setContainer($container);
    }

    /**
     * Checks if the passed value is valid.
     *
     * @param mixed $value The value that should be validated
     * @param Constraint $constraint The constraint for the validation
     */
    public function validate($value, Constraint $constraint)
    {
        /** @var TokenStorage $tokenStorage */
        $tokenStorage = $this->get('security.token_storage');
        $user = $tokenStorage->getToken()->getUser();

        /** @var PostService $postService */
        $postService = $this->get('app.post_service');

        /** @var Post $post */
        $post = $this->context->getObject();

        $userOwnsPostWithSameSlug = $postService->userOwnsPostWithSameSlug($user, $value, $post);

        if ($userOwnsPostWithSameSlug) {
            $this->context
                ->buildViolation($constraint->message)
                ->addViolation()
            ;
        }
    }

    /**
     * @param string $id
     * @return object
     */
    public function get(string $id)
    {
        return $this->getContainer()->get($id);
    }

    /**
     * @return Container
     */
    public function getContainer(): Container
    {
        return $this->container;
    }

    /**
     * @param Container $container
     */
    public function setContainer(Container $container)
    {
        $this->container = $container;
    }
}