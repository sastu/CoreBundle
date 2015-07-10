<?php
namespace Core\Bundle\CoreBundle\Entity;

use Symfony\Component\Security\Core\User\UserInterface;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\EquatableInterface;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Table(name="baseactor");
 * @ORM\Entity(repositoryClass="Core\Bundle\CoreBundle\Entity\Repository\BaseActorRepository")
 * @UniqueEntity(fields="username", message="Username already taken")
 * @UniqueEntity(fields="email", message="Email already taken")
 * @ORM\InheritanceType("JOINED")
 * @ORM\DiscriminatorColumn(name="discr", type="string")
 * @ORM\DiscriminatorMap({"Actor" = "Core\Bundle\CoreBundle\Entity\Actor"})
 *
 */

abstract class BaseActor implements UserInterface, EquatableInterface , \Serializable
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    protected $username;

    /**
     * @ORM\Column(type="string", length=32)
     */
    private $salt;

    /**
     * @ORM\Column(type="string", length=100)
     */
    protected $password;

    /**
     * @ORM\Column(type="string", length=60, unique=true)
     */
    protected $email;

    /**
     * @ORM\Column(name="is_active", type="boolean")
     */
    protected $isActive;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $created;

    /**
     * @ORM\ManyToMany(targetEntity="Role", inversedBy="actors")
     * @ORM\JoinTable(name="role_actorrole")
     */
    protected $roles;

    public function __construct()
    {
        $this->isActive = false;
        $this->salt = md5(uniqid(null, true));
        $this->setCreated(new \DateTime());
        $this->roles = new ArrayCollection();
    }

    /**
     * @ORM\PrePersist
     */
    public function setCreatedValue()
    {
        $this->setCreated(new \DateTime());
    }

    /**
     * @inheritDoc
     */
    public function getId()
    {
        return $this->id;
    }

    public function __toString()
    {
        return $this->username;
    }

    /**
     * @inheritDoc
     */
    public function setUsername($username)
    {
        $this->username = $username;
    }

    /**
     * @inheritDoc
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @inheritDoc
     */
    public function setSalt($salt)
    {
        $this->salt = $salt;
    }

    /**
     * @inheritDoc
     */
    public function getSalt()
    {
        return $this->salt;
    }

    /**
     * @inheritDoc
     */
    public function setPassword($password)
    {
        $this->password = $password;
    }

    /**
     * @inheritDoc
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @inheritDoc
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * @inheritDoc
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @inheritDoc
     */
    public function setIsActive($isActive)
    {
        $this->isActive = $isActive;
    }

    /**
     * @inheritDoc
     */
    public function getIsActive()
    {
        return $this->isActive;
    }

    /**
     * Set created
     *
     * @param datetime $created
     */
    public function setCreated($created)
    {
        $this->created = $created;
    }

    /**
     * Get created
     *
     * @return datetime
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * Add roles
     *
     * @param Core\Bundle\CoreBundle\Entity\Role $roles
     */
    public function addRole(\Core\Bundle\CoreBundle\Entity\Role $roles)
    {
        $this->roles[] = $roles;
    }

    /**
     * Get roles
     *
     * @return Doctrine\Common\Collections\Collection
     */
    public function getRoles()
    {
        return $this->roles->toArray();
    }

    /**
     * Get roles
     *
     * @return Doctrine\Common\Collections\Collection
     */
    public function getRolesEntities()
    {
        return $this->roles;
    }

    public function eraseCredentials()
    {
    }

    public function isEqualTo(UserInterface $user)
    {
        if (!$user instanceof User) {
            return false;
        }

        if ($this->password !== $user->getPassword()) {
            return false;
        }

        if ($this->getSalt() !== $user->getSalt()) {
            return false;
        }

        if ($this->username !== $user->getUsername()) {
            return false;
        }

        return true;
    }

    /**
     * @see \Serializable::serialize()
     */
    public function serialize()
    {
        return serialize(array(
            $this->id,
            $this->username,
            $this->salt,
            $this->password,
        ));
    }

    /**
     * @see \Serializable::unserialize()
     */
    public function unserialize($serialized)
    {
        list (
            $this->id,
            $this->username,
            $this->salt,
            $this->password,
        ) = unserialize($serialized);
    }

}
