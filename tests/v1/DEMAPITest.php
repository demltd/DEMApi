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
        
        $this->_api->updateProvider(1, array(
            DEMAPI::PROVIDER_TITLE_PARAM_NAME => 'Derby University'
        ));
        
        $json = $this->_api->getProvider(1);
        
        $this->assertNotNull($json);
        
        $provider = json_decode($json);
        
        $this->assertEquals('Derby University', $provider->title);
        
        $this->_api->updateProvider(1, array(
            'title' => 'University of Derby'
        ));
        
        $provider = json_decode($this->_api->getProvider(1));
        
        $this->assertEquals('University of Derby', $provider->title);
    }

    public function testGetProviderCourses()
    {        
        $json = $this->_api->getProviderCourses(1);
        
        $courses = json_decode($json);
        
        $this->assertTrue(count($courses) > 200);
    }
    
    public function testGetCourse()
    {        
        $json = $this->_api->getCourse(1, 10);
        
        $this->assertNotNull($json);
        
        $course = json_decode($json);
        
        $this->assertTrue(isset($course->title));
        
        $this->assertEquals('Architectural Technology and Digital Innovation (K101)',
            $course->title);
    }
    
    public function testUpdateCourse()
    {        
        $json = $this->_api->getCourse(1, 8);
        
        $course = json_decode($json);
        
        $this->assertEquals('Applied Social Work (L510)', $course->title);
        
        $this->assertEquals(1, $course->active);
        
        $this->_api->updateCourse(1, 8, array(
            DEMAPI::COURSE_ACTIVE_PARAM_NAME => 0
        ));
        
        $json = $this->_api->getCourse(1, 8);
        
        $course = json_decode($json);
        
        $this->assertEquals(0, $course->active);
        
        $this->_api->updateCourse(1, 8, array(
            DEMAPI::COURSE_ACTIVE_PARAM_NAME => 1
        ));
    }
    
    public function testGetCourseVariations()
    {
        $this->_api->getCourseVariations(1, 8);
    }
    
    public function testUpdateCourseVariation()
    {        
        $json = $this->_api->getCourseVariations(1, 8);
        
        $variations = json_decode($json);
               
        $this->assertEquals(array('LOS-UG-BA', 'LOS-UG-BAH'), 
            $variations[0]->award_types);
        
        $vid = $variations[0]->id;
        
        echo $this->_api->updateVariation(1, 8, $vid, 
            array(DEMAPI::VARIATION_AWARD_TYPES_PARAM_NAME => 
                'LOS-UG-BA,LOS-UG-BAH,LOS-UG-DIP'));
        
        $json = $this->_api->getVariation(1, 8, $vid);
        
        $variation = json_decode($json);
        
        $this->assertEquals(array('LOS-UG-BA','LOS-UG-BAH','LOS-UG-DIP'),
            $variation->award_types);
        
        // set back to original value
        $this->_api->updateVariation(1, 8, $vid, array(
            DEMAPI::VARIATION_AWARD_TYPES_PARAM_NAME => 'LOS-UG-BA,LOS-UG-BAH',
        ));
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