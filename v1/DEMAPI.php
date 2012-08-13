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
        return $this->_call("providers/$pid", 'get');
    }
    
    const PROVIDER_TITLE_PARAM_NAME = 'title';
    
    /**
     * Updates provider associated with given id .
     * 
     * @param type $pid
     * @param array $params
     * @return json
     */
    public function updateProvider($pid, array $params)
    {   
        return $this->_call("providers/$pid", 'put', $params);
    }
    
    /**
     * Returns the courses associated with the provider id.
     * 
     * @param type $pid
     * @return json
     */
    public function getProviderCourses($pid)
    {
        return $this->_call("providers/$pid/courses", 'get');
    }
    
    /**
     * Returns the course associated with the course id.
     * 
     * @param int $pid
     * @param int $cid
     * @return json
     */
    public function getCourse($pid, $cid)
    {           
        return $this->_call("providers/$pid/courses/$cid", 'get');
    }
    
    const COURSE_ACTIVE_PARAM_NAME = 'active';
    
    /**
     * Update a course field
     * 
     * @param int $pid
     * @param int $cid
     * @param array $params
     */
    public function updateCourse($pid, $cid, array $params)
    {
        return $this->_call("providers/$pid/courses/$cid", 'put', $params);
    }
    
    /**
     * 
     * @param int $pid
     * @param int $cid
     * @return type
     */
    public function getCourseVariations($pid, $cid)
    {
        return $this->_call("providers/$pid/courses/$cid/variations", 'get');
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
        return $this->_call("providers/$pid/courses/$cid/variations/$vid", 'get');
    }
    
    const VARIATION_AWARD_TYPES_PARAM_NAME = 'award_types';
    
    /**
     * 
     * @param type $pid
     * @param type $cid
     * @param type $vid
     * @param array $params
     * @return json
     * @throws DEMAPI_IllegalArgumentException
     */
    public function updateVariation($pid, $cid, $vid, array $params)
    {
        return $this->_call("providers/$pid/courses/$cid/variations/$vid", 'put', $params);
    }
    
    /**
     * Returns all award types
     * 
     * @return json
     */
    public function getAwardTypes()
    {
        return $this->_call('awardtypes', 'get');
    }
    
    /**
     * Returns all subject areas
     * 
     * @return json
     */
    public function getSubjectAreas()
    {
        return $this->_call('subjectareas', 'get');
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
    private function _call($resource, $method, $params = array())
    {
        $path = "/$resource";
        
        $fields = "";
        foreach(array_keys($params) as $p){
            $fields .= "$p=" . $params[$p] . '&';
        }
        
        // add any get params
        if($method === 'get'){
            $path .= "?$fields";
        }
        
        date_default_timezone_set('UTC');
        $date = new DateTime();        
        $date = $date->format(DateTime::RFC822);

        $url = $this->_apiUrl . $path;
        
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