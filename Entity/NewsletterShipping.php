<?php
namespace Core\Bundle\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints as Assert;
use Core\Bundle\CoreBundle\Entity\Actor;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity(repositoryClass="Core\Bundle\CoreBundle\Entity\Repository\NewsletterShippingRepository")
 * @ORM\Table(name="newsletter_shipping")
 */
class NewsletterShipping
{
    
    const TYPE_ALL = 'all';
    const TYPE_MANAGER = 'managers';
    const TYPE_INVESTOR = 'investors';
    const TYPE_USER = 'users';
    
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;


    /**
     * @Assert\NotBlank()
     * @ORM\ManyToOne(targetEntity="Core\Bundle\CoreBundle\Entity\Newsletter")
     * @ORM\JoinColumn(name="newsletter_id", referencedColumnName="id", nullable=true, onDelete="CASCADE")
     */
    protected $newsletter;

    /**
     * @Assert\NotBlank()
     * @ORM\Column(type="text")
     */
    protected $type;
        
    /**
     * @var boolean
     *
     * @ORM\Column(name="total_sent", type="integer", nullable=true)
     */
    private $totalSent;
    
    /**
     * @var \DateTime
     *
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(name="created", type="datetime")
     */
    private $created;
 
    
    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }
    
    /**
     * Set newsletter
     *
     * @param entity $newsletter
     */
    public function setNewsletter(Newsletter $newsletter)
    {
        $this->newsletter = $newsletter;
    }

    /**
     * Get newsletter
     *
     * @return string
     */
    public function getNewsletter()
    {
        return $this->newsletter;
    }


    /**
     * Set type
     *
     * @param string $type
     */
    public function setType($type) 
    {
        $this->type = $type;
    }
    
    /**
     * Get type
     *
     * @return string
     */
    public function getType() 
    {
        return $this->type;
    }
    
    /**
     * Set totalSent
     *
     * @param string $totalSent
     *
     * @return Mailer
     */
    public function setTotalSent($totalSent)
    {
        $this->totalSent = $totalSent;

        return $this;
    }

    /**
     * Get totalSent
     *
     * @return string 
     */
    public function getTotalSent()
    {
        return $this->totalSent;
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

}
