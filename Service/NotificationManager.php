<?php

namespace Core\Bundle\CoreBundle\Service;

use Core\Bundle\CoreBundle\Entity\Notification;
use Core\Bundle\CoreBundle\Entity\Actor;
use Core\Bundle\CoreBundle\Entity\Project;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Description of NotificationManager
 *
 * @author sebastian
 */
class NotificationManager 
{

    protected $container = null;

    public function setContainer(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function getContainer()
    {
        return $this->container;
    }
    
    public function getManager()
    {
        return $this->container->get('doctrine')->getManager();
    }
    
    public function setNotification(Actor $actor, Actor $actorDest, Project $project, $type)
    {
        $em = $this->getManager();
        $notif = new Notification();
        $notif->setActor($actor);
        $notif->setActorDest($actorDest);
        $notif->setType($type);
        $notif->setProject($project);
        $em->persist($notif);
        $em->flush();
        
        return $notif;
    }
    
    public function getNotification($actorDest, $type, $count=false, Project $project=null)
    {
        $em = $this->getManager();
        if($type == 'all'){
            if(!is_null($project)){
                $notifications = $em->getRepository('CoreBundle:Notification')->findBy(array('actorDest' => $actorDest, 'isActive' => true, 'project' => $project));
            }else{
                $notifications = $em->getRepository('CoreBundle:Notification')->findBy(array('actorDest' => $actorDest, 'isActive' => true));
            }
        }else{
            if(!is_null($project)){
                $notifications = $em->getRepository('CoreBundle:Notification')->findBy(array('actorDest' => $actorDest, 'type' => $type, 'isActive' => true, 'project' => $project));
            }else{
                $notifications = $em->getRepository('CoreBundle:Notification')->findBy(array('actorDest' => $actorDest, 'type' => $type, 'isActive' => true));
            }
        }
        if($count) return count($notifications);
        return $notifications;
    }
    
    public function disableNotification($actorDest, $type, $project=null, $actor=null)
    {
        $em = $this->getManager();
        if(!is_null($actor)){
            $notifications = $em->getRepository('CoreBundle:Notification')->findBy(array('actor' => $actor, 'actorDest' => $actorDest, 'type' => $type, 'project' => $project));
        }else{
            $notifications = $em->getRepository('CoreBundle:Notification')->findBy(array('actorDest' => $actorDest, 'type' => $type, 'project' => $project));
        }
        foreach ($notifications as $notification) {
            $notification->setIsActive(false);
            $em->flush();
        }
    }
    
    
    
    public function addNotification(Actor $actor, Project $project, $type)
    {
        $em = $this->getManager();
        $actorIds = $this->getActorRelatedProject($project);
         
        foreach ($actorIds as $actorId) {
               $actorDest = $em->getRepository('CoreBundle:Actor')->find($actorId);
               $this->setNotification($actor, $actorDest, $project, $type);
            
        } 
    }
    
    private function getActorRelatedProject($project) 
    {
        return $this->container->get('front_manager')->getActorRelatedProject($project);
    }
}
