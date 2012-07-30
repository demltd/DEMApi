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
        $json = $this->_api->getProvider(1);
        
        $this->assertNotNull($json);
        
        $provider = json_decode($json);
        
        $this->assertEquals('University of Derby', $provider->title);
        
        try{
            $this->_api->updateProvider(null, null);
            $this->fail();
        }catch(DEMAPI_IllegalArgumentException $e){
            // expected
        }
        
        try{
            $this->_api->updateProvider(1, null);
            $this->fail();
        }catch(DEMAPI_IllegalArgumentException $e){
            // expected
        }
        
        try{
            $this->_api->updateProvider('1', null);
            $this->fail();
        }catch(DEMAPI_IllegalArgumentException $e){
            // expected
        }
        
        $provider->title = 'Derby University';
        
        $this->_api->updateProvider(json_encode($provider), 1);
        
        $json = $this->_api->getProvider(1);
        
        $this->assertNotNull($json);
        
        $provider = json_decode($json);
        
        $this->assertEquals('Derby University', $provider->title);
        
        $provider->title = 'University of Derby';
        
        $this->_api->updateProvider(json_encode($provider), 1);
        
        $this->assertEquals('University of Derby', $provider->title);
    }

    public function testGetCourses()
    {
        $json = $this->_api->getProviderCourses(1);
        
        $courses = json_decode($json);
        
        $this->assertTrue(count($courses) > 200);
    }
    
    public function testGetAwardTypes()
    {
        $json = $this->_api->getAwardTypes();
        
        $this->assertNotNull($json);
        
        $types = json_decode($json);
        
        $this->assertTrue(count($types) > 40);
    }
}