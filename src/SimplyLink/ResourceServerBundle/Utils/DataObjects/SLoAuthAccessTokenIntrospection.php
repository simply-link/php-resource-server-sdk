<?php
/**
 * Created by PhpStorm.
 * User: ronfridman
 * Date: 31/08/2017
 * Time: 8:30
 */

namespace SimplyLink\ResourceServerBundle\Utils\DataObjects;

use SimplyLink\AuthSDKBundle\Model\SLoAuthAccessToken;
use SimplyLink\UtilsBundle\Utils\GenericDataManager;
use Symfony\Component\Security\Core\User\UserInterface;


/**
 * Class SLoAuthAccessTokenIntrospection
 * @see For more information: https://tools.ietf.org/html/rfc7662
 * @see Jason Web Tokens: https://tools.ietf.org/html/rfc7519
 * @package SimplyLink\AuthSDKBundle\Utils\DataObjects
 */
class SLoAuthAccessTokenIntrospection implements UserInterface
{
    
    
    /**
     * @var bool
     * @JMS\SerializedName("active")
     *
     * REQUIRED.  Boolean indicator of whether or not the presented token
     * is currently active.  The specifics of a token's "active" state
     * will vary depending on the implementation of the authorization
     * server and the information it keeps about its tokens, but a "true"
     * value return for the "active" property will generally indicate
     * that a given token has been issued by this authorization server,
     * has not been revoked by the resource owner, and is within its
     * given time window of validity (e.g., after its issuance time and
     * before its expiration time).
     */
    private $active = false;
    
    /**
     * @var string
     * @JMS\SerializedName("scope")
     *
     * A JSON string containing a space-separated list of  scopes associated with this token.
     * OPTIONAL
     *
     */
    private $scope;
    
    /**
     * @var string
     * @JMS\SerializedName("client_id")
     *
     * Client identifier for the OAuth 2.0 client that requested this token.
     * OPTIONAL
     */
    private $clientPublicId;
    
    /**
     * @var int
     * @JMS\Exclude()
     *
     * Client ID number for the OAuth 2.0 client that requested this token.
     * OPTIONAL
     */
    private $clientId;
    
    /**
     * @var string
     * @JMS\SerializedName("username")
     *
     * Human-readable identifier for the resource owner who authorized this token.
     * OPTIONAL
     */
    private $username;
    
    /**
     * @var string
     * @JMS\SerializedName("token_type")
     *
     * Type of the token as defined in Section 5.1 of OAuth 2.0 [RFC6749]
     * Values: "Access Token" or "Refresh Token"
     * OPTIONAL
     */
    private $tokenType;
    
    
    /**
     * @var int
     * @JMS\SerializedName("exp")
     *
     * "exp" (Expiration Time) Claim
     *
     * The "exp" (expiration time) claim identifies the expiration time on
     * or after which the JWT MUST NOT be accepted for processing.  The
     * processing of the "exp" claim requires that the current date/time
     * MUST be before the expiration date/time listed in the "exp" claim.
     * Implementers MAY provide for some small leeway, usually no more than
     * a few minutes, to account for clock skew.  Its value MUST be a number
     * containing a NumericDate value.
     * OPTIONAL
     */
    private $expirationTime;
    
    
    
    /**
     * @var int
     * @JMS\SerializedName("iat")
     *
     * "iat" (Issued At) Claim
     *
     * The "iat" (issued at) claim identifies the time at which the JWT was
     * issued.  This claim can be used to determine the age of the JWT.  Its
     * value MUST be a number containing a NumericDate value.
     * OPTIONAL
     */
    private $issuedAt;
    
    
    
    /**
     * @var int
     * @JMS\SerializedName("nbf")
     *
     * "nbf" (Not Before) Claim
     *
     * The "nbf" (not before) claim identifies the time before which the JWT
     * MUST NOT be accepted for processing.  The processing of the "nbf"
     * claim requires that the current date/time MUST be after or equal to
     * the not-before date/time listed in the "nbf" claim.  Implementers MAY
     * provide for some small leeway, usually no more than a few minutes, to
     * account for clock skew.  Its value MUST be a number containing a
     * NumericDate value.
     * OPTIONAL
     */
    private $notBefore;
    
    
    /**
     * @var string
     * @JMS\SerializedName("sub")
     *
     * "sub" (Subject) Claim
     *
     * The "sub" (subject) claim identifies the principal that is the
     * subject of the JWT.  The claims in a JWT are normally statements
     * about the subject.  The subject value MUST either be scoped to be
     * locally unique in the context of the issuer or be globally unique.
     * The processing of this claim is generally application specific.  The
     * "sub" value is a case-sensitive string containing a StringOrURI
     * value.
     * OPTIONAL
     */
    private $subject;
    
    
    
    /**
     * @var string
     * @JMS\SerializedName("aud")
     *
     * "aud" (Audience) Claim
     *
     * The "aud" (audience) claim identifies the recipients that the JWT is
     * intended for.  Each principal intended to process the JWT MUST
     * identify itself with a value in the audience claim.  If the principal
     * processing the claim does not identify itself with a value in the
     * "aud" claim when this claim is present, then the JWT MUST be
     * rejected.  In the general case, the "aud" value is an array of case-
     * sensitive strings, each containing a StringOrURI value.  In the
     * special case when the JWT has one audience, the "aud" value MAY be a
     * single case-sensitive string containing a StringOrURI value.  The
     * interpretation of audience values is generally application specific.
     * OPTIONAL
     */
    private $audience;
    
