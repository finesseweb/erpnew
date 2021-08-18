<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */



//define('SS_ACAD_T', 305 << 8);
//define('SS_ACAD_M', 306 << 8);
//define('SS_ACAD_P', 307 << 8);
//define('SS_ACAD_FP', 308 << 8);
//define('SS_ACAD_R', 309 << 8);
//define('SS_ACAD_A', 310 << 8);
//define('SS_ACAD_L', 311 << 8);
//define('SS_ACAD_SA', 312 << 8);
//define('SS_ACAD_EV', 314 << 8);
//define('SS_ACAD_PL', 315 << 8);
define('SS_ACAD_T',	111<<8);
define('SS_ACAD_M',	112<<8);
define('SS_ACAD_P',	113<<8);
define('SS_ACAD_FP',	114<<8);
define('SS_ACAD_R',	115<<8);
define('SS_ACAD_A',	116<<8);
define('SS_ACAD_L',     117<<8);
define('SS_ACAD_SA',    118<<8);
define('SS_ACAD_EV',    119<<8);
define('SS_ACAD_PL',    120<<8);
global $security_sections,$security_areas;
   $security_sections = array(
                    //==[ONLY FOR ACADEMIC] ==//
                    SS_ACAD_T => ("Academic Transactions"),
                    SS_ACAD_M => ("Academic Masters"),
                    SS_ACAD_P => ("Academic Participants"),
                    SS_ACAD_FP => ("Academic Faculty Portal"),
                    SS_ACAD_R => ("Academic Reports"),
                    SS_ACAD_A => ("Academic Attendance"),
                    SS_ACAD_L => ("Academic LMS"),
                    SS_ACAD_SA => ("Academic Seating Arrangment"),
                    SS_ACAD_EV => ("Academic Events"),
                    SS_ACAD_PL => ("Academic placement"),
                );



    $security_areas = array(//===[ACADEMIC SETUP]
                //masters
              'SA_ACAD_CLASS' => array(SS_ACAD_M | 1, _("Class")),//Done
              'SA_ACAD_BATCH' => array(SS_ACAD_M | 2, _("Batch")),//Done
              'SA_ACAD_DEPARTMENT' => array(SS_ACAD_M | 3, _("Department")),//Done
              'SA_ACAD_COURSE_CAT' => array(SS_ACAD_M | 4, _("Course Category")),//Done
              'SA_ACAD_COURSE_TYPE' => array(SS_ACAD_M | 5, _("Course Type")),//Done
              'SA_ACAD_E_LEARN_C' => array(SS_ACAD_M | 6, _("Experential LEarning Component")),//Done
              'SA_ACAD_FEE_CAT' => array(SS_ACAD_M | 7, _("Fee Cat")),//Done
              'SA_ACAD_RATING' => array(SS_ACAD_M | 8, _("Rating")),
              'SA_ACAD_QUESTION' => array(SS_ACAD_M | 9, _("Question")),
              'SA_ACAD_CREDIT' => array(SS_ACAD_M | 10, _("Credit")),
              'SA_ACAD_FEE_HEADS' => array(SS_ACAD_M | 11, _("Fee Heads")),
              'SA_ACAD_SECTION' => array(SS_ACAD_M | 12, _("Section")),
              'SA_ACAD_ROOM' => array(SS_ACAD_M | 13, _("Room")),
              
              'SA_ACAD_TIME_TABLE' => array(SS_ACAD_M | 14, _("Time Table")),
              'SA_ACAD_COURSE' => array(SS_ACAD_M | 15, _("Course")),
              'SA_ACAD_COURSE_FEE' => array(SS_ACAD_M | 33, _("Exam Fee")),
              'SA_ACAD_CORE_COURSE' => array(SS_ACAD_M | 16, _("Core Course")),
              'SA_ACAD_EC_LEARN' => array(SS_ACAD_M | 17, _("Elective Course Learning")),
              'SA_ACAD_E_LEARN' => array(SS_ACAD_M | 18, _("Exprential Learning")),
              'SA_ACAD_REF_GRADE' => array(SS_ACAD_M | 19, _("Refrence Grade")),
              'SA_ACAD_PROG_DESIGN' => array(SS_ACAD_M | 20, _("Program Design")),
              'SA_ACAD_PROG_CAL_YEAR' => array(SS_ACAD_M | 21, _("Program Calendar Year")),
              'SA_ACAD_FEE_STRUCTURE' => array(SS_ACAD_M | 22, _("Fee Structure")),
              'SA_ACAD_SCHOLAR_SHIP' => array(SS_ACAD_M | 23, _("Scholar Ship")),
              'SA_ACAD_PART_SCHOLAR_SHIP' => array(SS_ACAD_M | 24, _("Participants Scholar ship")),
              'SA_ACAD_INTERACTIVE_LEARNING_SESSION' => array(SS_ACAD_M | 25, _("Interactive Learning Sessions")),
              'SA_ACAD_FEED_TEMPLATE' => array(SS_ACAD_M | 26, _("Feed BAck Templates")),
              'SA_ACAD_TERM_VIEW' => array(SS_ACAD_M | 27, _("Term View")),
              'SA_ACAD_CORE_COURSE_LEARN' => array(SS_ACAD_M | 28, _("Core Course Learn")),
              'SA_ACAD_E_LEARN_DESIGN_VIEW' => array(SS_ACAD_M | 29, _("Exprential Learn Desing View")),
              'SA_ACAD_PROG_CAL_VIEW' => array(SS_ACAD_M | 30, _("Program Calendar View")),
              'SA_ACAD_GLOBAL_SET' => array(SS_ACAD_M | 31, _("Global Settings")),
              'SA_ACAD_TERM_MASTER' => array(SS_ACAD_M | 32, _("Term Master")),
	      'SA_ACAD_ROOMMAPPING' => array(SS_ACAD_M | 34, _("Room Mapping")),
              
                
                
                //transaction
              'SA_ACAD_TO_DO_LIST' => array(SS_ACAD_T | 1, _("To Do List")),
                    

                //faculty Portal);
                'SA_ACAD_FACULTY_ALLOTMENT' => array(SS_ACAD_FP | 1, _("Faculty Allotment")),
                'SA_ACAD_LEARNING_ALOTMENT' => array(SS_ACAD_FP | 2, _("Exprential Learning Allotment")),
                'SA_ACAD_EVALUATION_COMPONENT' => array(SS_ACAD_FP | 3, _("Evaluation Component")),
                'SA_ACAD_GRADE_ALLOCATION' => array(SS_ACAD_FP | 4, _("Grade Allocation")),
                'SA_ACAD_E_GRADE_ALLOCATION' => array(SS_ACAD_FP | 5, _("Exprential Grade Allocation")),
                'SA_ACAD_REVIEW_PUBLISH' => array(SS_ACAD_FP | 6, _("Review And Publish")),
                'SA_ACAD_LEARNING_PROJECT' => array(SS_ACAD_FP | 7, _("Exprential Learning Project")),
                'SA_ACAD_LEARN_PROJECT_ALLOCATION' => array(SS_ACAD_FP | 8, _("Exprential Learning Project Allocation ")),
                'SA_ACAD_NOTIFICATION' => array(SS_ACAD_FP | 9, _("Notification Push")),
                'SA_ACAD_ASSIGNMENT' => array(SS_ACAD_FP | 10, _("Participants Assignment")),
                
                //participants
                'SA_ACAD_ENROLLMENT' => array(SS_ACAD_P | 1, _("Participants Enrollment")),
                'SA_ACAD_E_SELECTION' => array(SS_ACAD_P | 2, _("Elective Selection")),
                'SA_ACAD_APPLICATION_FORM' => array(SS_ACAD_P | 3, _("Application Form")),
                
                //Reports
                'SA_ACAD_GRADE_REPORT' => array(SS_ACAD_R | 1, _("Course Wise Grade Report")),
                'SA_ACAD_PENALTIES_REPORT' => array(SS_ACAD_R | 2, _("Course Wise Penalties Report")),
                'SA_ACAD_AFTER_PENALTIES' => array(SS_ACAD_R | 3, _("Course Grade After Penalties")),
                'SA_ACAD_CGPA_GPA' => array(SS_ACAD_R | 4, _("Participants GPA/CGPA")),
                'SA_ACAD_TERM_GRADE_SHEET' => array(SS_ACAD_R | 5, _("Generate Term Wise Grade Sheet")),
                'SA_ACAD_FINAL_GRADE' => array(SS_ACAD_R | 6, _("Generate Final Grade")),
                'SA_ACAD_DIRECT_FINAL_GRADE' => array(SS_ACAD_R | 7, _("Direct Final Grade")),
                
                //Attendance
                'SA_ACAD_P_ATTENDANCE' => array(SS_ACAD_A | 1, _("Participant Attendance")),
                'SA_ACAD_ATTENDANCE_REPORT' => array(SS_ACAD_A | 2, _("Attendance Report")),
                'SS_ACAD_A_JOB_ANNOUNCEMENT' => array(SS_ACAD_A | 3, _("Job Announcement")),
        
        
                //LMS
                'SA_ACAD_BM' => array(SS_ACAD_L | 1, _("Book Managemnt")),
                'SA_ACAD_BH' => array(SS_ACAD_L | 2, _("Hold Book List")),
                'SA_ACAD_BE' => array(SS_ACAD_L | 3, _("Book Extension")),
        
               //Seating Arrangment
                'SS_ACAD_OPERATION' => array(SS_ACAD_SA | 1,("Seating Plan")),
                'SS_ACAD_STUDENT' => array(SS_ACAD_SA | 2,("Add Student")),
                'SS_ACAD_DURATION' => array(SS_ACAD_SA | 3, "Duration"),
                'SS_ACAD_EXAMBATCH' => array(SS_ACAD_SA | 4,"Exam Batch"),
                
                //SA_ACAD_ADDEVENT
                'SA_ACAD_ADDEVENT' => array(SS_ACAD_EV | 1,("Add Event")),
                'SA_ACAD_EVENTLIST' => array(SS_ACAD_EV | 2,("Event List")),
                'SA_ACAD_EVENTCATEGORY' => array(SS_ACAD_EV | 3,("Event Category")), 

                //SS_ACAD_PLACEMENT  
                'SA_ACAD_MASTER_PLACEMENT' => array(SS_ACAD_PL | 1,("Registration Form")),
                'SA_ACAD_MASTER_SELECTION_PROCESS' => array(SS_ACAD_PL | 2,("Selection Process")),
                'SA_ACAD_MASTER_CONFIGURE_SELETION_PROCESS' => array(SS_ACAD_PL | 3,("Configuration Selection Process")),
                'SA_ACAD_MASTER_JOB_ANNOUNCEMENT' => array(SS_ACAD_PL | 4,("Job Announcment")), 
                
            );

