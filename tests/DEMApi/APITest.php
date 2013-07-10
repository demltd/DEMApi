<?php
namespace DEMApi;

use DEMApi\Api;
use PHPUnit_Framework_TestCase;

class ApiTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Api
     */
    private $api;
    
    public function __construct()
    {        
        $config = include __DIR__ . '/../../config/module.demapi.local.php';
        
        $this->api = new Api($config['demapi']['api_key'], $config['demapi']['api_secret']);
        
        $this->api->setApiUrl('http://staging-portal.demltd.com/api/');
        
        $this->api->setSiteId(3);
    }
            
    public function testSearchDefaultPageAndRpp()
    {        
        $json = $this->api->search('engineering');
        
        $decodedResult = json_decode($json);

        $this->assertEquals(1, $decodedResult->current_page);
        
        $this->assertEquals(10, $decodedResult->rpp);
    }
    
    public function testSearch()
    {   
        // test keyword
        
        $json = $this->api->search('engineering', null, null, '1',
            null, null, 12, 60);

        $matches = json_decode($json)->matches;
        
        $this->assertTrue(strstr($matches[0]->course_title, 'Engineering') !== false);
        
        // test current_page
        
        $json = $this->api->search('engineering', '2');
        
        $decodedResult = json_decode($json);
        
        $this->assertEquals(2, $decodedResult->current_page);
        
        // test rpp
        
        $json = $this->api->search('engineering', '2', '50');
        
        $decodedResult = json_decode($json);
        
        $this->assertEquals(50, $decodedResult->rpp);
        
        // test pages
        
        $expectedPages = ceil($decodedResult->total / $decodedResult->rpp);
        
        $this->assertEquals($expectedPages, $decodedResult->pages);
        
        // test study mode
        
        $json = $this->api->search('engineering', null, null, 'distance');
        
        $decodedResult = json_decode($json);
        
        $this->assertEquals('distance', $decodedResult->matches[0]->variations[0]->study_modes);
    }
    
    public function testGetProvider()
    {
        $json = $this->api->getProvider('university-of-derby');

        $decodedResult = json_decode($json);
        
        $this->assertEquals('university-of-derby', $decodedResult->ident);
        $this->assertEquals('University of Derby', $decodedResult->title);
        $this->assertEquals('GB', $decodedResult->country);
    }
    
    public function testGetProviderMeta()
    {
        $json = $this->api->getProviderMeta('university-of-derby');
        
        echo $json;
        $decodedResult = json_decode($json);
        
        $this->assertNotNull($decodedResult->facilities);
        $this->assertNotNull($decodedResult->year_founded);
    }
    
    public function testGetAllProviderProfiles()
    {
        $json = $this->api->getProviderProfiles('university-of-derby', 1);
        
        $decodedResult = json_decode($json);
        
        $this->assertTrue(count($decodedResult) > 0);
        $this->assertNotNull($decodedResult[0]->id);
        $this->assertNotNull($decodedResult[0]->site_id);
        $this->assertNotNull($decodedResult[0]->description);
    }
    
    public function testGetProviderProfile()
    {
        $json = $this->api->getProviderProfile('university-of-derby',
            'profile', 1);
        
        $decodedResult = json_decode($json);
        
        $this->assertNotNull($decodedResult->id);
        $this->assertEquals(1, $decodedResult->site_id);
        $this->assertEquals('profile', $decodedResult->description);
        $this->assertNotNull($decodedResult->content);
    }
    
    public function testGetProviderProfilesForSiteId()
    {
        $json = $this->api->getProviderProfiles('university-of-derby', 1);

        $decodedResult = json_decode($json);
        
        foreach($decodedResult as $profile){
            
            $this->assertEquals(1, $profile->site_id);
        }
    }
    
    public function testGetProviderCourses()
    {
        $json = $this->api->getProviderCourses('university-of-derby');
        
        $decodedResult = json_decode($json);
        
        $this->assertTrue(count($decodedResult) > 0);
        $this->assertNotNull($decodedResult[0]->id);
        $this->assertNotNull($decodedResult[0]->title);
    }
    
    public function testGetCourse()
    {
        // get a cid first
        $json = $this->api->getProviderCourses('university-of-derby');
        
        $decodedResult = json_decode($json);
        
        $cid = (int) $decodedResult[0]->id;
        
        // get course
        $json = $this->api->getCourse('university-of-derby', $cid);
        
        $decodedResult = json_decode($json);
        
        $this->assertNotNull($decodedResult->id);
        $this->assertEquals($cid, $decodedResult->id);
        $this->assertNotNull($decodedResult->title);
        $this->assertNotNull($decodedResult->contact_name);
        $this->assertNotNull($decodedResult->variations);
        $this->assertTrue(is_array($decodedResult->variations));
    }
    
    public function testGetCourseProfile()
    {       
        $json = $this->api->getProviderCourses('university-of-derby');
        
        $decodedResult = json_decode($json);
        
        $cid = (int) $decodedResult[10]->id;
        
        // get profile
        $json = $this->api->getCourseProfile('university-of-derby', $cid, 'profile');
        
        echo $json;
        
        $decodedResult = json_decode($json);
        
        $this->assertEquals('profile', $decodedResult->description);
    }
}