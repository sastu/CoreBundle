<?php
namespace Core\Bundle\CoreBundle\DataFixtures\ORM;

use Core\Bundle\CoreBundle\DataFixtures\SqlScriptFixture;
use Core\Bundle\EcommerceBundle\Entity\Feature;
use Core\Bundle\EcommerceBundle\Entity\FeatureValue;
use Core\Bundle\EcommerceBundle\Entity\Category;
use Core\Bundle\CoreBundle\Entity\WebTemplate;
use Core\Bundle\CoreBundle\Entity\Slider;
use Core\Bundle\CoreBundle\Entity\Image;
use Core\Bundle\CoreBundle\Entity\Role;
use Core\Bundle\CoreBundle\Entity\Actor;

class LoadCoreData extends SqlScriptFixture
{
    public function createFixtures()
    {
        $core = $this->container->getParameter('core');
        
        $manager = $this->getManager();
        $factory = $this->get('security.encoder_factory');
        $encoder = $factory->getEncoder(new Actor());

        //Create user roles
        $userRole = new Role();
        $userRole->setName('user');
        $userRole->setRole(Role::USER);
        $manager->persist($userRole);;
        
        $adminRole = new Role();
        $adminRole->setRole(Role::ADMIN);
        $adminRole->setName('admin');
        $manager->persist($adminRole);

        $rootRole = new Role();
        $rootRole->setRole(Role::SUPER_ADMIN);
        $rootRole->setName('root');
        $manager->persist($rootRole);

        //User admin
        $password = 'admin';
        $user = new Actor();
        $user->setUsername('admin');
        $user->setEmail('admin@admin.com');
        $user->addRole($adminRole);
        $encodePassword = $encoder->encodePassword($password, $user->getSalt());
        $user->setPassword($encodePassword);
        $user->setName('Admin 1');
        $user->setSurnames('Surnames Admin');
        
        $manager->flush();


        $this->runSqlScript('Country.sql');
        $this->runSqlScript('State.sql');
       
        //copy profile imges
//        self::recurseCopy(__DIR__.'/images', __DIR__.'/../../../../../../web/uploads/images/');
//      
//        $this->createSliderFixtures();

    }
 
      public function createSliderFixtures()
    {
        $core = $this->container->getParameter('core');
        $server_base_url = $core['server_base_url'];
        //Create Item
        $slider  = new Slider();
//        $slider->setTitle('Proyecto Nº1 ciencia');
//        $slider->setCaption('Quisque venenatis et orci non pretium. Nunc pellentesque suscipit lorem, non volutpat ex mattis id. Vivamus dictum dolor metus. Aliquam erat volutpat.');
        $slider->setActive(true);
        $slider->setOpenInNewWindow(true);
        $slider->setUrl('http://www.google.com');
        $slider->setOrder(0);
        $this->getManager()->persist($slider);
        
//        $slider2  = new Slider();
//        $slider2->setTitle('Proyecto Nº1 biologia');
//        $slider2->setCaption(' Nunc pellentesque suscipit lorem, non volutpat ex mattis id. Vivamus dictum dolor metus. Aliquam erat volutpat. Nunc pellentesque suscipit lorem, non volutpat ex mattis id. Vivamus dictum dolor metus. Aliquam erat volutpat. ');
//        $slider2->setActive(true);
//        $slider2->setOpenInNewWindow(false);
//        $slider2->setUrl($server_base_url.'/quienes-somos');
//        $slider2->setOrder(1);
//        $this->getManager()->persist($slider2);
//        
//        
//        $slider3  = new Slider();
//        $slider3->setTitle('Proyecto Nº2 biologia');
//        $slider3->setCaption(' Nunc pellentesque suscipit lorem, non volutpat ex mattis id. Vivamus dictum dolor metus. Aliquam erat volutpat. Nunc pellentesque suscipit lorem, non volutpat ex mattis id. Vivamus dictum dolor metus. Aliquam erat volutpat. ');
//        $slider3->setActive(true);
//        $slider3->setOpenInNewWindow(false);
//        $slider3->setUrl($server_base_url.'/quienes-somos');
//        $slider3->setOrder(2);
//        $this->getManager()->persist($slider3);
     
        $this->getManager()->flush();
        
        //Brand
        $image = new Image();
        $image->setPath('slider1.png');
        $filename =  __DIR__ . '/../../Resources/public/images/slider1.png' ;
        copy($filename, __DIR__ . '/../../../../../../web/uploads/images/slider1.png' );
        $slider->setImage($image);
        $this->getManager()->persist($image);
        
//        $image2 = new Image();
//        $image2->setPath('slide2.jpg');
//        $filename =  __DIR__ . '/../../Resources/public/images/slide2.jpg';
//        copy($filename, __DIR__ . '/../../../../../../web/uploads/images/slide2.jpg' );
//        $slider2->setImage($image2);
//        $this->getManager()->persist($image2);
//        
//        $image3 = new Image();
//        $image3->setPath('slide4.jpg');
//        $filename =  __DIR__ . '/../../Resources/public/images/slide4.jpg';
//        copy($filename, __DIR__ . '/../../../../../../web/uploads/images/slide4.jpg' );
//        $slider3->setImage($image3);
//        $this->getManager()->persist($image3);
        
        $this->getManager()->flush();
        
        
    }
    
    
    
