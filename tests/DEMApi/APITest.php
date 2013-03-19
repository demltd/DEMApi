<?php
require_once dirname(__FILE__) . "/../TestHelper.php";

class DEMAPITest extends PHPUnit_Framework_TestCase{
    
    /**
     * @var DEMAPI
     */
    private $api;
    
    public function __construct()
    {
        $this->api = new DEMAPI(API_KEY, API_SECRET);
    }
    
    public function testGetProvider()
    {
        $json = $this->_api->getProvider(4, array(
            'sid' => 2,
        ));
        
        echo $json;
        
        $this->assertNotNull($json);

        $providerById = json_decode($json);
        
        $json = $this->_api->getProvider('university-of-york');
                
        $providerByIdent = json_decode($json);
        
        $this->assertEquals($providerById, $providerByIdent);
        
        $provider = $providerByIdent;
        
        $this->assertEquals('http://media.demltd.com/providers/university-of-york/logo.gif', 
            $provider->logo_src);
        
        $this->assertEquals('http://www.york.ac.uk/', $provider->url);
        
        $this->assertEquals('', $provider->youtube_embed_code);
        
        $json = $this->_api->getProvider('university-of-york', array(
            'sid' => 2,
        ));
    }
    
    public function UpdateProvider()
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
        $json = $this->_api->getProviderCourses('university-of-york');
        
        $courses = json_decode($json);
        
        $this->assertTrue(count($courses) > 200);
    }
    
    public function testGetCourse()
    {        
        $json = $this->_api->getCourse(87, 13079);
        
        echo $json;
        
        $this->assertNotNull($json);
        
        $courseByPid = json_decode($json);
        
        $json = $this->_api->getCourse('university-of-huddersfield-the-business-school',
            13079);
        
        echo $json;
        
        $courseByIdent = json_decode($json);
        
        $this->assertEquals($courseByPid, $courseByIdent);
        
        $this->assertTrue(isset($course->title));
                        
        $this->assertEquals('Architectural Technology and Digital Innovation (K101)',
            $course->title);               
    }
    
    public function UpdateCourse()
    {        
        $json = $this->_api->getCourse(87, 13079);
        
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
    
    public function GetCourseVariations()
    {
        $this->_api->getCourseVariations(1, 8);
    }
    
    public function UpdateCourseVariation()
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
    
    public function GetProfiles()
    {
        $params = array(
            'sid' => DEMAPI::SITE_ID_STUDYLINK_INTL,
        );
        
        $json = $this->_api->getProviderProfiles(1, $params);
        
        $this->assertNotNull($json);
        
        $this->assertTrue(is_string($json));
        
        $profiles = json_decode($json);
        
        $this->assertTrue(count($profiles) > 10);
        
        foreach($profiles as $p){
            
            $this->assertEquals(1, $p->site_id, 'profile description: ' .
                $p->description);
        }
    }
    
    public function ProviderProfile()
    {
        $json = $this->_api->getProfile(1, 61);
        
        $this->assertNotNull($json);
        
        $profile = json_decode($json);
        
        $this->assertEquals('http://derby.ac.uk/apply', $profile->content);
        
        $original = $profile->content;
        
        $params = array(
            'value' => 'http://derby.ac.uk/applyingtoderby',
        );
        
        $this->_api->updateProviderProfile(1, 61, $params);
        
        $json = $this->_api->getProfile(1, 61);
        
        $profile = json_decode($json);
        
        $this->assertEquals('http://derby.ac.uk/applyingtoderby', $profile->content);

        // change back
        $this->_api->updateProviderProfile(1, 61, array('value' => $original));
        
        
    }
    
    public function GetAwardTypes()
    {
        $json = $this->_api->getAwardTypes();
        
        $this->assertNotNull($json);
        
        $types = json_decode($json);
        
        $this->assertTrue(count($types) > 40);
    }
    
    public function GetSubjectAreas()
    {
        $json = $this->_api->getSubjectAreas();
        
        $this->assertNotNull($json);
        
        $subjects = json_decode($json);
        
        $this->assertTrue(count($subjects) > 300);
    }
    
    public function testSearch()
    {
        $params = array(
            'keyword' => 'Engineering',
            'sid' => 2,
        );
        
        $json = $this->_api->search($params);
        
        $matches = json_decode($json)->results;
        
        $this->assertTrue(strstr($matches[0]->title, 'Engineering') !== false);
    }
}