    /**
     * @var string
     * @JMS\SerializedName("iss")
     *
     * "iss" (Issuer) Claim
     *
     * The "iss" (issuer) claim identifies the principal that issued the
     * JWT.  The processing of this claim is generally application specific.
     * The "iss" value is a case-sensitive string containing a StringOrURI
     * value.
     * OPTIONAL
     */
    private $issuer;
    
    
    
    /**
     * @var string
     * @JMS\SerializedName("jti")
     *
     * "jti" (JWT ID) Claim
     *
     * The "jti" (JWT ID) claim provides a unique identifier for the JWT.
     * The identifier value MUST be assigned in a manner that ensures that
     * there is a negligible probability that the same value will be
     * accidentally assigned to a different data object; if the application
     * uses multiple issuers, collisions MUST be prevented among values
     * produced by different issuers as well.  The "jti" claim can be used
     * to prevent the JWT from being replayed.  The "jti" value is a case-
     * sensitive string.
     * OPTIONAL
     */
    private $JWTId;
    
    
    
    
    /**
     * @var string
     *
     * The token string from validation
     */
    private $tokenString;
    
    
    
    /**
     * SLoAuthAccessTokenIntrospection constructor.
     *
     * @param array $tokenData
     */
    public function __construct(array $tokenData)
    {
        $this->active = GenericDataManager::getArrayValueForKey($tokenData,'active');
        $this->scope = GenericDataManager::getArrayValueForKey($tokenData,'scope');
        $this->clientPublicId = GenericDataManager::getArrayValueForKey($tokenData,'client_id');
        
        if($this->clientPublicId)
        {
            $parts = explode("_",$this->clientPublicId);
            if($parts && count($parts) > 0)
                $this->clientId = $parts[0];
        }
        
        
        $this->username = GenericDataManager::getArrayValueForKey($tokenData,'username');
        $this->tokenType = GenericDataManager::getArrayValueForKey($tokenData,'token_type');
        $this->expirationTime = GenericDataManager::getArrayValueForKey($tokenData,'exp');
        $this->issuedAt = GenericDataManager::getArrayValueForKey($tokenData,'iat');
        $this->notBefore = GenericDataManager::getArrayValueForKey($tokenData,'nbf');
        $this->subject = GenericDataManager::getArrayValueForKey($tokenData,'sub');
        $this->audience = GenericDataManager::getArrayValueForKey($tokenData,'aud');
        $this->issuer = GenericDataManager::getArrayValueForKey($tokenData,'iss');
        $this->JWTId = GenericDataManager::getArrayValueForKey($tokenData,'jti');
    }
    
    /**
     * @return bool
     */
    public function isActive()
    {
        return $this->active;
    }
    
    /**
     * @return string
     */
    public function getScope()
    {
        return $this->scope;
    }
    
    /**
     * @return string
     */
    public function getClientId()
    {
        return $this->clientId;
    }
    
    /**
     * @return string
     */
    public function getClientPublicId()
    {
        return $this->clientPublicId;
    }
    
    /**
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }
    
    /**
     * @return string
     */
    public function getTokenType()
    {
        return $this->tokenType;
    }
    
    /**
     * @return int
     */
    public function getExpirationTime()
    {
        return $this->expirationTime;
    }
    
    /**
     * @return int
     */
    public function getIssuedAt()
    {
        return $this->issuedAt;
    }
    
    /**
     * @return int
     */
    public function getNotBefore()
    {
        return $this->notBefore;
    }
    
    /**
     * @return string
     */
    public function getSubject()
    {
        return $this->subject;
    }
    
    /**
     * @return string
     */
    public function getAudience()
    {
        return $this->audience;
    }
    
    /**
     * @return string
     */
    public function getIssuer()
    {
        return $this->issuer;
    }
    
    /**
     * @return string
     */
    public function getJWTId()
    {
        return $this->JWTId;
    }
    
    
    
    
    /**
     * @inheritDoc
     */
    public function getRoles()
    {
        if($this->active)
        {
            $roles = ["ROLE_USER","ROLE_CLIENT"];
            
            if($this->getScope())
            {
                $scopes = explode(" ",$this->getScope());
                foreach ($scopes as $scope)
                {
                    $scopeName = 'ROLE_SCOPE_' . strtoupper(str_replace('.','_',$scope));
                    $roles[] = $scopeName;
                }
                
            }
            
            return $roles;
        }
        return [];
    }
    
    /**
     * @inheritDoc
     */
    public function getPassword()
    {
        return uniqid();
    }
    
    /**
     * @inheritDoc
     */
    public function getSalt()
    {
        // TODO: Implement getSalt() method.
    }
    
    /**
     * @inheritDoc
     */
    public function eraseCredentials()
    {
        // TODO: Implement eraseCredentials() method.
    }
    
    /**
     * @return string
     */
    public function getTokenString()
    {
        return $this->tokenString;
    }
    
    /**
     * @param string $tokenString
     * @return SLoAuthAccessTokenIntrospection
     */
    public function setTokenString($tokenString)
    {
        $needle = ' ';
        if (strpos($tokenString, $needle) !== false) {
            $tokenString = substr($tokenString,strpos($tokenString, ' ')+strlen($needle));
        }
        
        $this->tokenString = $tokenString;
        return $this;
    }
    
    
    /**
     * Get the user token for additional usage with the API
     *
     * @return SLoAuthAccessToken
     */
    public function getAccessToken()
    {
        $token = new SLoAuthAccessToken();
        $token
            ->setAccessToken($this->getTokenString())
            ->setExpiresIn((new \Datetime())->getTimestamp() - $this->getExpirationTime())
            ->setExpirationTimestamp($this->getExpirationTime())
            ->setTokenType($this->getTokenType())
            ->setScope($this->getScope());
        
        return $token;
    }
    
    
}