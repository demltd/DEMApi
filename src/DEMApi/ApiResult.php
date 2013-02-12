<?php
namespace DEMPortal\Api;

use DEMPortal\Api\ApiQuery;
use InvalidArgumentException;

class ApiResult
{
    /**
     *
     * @var ApiQuery
     */
    private $query;
    
    /**
     *
     * @var string
     */
    private $json;
    
    public function __construct(ApiQuery $query, $json)
    {
        if(!is_string($json) || $json === ''){
            throw new InvalidArgumentException('json response cannot be null
                and must be of type string');
        }
        
        $this->query = $query;
        
        $this->json = $json;
    }
    
    public function getJson()
    {
        return $this->json;
    }
    
    public function getQuery()
    {
        return $this->query;
    }
}