<?php
require_once dirname(__FILE__) . "/../TestHelper.php";

class DEMAPITest extends PHPUnit_Framework_TestCase{
    
    private $_api;
    
    public function __construct()
    {
        $this->_api = new DEMAPI(API_KEY, API_SECRET);
    }
    
    public function testGetProvider()
    {
        $json = $this->_api->getProvider(1);
        
        $this->assertNotNull($json);
                
        $provider = json_decode($json);
                
        $this->assertEquals('University of Derby', $provider->title);
        
        $this->assertEquals('http://media.local/provider/1/logo.gif', 
            $provider->logoSrc);
            
        $regions = $provider->subscriptions->subscribedRegions;
        
        $this->assertEquals(17, count($regions));
    }
    
    public function testUpdateProvider()
    {
        $this->fail();
        
        $json = $this->_api->getProvider(1);
        
        $this->assertNotNull($json);
        
    }
    
    
    
}