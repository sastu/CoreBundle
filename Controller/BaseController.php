<?php
namespace Core\Bundle\CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class BaseController  extends Controller
{
    protected function paginator($query, $page, $params=array(), $limit=10) 
    {
        
        $manager = $this->getDoctrine()->getManager();
        $q = $manager->createQuery($query);
       
        if(count($params)>0){
             $q->setParameters($params);
        }
        
        $entities = $q->getResult();
       
        
        if(count($entities)>1){
            $total = count($entities);
            $offset = $page * $limit - $limit;
            $pages = $total / $limit;
            $paginator = array();
            $paginator['total'] = $total;
            $paginator['pages'] = array();
            for ($index = 1; $index < $pages; $index++) {
                $paginator['pages'][] = $index;
            }
            $q = $manager->createQuery($query);
            
           
            if(count($params)>0){
                 $q->setParameters($params);
            }
            $entities2 = $q->setMaxResults($limit)
                ->setFirstResult($offset)
                ->getResult()
            ;

            return array($entities2, $paginator );
       }
       
       $paginator = array();
       $paginator['total'] = 1;
       $paginator['pages'] = array();

       return array($entities, $paginator);
    }
    
    protected function checkAccess($projectId) 
    {
        $user = $this->container->get('security.context')->getToken()->getUser();
        if ($user->isGranted('ROLE_MANAGER')) {
            $projectIds = $this->get('admin_manager')->getProjectIds(); 
            if(!in_array($projectId, $projectIds))  throw new AccessDeniedHttpException();
        }
    }
        
}
