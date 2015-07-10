<?php

namespace Core\Bundle\CoreBundle\Model;

use Symfony\Component\Validator\Constraints as Assert;

class Registration
{
    /**
     * @Assert\Type(type="\Core\Bundle\CoreBundle\Entity\Actor")
     * @Assert\Valid()
     */
    protected $actor;

    /**
     * @Assert\NotBlank()
     * @Assert\True()
     */
    protected $termsAccepted;
   

    public function setActor(\Core\Bundle\CoreBundle\Entity\Actor $actor)
    {
        $this->actor = $actor;
    }

    public function getActor()
    {
        return $this->actor;
    }

    public function getTermsAccepted()
    {
        return $this->termsAccepted;
    }

    public function setTermsAccepted($termsAccepted)
    {
        $this->termsAccepted = (Boolean) $termsAccepted;
    }

}
