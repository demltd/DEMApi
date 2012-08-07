<?php
require_once dirname(__FILE__) . "/../TestHelper.php";

class DEMAPITest extends PHPUnit_Framework_TestCase{
    
    /**
     * @var DEMAPI
     */
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
            $this->_api->updateProvider(null, array());
            $this->fail();
        }catch(DEMAPI_IllegalArgumentException $e){
            // expected
        }
        
        try{
            $this->_api->updateProvider(1, array());
            $this->fail();
        }catch(DEMAPI_IllegalArgumentException $e){
            // expected
        }
        
        try{
            $this->_api->updateProvider('1', array('title' => 'test'));
            $this->fail();
        }catch(DEMAPI_IllegalArgumentException $e){
            // expected
        }
        
        $this->_api->updateProvider(1, array('title' => 'Derby University'));
        
        $json = $this->_api->getProvider(1);
        
        $this->assertNotNull($json);
        
        $provider = json_decode($json);
        
        $this->assertEquals('Derby University', $provider->title);
        
        $this->_api->updateProvider(1, array('title' => 'University of Derby'));
        
        $provider = json_decode($this->_api->getProvider(1));
        
        $this->assertEquals('University of Derby', $provider->title);
    }

    public function testGetCourses()
    {
        try{
            $this->_api->getProviderCourses(null);
            $this->fail();
        }catch(DEMAPI_IllegalArgumentException $e){
            // expected
        }
        
        $json = $this->_api->getProviderCourses(1);
        
        $courses = json_decode($json);
        
        $this->assertTrue(count($courses) > 200);
    }
    
    public function testGetCourse()
    {
        try{
            $this->_api->getCourse(null);
            $this->fail();
        }catch(DEMAPI_IllegalArgumentException $e){
            // expected
        }
        
        $json = $this->_api->getCourse(10);
        
        $this->assertNotNull($json);
        
        $course = json_decode($json);
        
        $this->assertTrue(isset($course->title));
        
        $this->assertEquals('Architectural Technology and Digital Innovation (K101)',
            $course->title);
    }
    
    public function testUpdateCourse()
    {
        try{
            $this->_api->updateCourse(null, array());
            $this->fail();
        }catch(DEMAPI_IllegalArgumentException $e){
            // expected
        }
        
        try{
            $this->_api->updateCourse(1, array());
            $this->fail();
        }catch(DEMAPI_IllegalArgumentException $e){
            // expected
        }
        
        $json = $this->_api->getCourse(8);
        
        $course = json_decode($json);
        
        $this->assertEquals('Applied Social Work (L510)', $course->title);
        
        $this->assertEquals(1, $course->active);
        
        echo $this->_api->updateCourse(8, 'active', 0);
        
        $json = $this->_api->getCourse(8);
        
        $course = json_decode($json);
        
        $this->assertEquals(0, $course->active);
        
        $this->_api->updateCourse(8, 'active', 1);
    }
    
    public function testUpdateCourseVariation()
    {
        try{
            $this->_api->updateCourseVariation(null, array());
            $this->fail();
            
        }catch(DEMAPI_IllegalArgumentException $e){
            // expected
        }
        
        try{
            $this->_api->updateCourseVariation(1, array());
            $this->fail();
        }catch(DEMAPI_IllegalArgumentException $e){
            // expected
        }
        
        $json = $this->_api->getCourse(8);
        
        $course = json_decode($json);
        
        $this->assertEquals(array('LOS-UG-BA', 'LOS-UG-BAH'), 
            $course->variations[0]->award_types);
        
        $this->_api->updateCourseVariation($course->variations[0]->id, 
            array(DEMAPI::VARIATION_AWARD_TYPES_PARAM_NAME => 
                'LOS-UG-BA,LOS-UG-BAH,LOS-UG-DIP'));
        
        $json = $this->_api->getCourse(8);
        
        $course = json_decode($json);
        
        $this->assertEquals(array('LOS-UG-BA','LOS-UG-BAH','LOS-UG-DIP'),
            $course->variations[0]->award_types);
    }
    
    public function testGetAwardTypes()
    {
        $json = $this->_api->getAwardTypes();
        
        $this->assertNotNull($json);
        
        $types = json_decode($json);
        
        $this->assertTrue(count($types) > 40);
    }
    
    public function testGetSubjectAreas()
    {
        $json = $this->_api->getSubjectAreas();
        
        $this->assertNotNull($json);
        
        $subjects = json_decode($json);
        
        $this->assertTrue(count($subjects) > 300);
    }
}