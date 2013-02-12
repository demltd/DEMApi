<?php
namespace DEMPortal\Api;

use InvalidArgumentException;

class SearchQuery implements ApiQuery
{
    private $keywords;
    
    private $level;
    
    private $location;
    
    public function __construct($keywords, $level = null, $location = null)
    {
        if(!is_string($keywords)){
            throw new InvalidArgumentException('keywords cannot be null and must be of type string');
        }
        
        $this->keywords = $keywords;
        
        $this->level = $level;
        
        $this->location = $location;
    }
    
    public function getKeywords()
    {
        return $this->keywords;
    }
    
    public function getLevel()
    {
        return $this->level;
    }
    
    public function getLocation()
    {
        return $this->location;
    }
}