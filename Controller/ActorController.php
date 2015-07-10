<?php
namespace Core\Bundle\CoreBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Core\Bundle\FrontBundle\Form\RegistrationType;
use Core\Bundle\FrontBundle\Form\Model\Registration;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Core\Bundle\CoreBundle\Form\ActorType;
use Core\Bundle\CoreBundle\Form\ActorEditType;
use Core\Bundle\CoreBundle\Entity\Actor;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Core\Bundle\CoreBundle\Entity\Image;


class ActorController  extends Controller
{
    /**
     * Lists all Actor entities.
     *
     * @return array
     *
     * @Route("/admin/actor")
     * @Method("GET")
     * @Template("CoreBundle:Actor:index.html.twig")
     */
    public function indexAction()
    {
        return array();
    }

    /**
     * Returns a list of Actor entities in JSON format.
     *
     * @return JsonResponse
     *
     * @Route("/admin/actor/list.{_format}", requirements={ "_format" = "json" }, defaults={ "_format" = "json" })
     * @Method("GET")
     */
    public function listJsonAction()
    {
        $em = $this->getDoctrine()->getManager();

        /** @var \Kitchenit\AdminBundle\Services\DataTables\JsonList $jsonList */
        $jsonList = $this->get('json_list');
        $jsonList->setRepository($em->getRepository('CoreBundle:Actor'));
        $response = $jsonList->get();

        return new JsonResponse($response);
    }

    /**
     * Creates a new Team entity.
     *
     * @param Request $request The request
     *
     * @return array|RedirectResponse
     *
     * @Route("/admin/actor/")
     * @Method("POST")
     * @Template("CoreBundle:Actor:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity  = new Actor();
        $form = $this->createForm(new ActorType(), $entity);
         
        $form->bind($request);

        if ($form->isValid()) 
        {
            $em = $this->getDoctrine()->getManager();

            //crypt password
            $factory = $this->get('security.encoder_factory');
            $encoder = $factory->getEncoder(new Actor());
            $encodePassword = $encoder->encodePassword($entity->getPassword(), $entity->getSalt());
            $entity->setPassword($encodePassword);
            $image = $form->getNormData()->getImage();
            $entity->setImage(null);
            $em->persist($entity);
            $em->flush();


            if ($image instanceof UploadedFile) {
                $imagePath = $this->get('admin_manager')->uploadProfileImage($image, $entity);
                $img = new Image();
                $img->setPath($imagePath);
                $em->persist($img);
                $entity->setImage($img);
                $em->flush();
            }

            
            $em->persist($entity);
            $em->flush();

            $this->get('session')->getFlashBag()->add('success', 'actor.created');

            //if come from popup
            if($request->query->get('referer') != '') {
                
                $url = null;
                $x = 1;
                foreach ($request->query->all() as $key => $value) {
                        if($key == 'referer') $url .= $value.'?';
                        else $url .= $key.'='.$value;
                        if(count($request->query->all()) != $x && $request->query->all() != 1) $url .= '&';
                        $x++;
                }
                return $this->redirect($url.'&addUser='.$entity->getId());
            }
            
            return $this->redirect($this->generateUrl('optisoop_core_actor_show', array('id' => $entity->getId())));
        }else{
//             die('invalid');
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

  
    
    /**
     * Displays a form to create a new Team entity.
     *
     * @return array
     *
     * @Route("/admin/actor/new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new Actor();
        $form = $this->createForm(new ActorType(), $entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }
    
    /**
     * Finds and displays a Actor entity.
     *
     * @param int $id The entity id
     *
     * @throws NotFoundHttpException
     * @return array
     *
     * @Route("/admin/actor/{id}")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        /** @var Actor $entity */
        $entity = $em->getRepository('CoreBundle:Actor')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Actor entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing Actor entity.
     *
     * @param int $id The entity id
     *
     * @throws NotFoundHttpException
     * @return array
     *
     * @Route("/admin/actor/{id}/edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        /** @var Actor $entity */
        $entity = $em->getRepository('CoreBundle:Actor')->find($id);
        $entity_image = clone $entity;
        $entity_image->setImage(null);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Actor entity.');
        }

