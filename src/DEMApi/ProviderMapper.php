<?php
namespace DEMPortal\Api;

use DEMPortal\Api\ApiResult;
use DEMPortal\Entity\Provider;

/**
 * Maps the ApiResult to a Provider
 */
class ProviderMapper
{
    /**
     *
     * @var Provider
     */
    private $provider;
    
    public function __construct(ApiResult $result)
    {
        $object = json_decode($result->getJson());
        
        $this->provider = new Provider($object->id, $object->title);
        
        $this->provider->setUrl($object->url);
        
        $this->provider->setLogoSrc($object->logo_src);
        
        $this->provider->setYoutubeEmbedCode($object->youtube_embed_code);
        
//        $this->provider->setProfile($object->profile);
        
        if(is_string($object->parent_ident)){

            $this->provider->setParentIdent($object->parent_ident);
        }
    }
    
    public function getProvider()
    {
        return $this->provider;
    }
}