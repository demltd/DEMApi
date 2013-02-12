<?php
namespace DEMPortal\Api;

use DEMPortal\Api\ApiQuery;
use InvalidArgumentException;

class CourseQuery
    implements ApiQuery
{
    /**
     *
     * @var int
     */
    private $cid;
    
    /**
     *
     * @var string
     */
    private $providerIdent;
    
    /**
     * 
     * @param type $cid
     * @param type $providerIdent
     * @throws InvalidArgumentException
     */
    public function __construct($cid, $providerIdent)
    {
        if(!is_int($cid)){
            throw new InvalidArgumentException('cid cannot be null and must
                be of type integer');
        }
        
        if(!is_string($providerIdent)){
            throw new InvalidArgumentException('provider ident cannot be null
                and must be of type integer');
        }
        
        $this->cid = $cid;
        
        $this->providerIdent = $providerIdent;
    }
    
    public function getCid()
    {
        return $this->cid;
    }
    
    public function getProviderIdent()
    {
        return $this->providerIdent;
    }
}