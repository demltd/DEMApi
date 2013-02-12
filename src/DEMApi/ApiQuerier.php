<?php
namespace DEMPortal\Api;

use InvalidArgumentException;

class ApiQuerier
{   
    /**
     *
     * @var DEMApi
     */
    private $api;
    
    /**
     *
     * @var ApiResultStore
     */
    private $resultStore;
    
    public function __construct(DEMApi $api, ApiResultStore $resultStore)
    {
        $this->api = $api;
        
        $this->resultStore = $resultStore;
    }
    
    /**
     * Queries the api depending on the ApiQuery type.
     * TODO: stop using sid and return results based on
     * user location.
     * 
     * @param ApiQuery $query
     * @return ApiResult
     * @throws InvalidArgumentException
     */
    public function getResult(ApiQuery $query)
    {
        if($this->resultStore->getResult($query) !== null){
            
            return $this->resultStore->getResult($query);
                    
        }else{
                
            if($query instanceof SearchQuery){

                $json = $this->getDEMApi()->search(
                    array(
                        'keyword' => $query->getKeywords(),
                        'sid' => 2,
                    ));

            }else if ($query instanceof ProviderQuery){
                
                $json = $this->getDEMApi()->getProvider(
                    $query->getIdentifier(),
                    array(
                        'sid' => 2,
                    ));
                
            }else if($query instanceof ProviderCoursesQuery){
                
                $json = $this->getDEMApi()->getProviderCourses(
                    $query->getIdent());
                
            }else if($query instanceof CourseQuery){
                
                $json = $this->getDEMApi()->getCourse(
                    $query->getProviderIdent(), $query->getCid());
                
            }else{
                throw new InvalidArgumentException('ApiQuery type "' . get_class($query) . '" 
                    not handled');
            }
            
            $result = new ApiResult($query, $json);
            
            $this->resultStore->add($result);
            
            return $result;            
        }        
    }
    
    /**
     * 
     * @return DEMApi
     */
    public function getDEMApi()
    {
        if($this->api === null){
            
            throw new \InvalidArgumentException('api must be set');
        }
        
        return $this->api;
    }
}