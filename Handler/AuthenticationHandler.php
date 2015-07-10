<?php
namespace Core\Bundle\CoreBundle\Handler;

use Symfony\Component\Security\Http\Logout\LogoutSuccessHandlerInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationFailureHandlerInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
//
class AuthenticationHandler implements AuthenticationSuccessHandlerInterface, LogoutSuccessHandlerInterface
{

    protected $container;

    public function __construct($container)
    {
        $this->container = $container;
    }

//    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
//    {
//
//    }

    public function onLogoutSuccess(Request $request)
    {

    }

    /**
     * (non-PHPdoc)
     * @see Symfony\Component\Security\Http\Authentication.AuthenticationSuccessHandlerInterface::onAuthenticationSuccess()
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token)
    {
        
        if ($this->hasRole('ROLE_ADMIN', $token->getUser())) {
            return new RedirectResponse($this->container->get('router')->generate('core_admin_default_index'));
        } else {
            $referer = $this->getRefererPath($request);
            if (preg_match('/(^\/identification)/', $referer)) {
                preg_match_all('/^\/identification\/(.*)/', $referer, $matches);
                if(!isset($matches[1]))  {
                    throw $this->createNotFoundException('Unable to find Project entity.');
                }
                $slug = $matches[1][0];
                return new RedirectResponse($this->container->get('router')->generate('core_front_checkout_investinfo', array('slug' => $slug)));
            }
            return new RedirectResponse($this->container->get('router')->generate('index'));
        }
    }

    public function getRefererPath(Request $request=null)
    {
        $referer = $request->headers->get('referer');

        $baseUrl = $request->getSchemeAndHttpHost();

        $lastPath = substr($referer, strpos($referer, $baseUrl) + strlen($baseUrl));

        return $lastPath;
    }
    
    public function hasRole($searchRole, $user)
    {
        
        foreach ($user->getRoles() as $role) {
            if($searchRole == $role) return true;
        }

        return false;
    }
    
}
