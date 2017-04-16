<?php

namespace AppBundle\Security;


use AppBundle\Entity\Post;
use AppBundle\Entity\PostRole;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class PostVoter extends Voter
{
    const EDIT = 'edit';
    const SHOW = 'show';


    /**
     * Determines if the attribute and subject are supported by this voter.
     *
     * @param string $attribute An attribute
     * @param mixed $subject The subject to secure, e.g. an object the user wants to access or any other PHP type
     *
     * @return bool True if the attribute and subject are supported, false otherwise
     */
    protected function supports($attribute, $subject)
    {
        $supportedAttributes = [
            self::EDIT,
            self::SHOW,
        ];

        if (!in_array($attribute, $supportedAttributes)) {
            return false;
        }

        if (!$subject instanceof Post) {
            return false;
        }

        return true;
    }

    /**
     * Perform a single access check operation on a given attribute, subject and token.
     * It is safe to assume that $attribute and $subject already passed the "supports()" method check.
     *
     * @param string $attribute
     * @param mixed $subject
     * @param TokenInterface $token
     *
     * @return bool
     */
    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        $user = $token->getUser();

        if (!$user instanceof $user) {
            return false;
        }

        /** @var Post $post */
        $post = $subject;

        $roles = $post->getRoles();

        foreach ($roles as $role) {
            if ($role->getUser() !== $user) {
                continue;
            }

            if ($role->getType() === PostRole::TYPE_OWNER) {
                return true;
            }
        }

        return false;
    }
}