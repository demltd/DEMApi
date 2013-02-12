<?php
namespace DEMPortal\Api;

/**
 * Responsible for holding all data related to a provider courses query.
 * I.e an api query to get all of a providers courses:
 * 
 * /providers/university-of-york/courses
 */
class ProviderCoursesQuery
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
            throw new \InvalidArgumentException('ident cannot be null and must
                be of type string');
        }
        
        $this->ident = $ident;
    }
    
    public function getIdent()
    {
        return $this->ident;
    }
}