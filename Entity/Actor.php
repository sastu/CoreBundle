<?php
namespace Core\Bundle\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Core\Bundle\EcommerceBundle\Entity\Address;
use Core\Bundle\EcommerceBundle\Entity\Transaction;
use Doctrine\Common\Collections\ArrayCollection;


/**
 * @ORM\Entity(repositoryClass="Core\Bundle\CoreBundle\Entity\Repository\ActorRepository")
 * @ORM\Table(name="actor")
 */
class Actor extends BaseActor
{


    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     * @Assert\NotBlank
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="surnames", type="string", length=100, nullable=true)
     */
    private $surnames;
    
    /**
     * @var boolean
     *
     * @ORM\Column(name="newsletter", type="boolean")
     */
    private $newsletter=0;
    
    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="\Core\Bundle\EcommerceBundle\Entity\Address", mappedBy="actor", cascade={"persist", "remove"})
     */
    private $addresses;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="\Core\Bundle\EcommerceBundle\Entity\Transaction", mappedBy="actor", cascade={"remove"})
     */
    private $transactions;

    /**
     * @var Image
     *
     * @ORM\OneToOne(targetEntity="Image", cascade={"persist", "remove"})
     * @ORM\JoinColumn(onDelete="set null")
     */
    private $image;

    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();
        $this->addresses = new ArrayCollection();
        $this->transactions = new ArrayCollection();
    }

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
     * Set name
     *
     * @param string $name
     *
     * @return Actor
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set surnames
     *
     * @param string $surnames
     *
     * @return Actor
     */
    public function setSurnames($surnames)
    {
        $this->surnames = $surnames;

        return $this;
    }

    /**
     * Get surnames
     *
     * @return string
     */
    public function getSurnames()
    {
        return $this->surnames;
    }

    /**
     * Add address
     *
     * @param Address $address
     *
     * @return Actor
     */
    public function addAddress(Address $address)
    {
        $address->setActor($this);
        $this->addresses->add($address);

        return $this;
    }

    /**
     * Remove address
     *
     * @param Address $address
     */
    public function removeAddress(Address $address)
    {
        $this->addresses->removeElement($address);
    }

    /**
     * Get addresses
     *
     * @return ArrayCollection
     */
    public function getAddresses()
    {
        return $this->addresses;
    }

    /**
     * Add order
     *
     * @param Transaction $order
     *
     * @return Actor
     */
    public function addTransaction(Transaction $order)
    {
        $order->setActor($this);
        $this->orders->add($order);

        return $this;
    }

    /**
     * Remove order
     *
     * @param Transaction $order
     */
    public function removeTransaction(Transaction $order)
    {
        $this->orders->removeElement($order);
    }

    /**
     * Get orders
     *
     * @return ArrayCollection
     */
    public function getTransactions()
    {
        return $this->orders;
    }

    /**
     * Get full name
     *
     * @return string
     */
    public function getFullName()
    {
        return $this->name . ' ' . $this->surnames;
    }

     /**
     * Set image
     *
     * @param Image $image
     *
     * @return Category
     */
    public function setImage(Image $image = null)
    {
        $this->image = $image;

        return $this;
    }

    /**
     * Get image
     *
     * @return Image
     */
    public function getImage()
    {
        return $this->image;
    }
     
    /**
     * Set newsletter
     *
     * @param boolean $newsletter
     *
     * @return User
     */
    public function setNewsletter($newsletter)
    {
        $this->newsletter = $newsletter;

        return $this;
    }

    /**
     * Is subscribed to newsletter?
     *
     * @return boolean
     */
    public function getNewsletter()
    {
        return $this->newsletter;
    }
    
    public function isGranted($role)
    {
        return in_array($role, $this->getRoles());
    }
}
