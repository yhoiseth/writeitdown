<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use FOS\UserBundle\Model\User as BaseUser;
use AppBundle\Entity\PostRole;

/**
 * User
 *
 * @ORM\Table(name="`user`")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\UserRepository")
 */
class User extends BaseUser
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    public function __construct()
    {
        parent::__construct();
        $this->setPostRoles(new ArrayCollection());
    }

    /**
     * @var ArrayCollection|PostRole[]
     *
     * @ORM\OneToMany(targetEntity="PostRole", mappedBy="user")
     */
    private $postRoles;

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return ArrayCollection|PostRole[]
     */
    public function getPostRoles(): ArrayCollection
    {
        return $this->postRoles;
    }

    /**
     * @param ArrayCollection|PostRole[] $postRoles
     */
    public function setPostRoles(ArrayCollection $postRoles)
    {
        $this->postRoles = $postRoles;
    }
}
