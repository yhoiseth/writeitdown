<?php

namespace AppBundle\Validator\Constraints;


use Symfony\Component\Validator\Constraint;

/**
 * Class UniqueForUser
 * @package AppBundle
 * @Annotation
 */
class UniqueForUser extends Constraint
{
    /**
     * @var string
     */
    public $message = 'You have to choose a unique slug.';
}
