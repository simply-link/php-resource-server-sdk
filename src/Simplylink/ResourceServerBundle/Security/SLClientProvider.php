<?php
/**
 * Created by PhpStorm.
 * User: ronfridman
 * Date: 31/08/2017
 * Time: 8:43
 */

namespace Simplylink\ResourceServerBundle\Security;



use Simplylink\ResourceServerBundle\Utils\DataObjects\SLoAuthAccessTokenIntrospection;
use Simplylink\ResourceServerBundle\Utils\SLoAuthTokenValidator;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;

class SLClientProvider implements UserProviderInterface
{
    public function loadUserByUsername($credentials)
    {
        $token = $credentials['token'];
        $referer = $credentials['referer'];
        $clientIp = $credentials['client_ip'];
        
        
        // make a call to your webservice here
        $tokenValidator = new SLoAuthTokenValidator();
        $tokenData = $tokenValidator->validateToken($token,$clientIp,$referer);
        
        if ($tokenData) {
//            return new SLTokenAuthenticator($tokenData);
            $tokenIntrospection = new SLoAuthAccessTokenIntrospection($tokenData);
            $tokenIntrospection->setTokenString($token);
            if($tokenIntrospection->isActive())
                return $tokenIntrospection;
        }
        
        throw new UsernameNotFoundException(
            sprintf('token "%s" does not exist.', $token)
        );
    }
    
    public function refreshUser(UserInterface $user)
    {
        if (!$user instanceof SLoAuthAccessTokenIntrospection ) {
            throw new UnsupportedUserException(
                sprintf('Instances of "%s" are not supported.', get_class($user))
            );
        }
        
        return $user;
    }
    
    public function supportsClass($class)
    {
        return SLoAuthAccessTokenIntrospection::class === $class;
    }
    
}