    public static function createPath($path)
    {
        if (is_dir($path)) return true;
        $prev_path = substr($path, 0, strrpos($path, '/', -2) + 1 );
        $return = self::createPath($prev_path);

        return ($return && is_writable($prev_path)) ? mkdir($path) : false;
    }

    public static function recurseCopy($src,$dst)
    {
        $dir = opendir($src);
        if (!is_dir($dst)) {
            self::createPath($dst);
        }

        while (false !== ( $file = readdir($dir)) ) {
            if (( $file != '.' ) && ( $file != '..' )) {
                if ( is_dir($src . '/' . $file) ) {
                    self::recurseCopy($src . '/' . $file,$dst . '/' . $file);
                } else {
//                    print_r($src . '/' . $file);echo PHP_EOL;
//                    print_r($dst . '/' . $file);
//                    die();
                    @copy($src . '/' . $file,$dst . '/' . $file);
                }
            }
        }
        closedir($dir);
    }

    public static function recurseRemove($directory, $empty=FALSE)
    {
       // if the path has a slash at the end we remove it here
       if (substr($directory,-1) == '/') {
           $directory = substr($directory,0,-1);
       }

      // if the path is not valid or is not a directory ...
       if (!file_exists($directory) || !is_dir($directory)) {
           // ... we return false and exit the function
           return FALSE;

       // ... if the path is not readable
       } elseif (!is_readable($directory)) {
           // ... we return false and exit the function
           return FALSE;

       // ... else if the path is readable
       } else {

           // we open the directory
           $handle = opendir($directory);

           // and scan through the items inside
           while (FALSE !== ($item = readdir($handle))) {
               // if the filepointer is not the current directory
               // or the parent directory
               if ($item != '.' && $item != '..') {
                   // we build the new path to delete
                   $path = $directory.'/'.$item;

                   // if the new path is a directory
                   if (is_dir($path)) {
                       // we call this function with the new path
                       self::recurseRemove($path);

                   // if the new path is a file
                   } else {
                       // we remove the file
                       unlink($path);
                   }
               }
          }
           // close the directory
           closedir($handle);

           // if the option to empty is not set to true
           if ($empty == FALSE) {
               // try to delete the now empty directory
               if (!rmdir($directory)) {
                   // return false if not possible
                   return FALSE;
               }
           }
           // return success
           return TRUE;
       }
    }
    
    public function getOrder()
    {
        return 1; // the order in which fixtures will be loaded
    }
}
