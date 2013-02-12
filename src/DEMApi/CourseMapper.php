<?php
namespace DEMPortal\Api;

use DEMPortal\Api\ApiResult;
use DEMPortal\Api\CourseQuery;
use DEMPortal\Api\ProviderCoursesQuery;
use DEMPortal\Entity\Course;


class CourseMapper
{
    private $courses = array();
    
    public function __construct(ApiResult $result)
    {               
        if($result->getQuery() instanceof ProviderCoursesQuery){
            
            /*
             * handle multiple courses, possibly summized courses
             * i.e no profile or variation detail
             */
            $objects = json_decode($result->getJson());

            foreach($objects as $c){
                
                $course = new Course($c->id, $c->title);
                
                $this->courses[] = $course;
            }
            
        }else if($result->getQuery() instanceof CourseQuery){
            
            /**
             * handle specific course, with all course detail
             */
            $object = json_decode($result->getJson());
                                                
            $course = new Course($object->id, $object->title);
            $course->setDescription($object->profile);
            
            $this->courses[] = $course;
        }
    }
    
    public function getCourses()
    {
        return $this->courses;
    }
}