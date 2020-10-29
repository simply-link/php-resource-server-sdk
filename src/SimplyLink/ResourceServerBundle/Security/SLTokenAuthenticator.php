<?php
/**
 * Created by PhpStorm.
 * User: ronfridman
 * Date: 31/08/2017
 * Time: 8:21
 */


namespace SimplyLink\ResourceServerBundle\Security;


use SimplyLink\ResourceServerBundle\Utils\DataObjects\SLoAuthAccessTokenIntrospection;
use SimplyLink\UtilsBundle\Utils\GenericDataManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class SLTokenAuthenticator extends AbstractGuardAuthenticator
{
    /**
     * Called on every request. Return whatever credentials you want to
     * be passed to getUser(). Returning null will cause this authenticator
     * to be skipped.
     */
    public function getCredentials(Request $request)
    {
        if (!$token = $request->headers->get('Authorization')) {
            // No token?
            return null;
        }
        
        // What you return here will be passed to getUser() as $credentials
        return array(
            'token' => $token,
            'referer' => $request->server->get('HTTP_REFERER'),
            'client_ip' => $request->getClientIp(),
        );
    }
    
    /**
     *
     * If getCredentials() returns a non-null value, then this method is called and its return value is passed here as the $credentials argument.
     * Your job is to return an object that implements UserInterface. If you do, then checkCredentials() will be called. If you return null (or throw an AuthenticationException) authentication will fail.
     *
     * @param mixed $credentials
     * @param UserProviderInterface $userProvider
     * @return UserInterface|void
     */
    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        if (null === $credentials) {
            return;
        }
    
    
        $userProvider = new SLClientProvider();
        $tokenIntrospection = $userProvider->loadUserByUsername($credentials);
        
        return $tokenIntrospection;
        // if a SLoAuthAccessTokenIntrospection object, checkCredentials() is called
    }
    
    public function checkCredentials($credentials, UserInterface $user)
    {
        // check credentials - e.g. make sure the password is valid
        // no credential check is needed in this case
        
        // return true to cause authentication success
        return count($user->getRoles()) > 0;
    }
    
    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
    {
        // on success, let the request continue
        return null;
    }
    
    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        $data = array(
            'message' => strtr($exception->getMessageKey(), $exception->getMessageData())
            
            // or to translate this message
            // $this->translator->trans($exception->getMessageKey(), $exception->getMessageData())
        );
        
        return new JsonResponse($data, Response::HTTP_FORBIDDEN);
    }
    
    /**
     * Called when authentication is needed, but it's not sent
     */
    public function start(Request $request, AuthenticationException $authException = null)
    {
        $data = array(
            // you might translate this message
            'message' => 'Authentication Required'
        );
        
        return new JsonResponse($data, Response::HTTP_UNAUTHORIZED);
    }
    
    public function supportsRememberMe()
    {
        return false;
    }
}