        $editForm = $this->createForm(new ActorEditType(), $entity_image);
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Edits an existing Actor entity.
     *
     * @param Request $request The request
     * @param int     $id      The entity id
     *
     * @throws NotFoundHttpException
     * @return array|RedirectResponse
     *
     * @Route("/admin/actor/{id}")
     * @Method("PUT")
     * @Template("CoreBundle:Actor:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        /** @var Actor $entity */
        $entity = $em->getRepository('CoreBundle:Actor')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Actor entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createForm(new ActorEditType(), $entity);
        $editForm->bind($request);

        if ($editForm->isValid()) {
            $em->persist($entity);
            $em->flush();

            $this->get('session')->getFlashBag()->add('success', 'user.edited');

            return $this->redirect($this->generateUrl('optisoop_core_actor_show', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Deletes a Actor entity.
     *
     * @param Request $request The request
     * @param int     $id      The entity id
     *
     * @throws NotFoundHttpException
     * @return RedirectResponse
     *
     * @Route("/admin/actor/{id}")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            /** @var Actor $entity */
            $entity = $em->getRepository('CoreBundle:Actor')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Actor entity.');
            }

            $em->remove($entity);
            $em->flush();

            $this->get('session')->getFlashBag()->add('info', 'user.deleted');
        }

        return $this->redirect($this->generateUrl('optisoop_core_actor_index'));
    }

    /**
     * Creates a form to delete a Actor entity by id.
     *
     * @param int $id The entity id
     *
     * @return Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder(array('id' => $id))
            ->add('id', 'hidden')
            ->getForm();
    }
    /**
     *
     * REGISTRATION
     *
     */

    /**
     * Create new Actor entity.
     *
     * @Route("/register", name="register")
     * @Method("GET")
     * @Template("FrontBundle:Registration:register.html.twig")
     */
    public function registerAction()
    {
        if ($this->get('security.context')->isGranted('IS_AUTHENTICATED_FULLY') ) {
                return $this->redirect($this->get('router')->generate('index'));
        }

        $form = $this->createForm(new RegistrationType(), new Registration());

        return array('form' => $form->createView());
    }
    
    public function getRefererPath(Request $request=null)
    {
        $referer = $request->headers->get('referer');

        $baseUrl = $request->getSchemeAndHttpHost();

        $lastPath = substr($referer, strpos($referer, $baseUrl) + strlen($baseUrl));

        return $lastPath;
    }

    /**
     * Creates
     *
     * @Route("/register/", name="create_actor")
     * @Method("POST")
     * @Template("FrontBundle:Registration:register.html.twig")
     */
    public function createActorAction()
    {

        $em = $this->getDoctrine()->getManager();

        $form = $this->createForm(new RegistrationType(), new Registration());

        $referer = $this->getRefererPath($this->getRequest());
//        $parameters = $this->get('router')->match($referer);
//    
//        print_r($parameters);die();
        $form->bind($this->getRequest());

        if ($form->isValid()) {
            $registration = $form->getData();

            //Encode pass
            $factory = $this->get('security.encoder_factory');
            $encoder = $factory->getEncoder($registration->getActor());
            $password = $encoder->encodePassword($registration->getActor()->getPassword(), $registration->getActor()->getSalt());
            $registration->getActor()->setPassword($password);

            //Add ROLE
            $role = $em->getRepository('CoreBundle:Role')->findOneBy(array('role' => 'ROLE_USER'));
            $registration->getActor()->addRole($role);
            
            $em->persist($registration->getActor());
            $em->flush();

            //Login
            $username = $registration->getActor()->getName();
            $password = $registration->getActor()->getPassword();
            $email = $registration->getActor()->getEmail();

            //Automatic login
            $token = new UsernamePasswordToken(
               $registration->getActor(),
               $password,
               'secured_area',
               $registration->getActor()->getRoles()
               );

            $this->get('security.context')->setToken($token);

            $this->get('core.mailer')->sendRegisteredEmailMessage($registration->getActor());

            if ($referer == '/identification') {
                return $this->redirect($this->generateUrl('optisoop_ecommerce_checkout_deliveryinfo'));
            }
   
           
            return $this->redirect($this->generateUrl('optisoop_front_profile_index').'?transactions=true');


        }else{
//            $string = var_export($this->getErrorMessages($form), true);
//            print_r($string);die();
        }

        return array('form' => $form->createView());

   }

   private function getErrorMessages(\Symfony\Component\Form\Form $form) {
        $errors = array();

        foreach ($form->getErrors() as $key => $error) {
            if ($form->isRoot()) {
                $errors['#'][] = $error->getMessage();
            } else {
                $errors[] = $error->getMessage();
            }
        }

        foreach ($form->all() as $child) {
            if (!$child->isValid()) {
                $errors[$child->getName()] = $this->getErrorMessages($child);
            }
        }

        return $errors;
    }
   /**
     * Validate registration.
     *
     * @Route("/validate/{email}/{hash}", name="register_validate")
     * @Method("GET")
     * @Template("CoreBundle:Registration:validate.html.twig")
     */
    public function validateAction($email, $hash)
    {

        $em = $this->getDoctrine()
                    ->getManager();

        $user = $em->getRepository('CoreBundle:Actor')->findOneByEmail($email);

        if (!$user) {
            throw $this->createNotFoundException('Unable to find actor.');
        }

        if (($user instanceof Actor) && ($user->getSalt() == $hash)) {
            //check validate time
             $now = new \DateTime();
             $diff = $now->getTimestamp() - $user->getCreated()->getTimestamp();
             $core = $this->container->getParameter('core');
             if ($diff < $core['validate_time']) {
                 $user->setIsActive(true);
                 $em->persist($user);
                 $em->flush();

                 $this->get('core.mailer')->sendValidateEmailMessage($user);

             }
        }

    }
  

}
