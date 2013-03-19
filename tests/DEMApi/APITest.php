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
//        $this->api = new API(API_KEY, API_SECRET);
        $this->api = new Api('', '');
        
        $this->api->setApiUrl('http://dev-portal.demltd.com/api/');
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
        
        $json = $this->api->search('engineering');
                
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
        
        $decodedResult = json_decode($json);
        
        $this->assertNotNull($decodedResult->facilities);
        $this->assertNotNull($decodedResult->year_founded);
    }
}