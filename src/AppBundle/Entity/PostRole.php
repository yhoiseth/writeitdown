<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use AppBundle\Entity\Post;
use AppBundle\Entity\User;

/**
 * PostRole
 *
 * @ORM\Table(name="post_role")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\PostRoleRepository")
 */
class PostRole extends BaseEntity
{
    const TYPE_OWNER = 'owner';

    /**
     * @ORM\ManyToOne(targetEntity="Post", inversedBy="roles")
     * @ORM\JoinColumn(name="post_id", referencedColumnName="id")
     * @var Post
     */
    private $post;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="postRoles")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     * @var User
     */
    private $user;

    /**
     * @var string
     *
     * @ORM\Column(name="type", type="string", length=255)
     */
    private $type;

    /**
     * @return \AppBundle\Entity\Post
     */
    public function getPost(): \AppBundle\Entity\Post
    {
        return $this->post;
    }

    /**
     * @param \AppBundle\Entity\Post $post
     */
    public function setPost(\AppBundle\Entity\Post $post)
    {
        $this->post = $post;
    }

    /**
     * @return \AppBundle\Entity\User
     */
    public function getUser(): \AppBundle\Entity\User
    {
        return $this->user;
    }

    /**
     * @param \AppBundle\Entity\User $user
     */
    public function setUser(\AppBundle\Entity\User $user)
    {
        $this->user = $user;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @param string $type
     */
    public function setType(string $type)
    {
        $this->type = $type;
    }
}

