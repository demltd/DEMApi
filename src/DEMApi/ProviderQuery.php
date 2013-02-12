<?php
namespace DEMPortal\Api;

use DEMPortal\Api\ApiQuery;
use InvalidArgumentException;

/**
 * Query object responsible for holding all
 * data required to make a provider api query
 */
class ProviderQuery
    implements ApiQuery
{
    /**
     *
     * @var string
     */
    private $ident;
    
    public function __construct($ident)
    {
        if(!is_string($ident)){
            throw new InvalidArgumentException('ident cannot be null and must
                be of type string');
        }
        
        $this->ident = $ident;
    }
    
    public function getIdentifier()
    {
        return $this->ident;
    }
}