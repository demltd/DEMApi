<?php
namespace DEMApi;

use DateTime;
use DomainException;
use Exception;
use InvalidArgumentException;
use RuntimeException;
/**
 * Library exposing the DEM api.
 *
 * Expected usage:
 *      - Client push updates
 *      - Third party sites
 *      - Internal sites
 * 
 * Requires an api key belonging to an administrator or client
 * user, otherwise will throw an exception.
 *
 * Some methods require administrator authentication.
 */
class Api
{    
    /**
    *  Api returns json
    */
    private $_acceptType = 'text/json';
    
    private $apiUrl;
    
    /**
    *  API Key identifies you.
    */
    private $apiKey;
    
    /** 
    *  Secret key used to sign request.
    */
    private $secret;
    
    public function __construct($apiKey, $apiSecret)
    {
        $this->apiKey = $apiKey;
        $this->secret = $apiSecret;
        $this->apiUrl = 'http://api.demltd.com';
    }
    
    public function setApiUrl($url)
    {
        $this->apiUrl = $url;
    }
    
    /**
     * Returns provider data in json format based on the provider id.
     * 
     * GET /api/providers/{ident}/
     * 
     * @param string $ident
     * @return tring json
     */
    public function getProvider($ident)
    {
        return $this->call("providers/$ident", 'get');
    }
    
    /**
     * GET /api/providers/{ident}/meta/
     * 
     * @param string $ident
     * @return type
     */
    public function getProviderMeta($ident)
    {
        return $this->call("providers/$ident/meta", 'get');
    }
    
    /**
     * GET /api/providers/{ident}/profiles/
     * 
     * @param string $ident
     * @param int $sid site id the profile is for
     * @return type
     */
    public function getProviderProfiles($ident, $sid)
    {
        return $this->call("providers/$ident/profiles/$sid", 'get');
    }
    
    /**
     * Returns a single profile for given provider, description
     * and site id.
     * 
     * GET /api/providers/{ident}/profiles/{siteId}/{description}/
     * 
     * @param string $ident
     * @param string $description
     * @param string $siteId
     */
    public function getProviderProfile($ident, $description, $siteId)
    {
        return $this->call("providers/$ident/profiles/$siteId/$description",
            'get');
    }
    
        
    /**
     * Returns the courses associated with the provider ident.
     * 
     * GET /api/providers/{ident}/courses/
     * 
     * @param string $ident
     * @return json
     */
    public function getProviderCourses($ident)
    {
        return $this->call("providers/$ident/courses", 'get');
    }

    /**
     * Returns the course associated with the course id.
     * Includes all course variations.
     * 
     * GET /api/providers/{ident}/courses/{id}/
     * 
     * @param string $ident
     * @param int $cid
     * @return json
     */
    public function getCourse($ident, $cid)
    {           
        return $this->call("providers/$ident/courses/$cid", 'get');
    }
    
    /**
     * Returns the most relevant courses (along 
     * with variations) for the given search criteria.
     * 
     * GET /api/search/
     * 
     * @param string $keywords
     * @param mixed $page
     * @param mixed $rpp
     * @param string $studyMode
     * @return string json
     */
    public function search($keywords = null, $page = null, $rpp = null,
        $studyMode = null)
    {
        $params = array();
        
        if($keywords !== null){
            $params['keywords'] = $keywords;
        }
        
        if($page !== null){
            $params['page'] = $page;
        }
        
        if($rpp !== null){
            $params['rpp'] = $rpp;
        }
        
        if($studyMode !== null){
            $params['study_mode'] = $studyMode;
        }
        
        return $this->call('search', 'get', $params);
    }
    
    /**
     * @param type $resource
     * @param type $method
     * @param type $params
     * @return type
     * @throws Exception
     * @throws DEMAPI_UnauthorizedAccessException
     * @throws DEMAPI_IllegalArgumentException
     * @throws DEMAPI_ServerErrorException
     */
    private function call($resource, $method, $params = array())
    {
        $path = "$resource/";
        
        $fields = "";
        foreach(array_keys($params) as $p){
            $fields .= "$p=" . urlencode($params[$p]) . '&';
        }
        
        // add any get params
        if($method === 'get'){
            $path .= "?$fields";
        }
        
        date_default_timezone_set('UTC');
        $date = new DateTime();        
        $date = $date->format(DateTime::RFC822);

        $url = $this->apiUrl . $path;
        
        $ch = curl_init($url);

        // set method
        switch($method){
            case 'get':
                curl_setopt($ch, CURLOPT_HTTPGET, true);
                break;
            case 'put':
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
                curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
                break;
            default:
                throw new Exception('Invalid http method');
        }
        
        $signature = $this->sign("/api/$resource/", 'GET', $date);
        
        $headers = array(
            'Authorization:' . $this->apiKey . ":$signature", 
            "Date: $date",
        );
        
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        
        $output = curl_exec($ch);
        
        $httpStatusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        
        curl_close($ch);
        
        switch ($httpStatusCode) {
            case '401':
                throw new DomainException($output);
                break;
            case '400':
                throw new InvalidArgumentException($output);
                break;
            case '500':
                throw new RuntimeException('There was a problem handling
                    your request, please try again later' . $output);
                break;
            case '404':
                throw new RuntimeException('Resource not found');
                break;

            default:
                break;
        }        
        
        return $output;
    }
    

    /**
     * Signs the api request with your secret key.
     * 
     * @param type $resource
     * @param type $method
     * @param type $date
     * @return type
     */
    private function sign($resource, $method, $date)
    {        
        return sha1($resource . $method . $date . $this->secret);
    }
}