class accessLevel{
    
    
    public function __construct() {
        $this->setAccessLevel();
    }
    
    
        private function setAccessLevel(){ 
            global $security_areas;
        $k = $j = 0; //row colour counter
	$ext = $sec = $m = -1;
        
	foreach($this->sort_areas($security_areas) as $area =>$parms ) {
		$newsec = ($parms[0]>>8)&0xff;
		$newext  = $parms[0]>>16;             
                       
		define($area,$parms[0]);
		
	}  
        
     
 
    }
    
    private function sort_areas($areas)
{
	$old_order = 0;
	foreach($areas as $key => $area) {
		$areas[$key][] = $old_order++;
	}
	//uasort($areas,'comp_areas');
	return $areas;
}

public function setAccess($Action = '',$role_id = 'NA'){
  //echo "<pre>";print_r(count($_SESSION['admin_login']['admin_login']->role_set));exit;
    try{
    if(empty($Action)){
        throw new Exception("Action should not be empty in SetAccess Function");
    }
        else{
                if((!in_array(constant($Action), $_SESSION['admin_login']['admin_login']->role_set) && count($_SESSION['admin_login']['admin_login']->role_set) ) ){
                   
                    throw new Exception("Access Denied By System Administrator");
                }
        
            }
    }
    catch(Exception $e){
         echo $e->getMessage();die;
    }
    
}


    

}

?>