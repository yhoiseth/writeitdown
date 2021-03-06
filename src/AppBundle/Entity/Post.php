<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use AppBundle\Entity\PostRole;
use AppBundle\Validator\Constraints as AppAssert;

/**
 * Post
 *
 * @ORM\Table(name="post")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\PostRepository")
 */
class Post extends BaseEntity
{
    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=255)
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="body", type="text", nullable=true)
     */
    private $body;

    /**
     * @var ArrayCollection|PostRole[]
     *
     * @ORM\OneToMany(targetEntity="PostRole", mappedBy="post")
     */
    private $roles;

    /**
     * @var string
     *
     * @ORM\Column(type="string")
     * @AppAssert\UniqueForUser
     */
    private $slug;

    /**
     * @var \DateTime
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $publishedAt;

    public function __construct()
    {
        parent::__construct();
        $this->setRoles(new ArrayCollection());
    }

    /**
     * Set title
     *
     * @param string $title
     *
     * @return Post
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @return string|null
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * @param string $body
     */
    public function setBody(string $body)
    {
        $this->body = $body;
    }

    /**
     * @return Collection|PostRole[]
     */
    public function getRoles(): Collection
    {
        return $this->roles;
    }

    /**
     * @param ArrayCollection|PostRole[] $roles
     */
    public function setRoles(ArrayCollection $roles)
    {
        $this->roles = $roles;
    }

    /**
     * @return string
     */
    public function getSlug(): string
    {
        return $this->slug;
    }

    /**
     * @param string $slug
     */
    public function setSlug(string $slug)
    {
        $this->slug = $slug;
    }

    /**
     * @return \DateTime|null
     */
    public function getPublishedAt()
    {
        return $this->publishedAt;
    }

    /**
     * @param \DateTime $publishedAt
     */
    public function setPublishedAt(\DateTime $publishedAt)
    {
        $this->publishedAt = $publishedAt;
    }

    public function publish()
    {
        $this->setPublishedAt(new \DateTime());
    }

    public function isPublic(): bool
    {
        return $this->getPublishedAt() instanceof \DateTime;
    }

    /**
     * @return User|null
     */
    public function getOwner()
    {
        /** @var PostRole[] $roles */
        $roles = $this->getRoles();

        foreach ($roles as $role) {
            if ($role->getType() === PostRole::TYPE_OWNER) {
                return $role->getUser();
            }
        }

        return null;
    }
}

