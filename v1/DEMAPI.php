<?php
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
class DEMAPI
{
    /**
    * This api library is for version 1 of the api.
    */
    private $_version = 'v1';
    
    /**
    *  Api returns json
    */
    private $_acceptType = 'text/json';
    
    private $_apiUrl;
    
    /**
    *  API Key identifies you.
    */
    private $_apiKey;
    
    /** 
    *  Secret key used to sign request.
    */
    private $_secret;
    
    public function __construct($apiKey, $apiSecret)
    {
        $this->_apiKey = $apiKey;
        $this->_secret = $apiSecret;
        $this->_apiUrl = 'http://api.publisher.local';
    }
    
    /**
     * Returns provider data in json format based on the provider id.
     * 
     * @param type $pid
     * @return json
     */
    public function getProvider($pid)
    {
        return $this->_call('provider', 'get', $pid);
    }
    
    const PROVIDER_TITLE_PARAM_NAME = 'title';
    
    /**
     * Updates provider associated with given id .
     * 
     * @param type $pid
     * @param array $params
     * @return json
     * @throws DEMAPI_IllegalArgumentException
     */
    public function updateProvider($pid, array $params)
    {   
        if($pid === null){
            throw new DEMAPI_IllegalArgumentException('provider is was null');
        }
        
        return $this->_call('provider', 'put', $pid, $params);
    }
    
    /**
     * Returns the courses associated with the provider id.
     * 
     * @param type $pid
     * @return json
     * @throws DEMAPI_IllegalArgumentException
     */
    public function getProviderCourses($pid)
    {
        if($pid === null){
            throw new DEMAPI_IllegalArgumentException('provider id cannot 
                be null');
        }
        
        return $this->_call('course', 'get', null, array('pid' => $pid));
    }
    
    /**
     * Returns the course associated with the course id.
     * 
     * @param int $cid
     * @return json
     * @throws DEMAPI_IllegalArgumentException
     */
    public function getCourse($cid)
    {   
        if($cid === null){
            throw new DEMAPI_IllegalArgumentException('course id cannot be
                null');
        }
        
        return $this->_call('course', 'get', $cid);
    }
    
    const COURSE_ACTIVE_PARAM_NAME = 'active';
    
    /**
     * Update a course field
     * 
     * @param type $cid
     * @param type $field
     * @param type $value
     */
    public function updateCourse($cid, array $params)
    {
        if($cid === null){
            throw new DEMAPI_IllegalArgumentException('course id cannot
                be null');
        }
        
        return $this->_call('course', 'put', $cid, $params);
    }
    
    const VARIATION_AWARD_TYPES_PARAM_NAME = 'award_types';
    
    public function updateCourseVariation($vid, array $params)
    {
        if($vid === null){
            throw new DEMAPI_IllegalArgumentException('variation id cannot be
                null');
        }
        
        return $this->_call('variation', 'put', $vid, $params);
        
    }
    
    /**
     * Returns all award types
     * 
     * @return json
     */
    public function getAwardTypes()
    {
        return $this->_call('award', 'get');
    }
    
    /**
     * Returns all subject areas
     * 
     * @return json
     */
    public function getSubjectAreas()
    {
        return $this->_call('subject', 'get');
    }
    
    /**
     * 
     * @param string $resource
     * @param string $method
     * @param int $id
     * @param array $params
     * @return type
     * @throws Exception
     */
    private function _call($resource, $method, $id = null, $params = array())
    {
        $path = '/' . $this->_version . "/$resource";
        
        if($id !== null){
            $path .=  "/$id";
        }
        
        date_default_timezone_set('UTC');
        $date = new DateTime();        
        $date = $date->format(DateTime::RFC822);
                        
        $fields = "";
        foreach(array_keys($params) as $p){
            $fields .= "$p=" . $params[$p] . '&';
        }
       
        $url = $this->_apiUrl . $path;

        // add any get params
        if($method === 'get'){
            $url .= "?$fields";
        }
        
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
            'Authorization:' . API_KEY . ":$signature", 
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
                throw new DEMAPI_UnauthorizedAccessException($output);
                break;
            case '400':
                throw new DEMAPI_IllegalArgumentException($output);
                break;
            case '500':
                throw new DEMAPI_ServerErrorException('There was a problem handling
                    your request, please try again later');
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

class DEMAPI_IllegalArgumentException extends Exception{}
class DEMAPI_UnauthorizedAccessException extends Exception{}
class DEMAPI_ServerErrorException extends Exception{}