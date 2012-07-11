<?php
/**
*  Library exposing the DEM api.
*
*  Expected usage:
*      - Client push updates
*      - Third party sites
*      - Internal sites
* 
*  Requires an api key belonging to an administrator or client
*  user, otherwise will throw an exception.
*
*  Some methods require administrator authentication.
*/
class DEMAPI
{
    /**
    * This api library is for version 1 of the api.
    */
    private $_version = 1;
    
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
        $this->_apiUrl = 'http://api.publisher.local/v' . $this->_version;
    }
    
    /**
    *  Returns provider data based on the provider id.
    * 
    *  @param $pid Provider id
    *  @return json string e.g '{ id: 1, title: University of Derby, 
    *      logoSrc: http://media.demltd.com/1/logo.jpg }'
    *  @throws DEMAPI_UnauthorizedAccessException
    */
    public function getProvider($pid)
    {
        return $this->_call('provider', 'get', $pid);
    }
    
    /**
    *  Updates provider associated with given id with the json data.
    *
    *  @param $json json representation of provider
    *  @param $pid
    *  @throws DEMAPI_IllegalArgumentException
    */
    public function updateProvider($json, $pid)
    {
        if($json === null){
            throw new DEMAPI_IllegalArgumentException('provider was null');
        }
        
        if(!is_string($json)){
            throw new DEMAPI_IllegalArgumentException('provider was not a json
                string');
        }
        
        if($pid === null){
            throw new DEMAPI_IllegalArgumentException('provider is was null');
        }
        
        return $this->_call('provider', 'put', $pid, $json);
    }
    
    private function _call($resource, $method, $id = null, $json = null)
    {
        $url = $this->_apiUrl . '/' . $resource . '/';
        
        if($id !== null){
            $url .= $id . '/';
        }
        
        date_default_timezone_set('UTC');
        $date = new DateTime();        
        $date = $date->format('Y-m-dH:i:s');
        
        $params = "apiKey=$this->_apiKey&timestamp=$date&signature=" . 
            $this->_sign($resource, $method, $id, $date);
                    
        $url .= "?$params";

        $ch = curl_init($url);

        // set method
        switch($method){
            case 'get':
                curl_setopt($ch, CURLOPT_HTTPGET, true);
                break;
            case 'put':
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
                break;
            default:
                throw new Exception('Invalid http method');
        }
        
        if($json !== null){
            $fields = array('json' => $json);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
        }
        
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        
        $output = curl_exec($ch);

        curl_close($ch);
        
        return $output;
    }
    
    /**
    *  Signs the api request with your secret key.
    */
    private function _sign($resource, $method, $id, $date)
    {
        $params = array(
            'module' => 'v1',
            'controller' => $resource,
            'action' => $method,
            'apiKey' => $this->_apiKey,
            'timestamp' => $date,
        );
        
        if($id !== null){
            $params['id'] = (string) $id;
        }
        
        return sha1($this->_apiKey . $date . implode($params) . $this->_secret);
    }
}

class DEMAPI_IllegalArgumentException extends Exception{}