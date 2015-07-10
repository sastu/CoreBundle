<?php

namespace Core\Bundle\CoreBundle\Twig;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Core\Bundle\CoreBundle\Entity\Actor;
use Core\Bundle\CoreBundle\Form\ActorType;
use Core\Bundle\CoreBundle\Entity\Optic;
use Core\Bundle\EcommerceBundle\Entity\Product;

/**
 * Class CoreExtension
 */
class CoreExtension extends \Twig_Extension
{

    private $container;

    public function setContainer($container)
    {
        $this->container = $container;
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return array(
            'addSufix' => new \Twig_Function_Method($this, 'addSufix'),
            'changePage' => new \Twig_Function_Method($this, 'changePage'),
            'userForm' => new \Twig_Function_Method($this, 'userForm', array(
                            'is_safe' => array('html')
                        )),
            'get_profile_image' => new \Twig_Function_Method($this, 'getProfileImage'),
            'get_optic_stats' => new \Twig_Function_Method($this, 'getOpticStats'),
            'get_product_stats' => new \Twig_Function_Method($this, 'getProductStats'),
        );
    }
    
  
    public function changePage($array, $value)
    {
       if(isset($array['page'])){
           $array['page'] = $value;
       }
       return $array;
    }
    
    public function addSufix($string)
    {
        $pos = strpos($string, '_pager');

        if ($pos === false) {
            return $string.'_pager';
        } else {
            return $string;
        }
    }
    
    /**
    * Returns the part of a feedID
    *
    * @param string $feedID  ID of the feed to load
    */
    public function userForm($uri)
    {
        
        $entity  = new Actor();

        $form =$this->container->get('form.factory')->create(new ActorType(), $entity, array(
            'action' => $this->container->get('router')->generate('itnube_core_actor_create').'?referer='.$uri,
            'method' => 'POST',
            'attr' => array('class' => 'form-horizontal form-row-seperated')
        ));
         
        $twig = $this->container->get('twig');
        
        $content = $twig->render('CoreBundle:Actor:_form.popup.html.twig', array(
                    'form' => $form->createView()
                    ));

        return $content;
            
    }
    
    /**
    * Returns the image path of user actor
    *
    */
    public function getProfileImage($actor=null)
    {

         /** @var FrontManager $frontManager */
        $adminManager =  $this->container->get('admin_manager');
        $profileImage = $adminManager->getProfileImage($actor);

        return  $profileImage;
    }
    
   /**
    * Returns statistics from optic
    *
    */
    public function getOpticStats(Optic $optic, $start, $end)
    {
         /** @var FrontManager $frontManager */
        $adminManager =  $this->container->get('admin_manager');
        $stats = $adminManager->getOpticStats($optic, $start, $end);

        return  $stats;
        
    }
    
    /**
    * Returns statistics from product
    *
    */
    public function getProductStats(Product $product, $start, $end)
    {
         /** @var FrontManager $frontManager */
        $adminManager =  $this->container->get('admin_manager');
        $stats = $adminManager->getProductStats($product, $start, $end);

        return  $stats;
    }
    
    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'core_extension';
    }
}