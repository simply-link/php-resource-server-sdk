<?php
/**
 * Created by PhpStorm.
 * User: ronfridman
 * Date: 31/08/2017
 * Time: 9:44
 */

namespace SimplyLink\ResourceServerBundle\Utils;

use GuzzleHttp\Client;

use SimplyLink\AuthSDKBundle\Api\BaseSimplyLinkConnector;
use SimplyLink\UtilsBundle\Utils\Exceptions\SLExceptionAuthentication;
use SimplyLink\UtilsBundle\Utils\SLBaseUtils;

class SLoAuthTokenValidator
{
    
    /**
     * @var Client
     */
    private $httpClient;
    
    /**
     * SLoAuthTokenValidator constructor.
     */
    public function __construct()
    {
        $this->httpClient = new Client([]);
    }
    
    
    /**
     * Validate token with SimplyLink oAuth server
     *
     * @param string $token The token passed in  the Authorization header
     * @param string $clientIP The REMOTE_ADDR of the request
     * @param string $referer The HTTP_REFERER of the request, can be null
     * @return array|mixed
     */
    public function validateToken($token,$clientIP, $referer)
    {
        $headers['Authorization'] = $token;
        
        if($clientIP)
            $headers['HTTP_X_SL_CLIENT_IP'] = $clientIP;
        
        if($referer)
            $headers['HTTP_X_SL_CLIENT_REFERER'] = $referer;
    
        $options = [];
        $options['headers'] = $headers;
        
        $domain = BaseSimplyLinkConnector::getDomainForEnvironment();
    
        $responseArray = [];
        try{
            $response = $this->httpClient->request('GET','https://auth.' . $domain . '/oauth/v2/introspection',$options);
        
            $responseArray = json_decode($response->getBody()->getContents(), true);
        }
        catch (\Exception $e)
        {
            SLBaseUtils::getLogger()->critical('Validate Token Error - ' . $e->getMessage(),['headers' => $headers]);
            throw (new SLExceptionAuthentication());
        }
    
        return $responseArray;
    }
    
}
