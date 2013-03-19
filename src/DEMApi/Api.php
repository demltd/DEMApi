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
    private $_secret;
    
    public function __construct($apiKey, $apiSecret)
    {
        $this->apiKey = $apiKey;
        $this->_secret = $apiSecret;
        $this->apiUrl = 'http://api.demltd.com';
    }
    
    public function setApiUrl($url)
    {
        $this->apiUrl = $url;
    }
    
    /**
     * Returns provider data in json format based on the provider id.
     * 
     * GET /api/search/{ident}/
     * 
     * @param string $ident
     * @return tring json
     */
    public function getProvider($identifier, $params = array())
    {
        return $this->call("providers/$identifier", 'get', $params);
    }
    
    /**
     * GET /api/providers/{ident}/meta/
     * 
     * @param string $identifier
     * @return type
     */
    public function getProviderMeta($identifier)
    {
        return $this->call("providers/$identifier/meta", 'get', array());
    }
        
    /**
     * Returns the courses associated with the provider id.
     * 
     * @param type $pid
     * @return json
     */
    public function getProviderCourses($pid)
    {
        return $this->call("providers/$pid/courses", 'get');
    }
    
    const SITE_ID_STUDYLINK_INTL = 1;
    
    public function getProviderProfiles($pid, array $params)
    {
        return $this->call("providers/$pid/profiles", 'get', $params);
    }
    
    public function getProfile($pid, $profileId)
    {
        return $this->call("providers/$pid/profiles/$profileId", 'get');
    }

    /**
     * Returns the course associated with the course id.
     * 
     * @param string $identifier
     * @param int $cid
     * @return json
     */
    public function getCourse($identifier, $cid)
    {           
        return $this->call("providers/$identifier/courses/$cid", 'get');
    }
    
    /**
     * 
     * @param int $pid
     * @param int $cid
     * @return type
     */
    public function getCourseVariations($pid, $cid)
    {
        return $this->call("providers/$pid/courses/$cid/variations", 'get');
    }
    
    /**
     * 
     * @param type $pid
     * @param type $cid
     * @param type $vid
     * @return type
     */
    public function getVariation($pid, $cid, $vid)
    {
        return $this->call("providers/$pid/courses/$cid/variations/$vid", 'get');
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
        
        $signature = $this->_sign($path, $method, $date);
        
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

            default:
                break;
        }        
        return $output;
    }
    

    /**
     * Signs the api request with your secret key.
     * 
     * @param type $path
     * @param type $method
     * @param type $date
     * @return type
     */
    private function _sign($path, $method, $date)
    {        
        return sha1($path . $method . $date . $this->_secret);
    }
}