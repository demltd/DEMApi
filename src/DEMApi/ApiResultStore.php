<?php
namespace DEMPortal\Api;

use Zend\Cache\Storage\StorageInterface;


/**
 * An Identity Map
 */
class ApiResultStore
{
    private $cache;
    
    public function __construct(StorageInterface $cache) {
        
        $this->cache = $cache;
    }
        
    public function add(ApiResult $result)
    {
        $key = $this->getKey($result->getQuery());
        
        $this->cache->setItem($key, $result);
    }
    
    public function getResult(ApiQuery $query)
    {
        $key = $this->getKey($query);
        
        $result = $this->cache->getItem($key);
        
        if($result === null){
            return null;            
        }else{
            return $result;
        }
    }
    
    /**
     * Creates the result store key by serializing the
     * query object and creating an md5 hash of the string.
     * 
     * @param ApiQuery $query
     * @return string md5
     */
    private function getKey(ApiQuery $query)
    {
        return md5(serialize($query));
    }
}