<?php

class GradeAllocationController extends Zend_Controller_Action {

    private $_siteurl = null;
    private $_db = null;
    private $_flashMessenger = null;
    private $_authontication = null;
    private $_agentsdata = null;
    private $_usersdata = null;
    private $_act = null;
    private $_adminsettings = null;
	Private $_unit_id = null;
        private $login_storage = NULL;
    private $roleConfig = NULL;
    public $cur_datetime = NULL;
    private $accessConfig =NULL;


    public function init() {
        $zendConfig = new Zend_Config_Ini(
                APPLICATION_PATH . '/configs/application.ini', APPLICATION_ENV);
                require_once APPLICATION_PATH . '/configs/access_level.inc';
                        
        $this->accessConfig = new accessLevel();
        $config = $zendConfig->mainconfig->toArray();
        $this->view->mainconfig = $config;
        $this->_action = $this->getRequest()->getActionName();
        //access role id
        $this->roleConfig = $config_role = $zendConfig->role_administrator->toArray();
        $this->view->administrator_role = $config_role;
        $storage = new Zend_Session_Namespace("admin_login");					
        $this->login_storage = $data = $storage->admin_login;
        $this->view->login_storage = $data;
        //print_r($data);exit;
        if( isset($data) ){
                $this->view->role_id = $data->role_id;
                $this->view->login_empl_id = $data->empl_id;
        }
        if ($this->_action == "login" || $this->_action == "forgot-password") {
            $this->_helper->layout->setLayout("adminlogin");
        } else {
            $this->_helper->layout->setLayout("layout");
        }
        $this->_act = new Application_Model_Adminactions();
        $this->_db = Zend_Db_Table::getDefaultAdapter();
        $this->_flashMessenger = $this->_helper->FlashMessenger;
        $this->authonticate();
        $this->cur_datetime = date('Y-m-d H:i:s');

    }

    protected function authonticate() {
        $storage = new Zend_Session_Namespace("admin_login");
        $data = $storage->admin_login;
          if($data->role_id == 0)
            $this->_redirect('student-portal/grade-sheet');
        if (!$data && $this->_action != 'login' &&
                $this->_action != 'forgot-password') {
            $this->_redirect('index/login');
            return;
        }
        if ($this->_action != 'forgot-password') {
            $this->_authontication = $data;
            $this->_agentsdata = $storage->agents_data;
        }
    }

    public function indexAction() {        
        $this->view->action_name = 'gradeallocation';
        $this->view->sub_title_name = 'gradeallocation';
        $this->accessConfig->setAccess('SA_ACAD_GRADE_ALLOCATION');
        $GradeAllocation_model = new Application_Model_GradeAllocation();
		$GradeAllocation_form = new Application_Form_GradeAllocation();
		$GradeAllocatonItems_model = new Application_Model_GradeAllocationItems();
		$EvaluationComponentsItems_model = new Application_Model_EvaluationComponentsItems();
                $student_info = new Application_Model_StudentPortal();
		$grade_id = $this->_getParam("id");
		//print_r($feehead_id);die;
        $type = $this->_getParam("type");
		$this->view->type = $type;
        $this->view->form = $GradeAllocation_form;
        
		
        switch ($type) {
            case "add":    
                                $messages = $this->_flashMessenger->getMessages();
                $this->view->messages = $messages;
                if ($this->getRequest()->isPost()) {
                    if ($GradeAllocation_form->isValid($this->getRequest()->getPost())) {
                        $data = $GradeAllocation_form->getValues();
						$data['term_id'] = $this->getRequest()->getPost('term_id');
						$data['course_id'] = $this->getRequest()->getPost('course_id');
						//$data['component_id'] = $this->getRequest()->getPost('component_id');
				//Add column "added_by" & "added_date" & "added_by_ip_address" in grade_allocation_master
                                            $studentNum =$student_info->getstudents($data['academic_id']);
				if(count($studentNum)>0){
                                $data['added_by'] = $this->login_storage->id;
                                $data['added_date'] = $this->cur_datetime;
                                $data['added_by_ip_address'] = $_SERVER['REMOTE_ADDR'];		
                        	$last_insert_id = $GradeAllocation_model->insert($data);	
							$grade = $this->getRequest()->getPost('grade');
							//print_r($grade);die;
                                                        $academic_id = $this->getRequest()->getPost('academic_id');
                                                        $Coursewisepenalties_model= new Application_Model_Coursewisepenalties();
                                                        $penalty = $Coursewisepenalties_model->getStudentAbsenceRecord($academic_id, $data['term_id']);
                                                        $penalty_master = array('academic_id' => $academic_id, 'term_id' => $data['term_id'], 'status' => 0);
                                                        if(empty($penalty)){
                                                            $penalty_master_id = $Coursewisepenalties_model->insert($penalty_master);
                                                        }
                                                        else{
                                                            $penalty_master_id = $penalty['id'];
                                                        }
                                                        $CoursewisepenaltiesItems_model = new Application_Model_CoursewisepenaltiesItems();
							foreach(array_filter($grade['student_id']) as $k => $student_id){
								$grade_data['grade_allocation_id'] = $last_insert_id;
								$grade_data['student_id'] = $student_id;
								$grade_data['grade_value'] = implode(",",$grade['grade_value_'.$student_id.'']);
								
								$grade_data['component_id'] = implode(",",$grade['component_id']);
								//print_r($grade_data);
								$GradeAllocatonItems_model->insert($grade_data);
                                                                $penalty_item = $CoursewisepenaltiesItems_model->getStudentTermPenalty($penalty_master_id, $data['term_id'], $student_id);
                                                                $penalty_value = $this->getRequest()->getPost('student_penalties_'.$student_id);
                                                                //Fetching Credit value
                                                                $Corecourselearning_model = new Application_Model_Corecourselearning();
                                                                $cc_detail = $Corecourselearning_model->getCoreCouseDetailByTermAcademicCourse($academic_id, $data['term_id'], $data['course_id']);
                                                                if(empty($penalty_item)){//If penalty item row not exists, insert it as a new row
                                                                    $item_data['id'] = $penalty_master_id;
                                                                    $item_data['term_id'] = $data['term_id'];
                                                                    $item_data['student_id'] = 	$student_id;
                                                                    $item_data['academic_courses'] = $data['course_id'];
                                                                    $item_data['academic_credits'] = $cc_detail['credit_value'];
                                                                    $item_data['absence'] = $penalty_value;
                                                                    $CoursewisepenaltiesItems_model->insert($item_data);
                                                                }
                                                                else{//If penalty item exist, update row
                                                                    $penalty_courses = explode(',', $penalty_item['academic_courses']);
                                                                    $penalty_credits = explode(',', $penalty_item['academic_credits']);
                                                                    $penalty_absense = explode(',', $penalty_item['absence']);
                                                                    
                                                                    $penalty_courses[] = $data['course_id'];
                                                                    $penalty_credits[] = $penalty_credits;
                                                                    $penalty_absense[] = $penalty_value;
                                                                    
                                                                    $penalty_courses = implode(',', $penalty_courses);
                                                                    $penalty_credits = implode(',', $penalty_credits);
                                                                    $penalty_absense = implode(',', $penalty_absense);
                                                                    
                                                                    $item_data['academic_courses'] = $penalty_courses;
                                                                    $item_data['academic_credits'] = $penalty_credits;
                                                                    $item_data['absence'] = $penalty_absense;
                                                                    $where = 'item_id = '.$penalty_item['item_id'];
                                                                    //print_r($item_data);echo $where;exit;
                                                                    $CoursewisepenaltiesItems_model->update($item_data,$where);
                                                                }
                                                                
							}	//die;
					
                        $this->_flashMessenger->addMessage('Grade Allocation Successfully added');
                        $this->_redirect('grade-allocation/index');
                                }
                                else{
                        $this->_flashMessenger->addMessage('There is no Student in this batch !');
                        $this->_redirect('grade-allocation/index');
                                }

                        
						 
						}
                    }

                
                break;
            case 'edit': 
                $result1 = $GradeAllocation_model->getRecord($grade_id);
                
                
			
				//$this->view->result_grade = $result_grade;
				//echo '<pre>',print_r($result_grade);die;
				$GradeAllocation_form->populate($result1);
				$this->view->grade_allocate_id = $grade_id;
                                
                                //Checking if it approved by D
                                $GradeAllocationReportItems_model = new Application_Model_GradeAllocationReportItems();
                                $isExist1 = $GradeAllocationReportItems_model->isGradeReportPublished($result1['academic_id'], $result1['term_id'], $result1['course_id']);
                                $this->view->isGradeReportPublished = $isExist1;
                                $this->view->grade_detail = $result1;
					
				//$this->view->result = $result;
                if ($this->getRequest()->isPost()) {
                    if ($GradeAllocation_form->isValid($this->getRequest()->getPost())) {
                        
                        /************ Saving Previous data in Log table *************/
                        $grade_log = array();
                        $grade_log['master'] = $result1;
                        $grade_log['items'] = $GradeAllocatonItems_model->getRecords($grade_id);
                        $GradeAllocationLog = new Application_Model_GradeAllocationLog();
                        $log_col_val = array(
                            'grade_id' => $grade_id,
                            'grade_detail' => json_encode($grade_log),
                            'grade_type' => 'CORE',
                            'added_date' => $this->cur_datetime,
                            'added_by' => $this->login_storage->id,
                            'ip_address' => $_SERVER['REMOTE_ADDR']
                        );
                        $GradeAllocationLog->insert($log_col_val);                        
                        /************ Saving Previous data in Log table *************/
                        $data = $GradeAllocation_form->getValues();
						$data['term_id'] = $this->getRequest()->getPost('term_id');
						$data['course_id'] = $this->getRequest()->getPost('course_id');
                                                $academic_id = $this->getRequest()->getPost('academic_id');      
                                                $isFinalSubmit = $this->getRequest()->getPost('final_submit'); 
                                                if($isFinalSubmit){
                                                    $data['published_by_faculty'] = 1;
                                                    $data['published_by_faculty_date'] = $this->cur_datetime;
                                                }
						//$data['component_id'] = $this->getRequest()->getPost('component_id');
                        $GradeAllocation_model->update($data, array('grade_id=?' => $grade_id));
                        
                        //Inserting/Updating Penalties
                                                                $Coursewisepenalties_model= new Application_Model_Coursewisepenalties();
                                                                $penalty = $Coursewisepenalties_model->getStudentAbsenceRecord($academic_id, $data['term_id']);
                                                                $penalty_master = array('academic_id' => $academic_id, 'term_id' => $data['term_id'], 'status' => 0);
                                                                if(empty($penalty)){
                                                                    $penalty_master_id = $Coursewisepenalties_model->insert($penalty_master);
                                                                }
                                                                else{
                                                                    $penalty_master_id = $penalty['id'];
                                                                }
                                                                
                                                                $CoursewisepenaltiesItems_model = new Application_Model_CoursewisepenaltiesItems();
						
						$grade = $this->getRequest()->getPost('grade');
                                                
						
						$GradeAllocatonItems_model ->trashItems($grade_id);
							foreach(array_filter($grade['student_id']) as $k => $student_id){
                                                           
								$grade_data['grade_allocation_id'] = $grade_id;
								$grade_data['student_id'] = $student_id;
								$grade_data['grade_value'] = implode(",",$grade['grade_value_'.$student_id.'']);
								$grade_data['component_id'] = implode(",",$grade['component_id']);
								$GradeAllocatonItems_model->insert($grade_data);
                                                                
                                                                //print_r($penalty_courses);print_r($penalty_credits);exit;
                                                                $penalty_value = $this->getRequest()->getPost('student_penalties_'.$student_id);
                                                                //Getting Credit Value of the Course
                                                                $Corecourselearning_model = new Application_Model_Corecourselearning();
                                                                $cc_detail = $Corecourselearning_model->getCoreCouseDetailByTermAcademicCourse($academic_id, $data['term_id'], $data['course_id']);                                                               
                                                                $penalty_item = $CoursewisepenaltiesItems_model->getStudentTermPenalty($penalty_master_id, $data['term_id'], $grade_data['student_id']);
                                                                if(empty($penalty_item)){//If penalty item row not exists, insert it as a new row
                                                                    $item_data['id'] = $penalty_master_id;
                                                                    $item_data['term_id'] = $data['term_id'];
                                                                    $item_data['student_id'] = 	$student_id;
                                                                    $item_data['academic_courses'] = $data['course_id'];
                                                                    $item_data['academic_credits'] = $cc_detail['credit_value'];
                                                                    $item_data['absence'] = $penalty_value;
                                                                    $CoursewisepenaltiesItems_model->insert($item_data);
                                                                }
                                                                else{
                                                                $penalty_item_id = $penalty_item['item_id'];//Getting unique id of penalty item
                                                                
                                                                $penalty_item['academic_courses'] = trim($penalty_item['academic_courses']);
                                                                if(empty($penalty_item['academic_courses'])){
                                                                    $penalty_courses = array();
                                                                    $penalty_credits = array();
                                                                    $penalty_absense = array();
                                                                }
                                                                else{
                                                                    $penalty_courses = explode(',', $penalty_item['academic_courses']);
                                                                    $penalty_credits = explode(',', $penalty_item['academic_credits']);
                                                                    $penalty_absense = explode(',', $penalty_item['absence']);
                                                                }
                                                                
                                                                 
                                                                if(empty($penalty_courses)){//If there is no value in column, insert it simply
                                                                    $penalty_courses[] = $data['course_id'];                                                                    
                                                                    $penalty_absense[] = $penalty_value;
                                                                    $penalty_credits[] = $cc_detail['credit_value'];
                                                                }
                                                                else{
                                                                    if(in_array($data['course_id'], $penalty_courses))//If course id already exits, it means penalty is already entered for this course,so we need to replace penalty value on same order
                                                                    {
                                                                        
                                                                        $course_id_pos = array_search($data['course_id'], $penalty_courses);//Find the index position of course id
                                                                        $penalty_courses[$course_id_pos] = $data['course_id'];
                                                                        $penalty_credits[$course_id_pos] = $cc_detail['credit_value'];
                                                                        $penalty_absense[$course_id_pos] = $penalty_value;
                                                                        
                                                                    }
                                                                    else{//If Course_id alredy exist, replace its value. In this case, no need to update course_id and credit_value
                                                                        $penalty_courses[] = $data['course_id'];
                                                                        $penalty_credits[] = $penalty_credits;
                                                                        $penalty_absense[] = $penalty_value;
                                                                    }
                                                                }
                                                                //print_r($penalty_courses);print_r($penalty_credits);exit;
                                                                $penalty_courses = implode(',', $penalty_courses);
                                                                $penalty_credits = implode(',', $penalty_credits);
                                                                $penalty_absense = implode(',', $penalty_absense);
                                                                $item_data['academic_courses'] = $penalty_courses;
                                                                $item_data['academic_credits'] = $penalty_credits;
                                                                $item_data['absence'] = $penalty_absense;
                                                                $where = 'item_id = '.$penalty_item_id;
                                                                //print_r($item_data);echo $where;exit;
                                                                $CoursewisepenaltiesItems_model->update($item_data,$where);
                                                                }
                                                                
							}	
						
                        $this->_flashMessenger->addMessage('Details Updated Successfully');
                        $this->_redirect('grade-allocation/index');
                    } else {         
                    }
                }
                break;
            case 'delete':
                $data['status'] = 2;
                if ($grade_id){
                    $GradeAllocation_model->update($data, array('grade_id=?' => $grade_id));
					$this->_flashMessenger->addMessage('Details Deleted Successfully');
					$this->_redirect('grade-allocation/index');
				}
                break;
            default:
                $messages = $this->_flashMessenger->getMessages();
                $this->view->messages = $messages;
                $role_id = $this->login_storage->role_id;
                $empl_id = $this->login_storage->empl_id;
                //echo $empl_id;exit;
                if(in_array($role_id, $this->roleConfig)){                    
                    $result = $GradeAllocation_model->getRecords();
                }
                else{
                    $result = $GradeAllocation_model->getRecordsByFacultyId($empl_id);
                }
              
                $page = $this->_getParam('page', 1);
                $paginator_data = array(
                    'page' => $page,
                    'result' => $result
                );
                $this->view->paginator = $this->_act->pagination($paginator_data);
                break;
        }
    }
	public function ajaxGetGradeDetailsAction(){
		 $this->_helper->layout->disableLayout();
        if ($this->_request->isPost () && $this->getRequest ()->isXmlHttpRequest ()) {
			 $department_id = $this->_getParam("department_id");
			 $employee_id = $this->_getParam("employee_id");
			 $academic_year_id = $this->_getParam("academic_year_id");
                         $course_id = $this->_getParam('course_id');
                         $term_id = $this->_getParam("term_id");
				$EmployeeAllotment_model= new Application_Model_EmployeeAllotment();
				$result= $EmployeeAllotment_model->getEmployeeTerms($academic_year_id,$department_id,$employee_id,$term_id,$course_id);
				$this->view->result = $result;
		}
	}
	
	
	public function ajaxGetGradeDetailsPrintAction(){
		
		   $GradeAllocationReport_model = new Application_Model_GradeAllocationReport();
		$GradeAllocationReport_form = new Application_Form_GradeAllocationReport();
		$grade_report_id = $this->_getParam("id");
		$this->view->grade_report_id  = $grade_report_id;
		//print_r($grade_report_id); die;
		$type = $this->_getParam("type");
		$this->view->type = $type;
		$this->view->form = $GradeAllocationReport_form;
		 $this->_helper->layout->disableLayout();
        if ($this->_request->isPost () && $this->getRequest ()->isXmlHttpRequest ()) {
			
				$GradeAllocationReport_model= new Application_Model_GradeAllocationReport();
				$result= $GradeAllocationReport_model->getRecord($grade_report_id);
                               
				 $department_id = $result['department_id'];
			// print_r($department_id);die;
			 $employee_id = $result['employee_id'];
			 $academic_year_id = $result['academic_id'];
                         $term_id = $result['term_id'];
                         $course_id = $result['course_id'];
				$EmployeeAllotment_model= new Application_Model_EmployeeAllotment();
				$result1= $EmployeeAllotment_model->getEmployeeTerms($academic_year_id,$department_id,$employee_id,$term_id,$course_id);
				
				$this->view->result1 = $result1;
		}
	}
	
	
	//pdf Print
	
		public function gradePdfAction() {
        
		$GradeAllocationReport_model = new Application_Model_GradeAllocationReport();
		$GradeAllocationReport_form = new Application_Form_GradeAllocationReport();
		$grade_report_id = $this->_getParam("id");
		$this->view->grade_report_id  = $grade_report_id;
		//print_r($grade_report_id); die;
		$GradeAllocationReport_model= new Application_Model_GradeAllocationReport();
				$result= $GradeAllocationReport_model->getRecord($grade_report_id);
				//print_r($result);die;
				 $department_id = $result['department_id'];
			// print_r($department_id);die;
			 $employee_id = $result['employee_id'];
			 $academic_year_id = $result['academic_id'];
				$EmployeeAllotment_model= new Application_Model_EmployeeAllotment();
				$result1= $EmployeeAllotment_model->getEmployeeTerms($academic_year_id,$department_id,$employee_id);
				//print_r($result1);die;
				$this->view->result1 = $result1;
				
			$pdfheader = $this->view->render('grade-allocation/pdfheader.phtml');
			$pdffooter = $this->view->render('grade-allocation/pdffooter.phtml');				
			$htmlcontent = $this->view->render('grade-allocation/grade-pdf.phtml');
			$this->_act->generatePdf($pdfheader, $pdffooter, $htmlcontent, "Grade Allocation Report Details");
			
    }
	
	
	public function ajaxGetGradeAllocationAction(){ 
         $this->_helper->layout->disableLayout();
         if ($this->_request->isPost () && $this->getRequest ()->isXmlHttpRequest()) { 
			$academic_year_id = $this->_getParam("academic_year_id");
			$year = $this->_getParam("year");
			$GradeAllocation_model = new Application_Model_GradeAllocation();
			$TermMaster_model = new Application_Model_TermMaster();
			$Corecourselearning_model = new Application_Model_Corecourselearning();
			$StudentPortal_model = new Application_Model_StudentPortal();
           if($academic_year_id)
            { 
			  $Category_data=$StudentPortal_model->getstudentsyearwise($academic_year_id,$year);
			  $this->view->Category_data = $Category_data;
			  $Term_data=$TermMaster_model->getGradeTermName($academic_year_id);
			  //print_r($Term_data); die;
			  //$this->view->Category_data = $Category_data;
			/*  foreach($Term_data as $k=>$val){
				 $term_id = $val['term_id'];
			  $Course_data=$Corecourselearning_model->getGradeCourseName($academic_year_id,$term_id);
			//  print_r($Course_data);die;
			  } */
			  //print_r($Course_data); die;
			  $this->view->Term_data = $Term_data;

		}
       }		 
    }
	
	
	public function ajaxGetStudentDetailsAction(){ 
         $this->_helper->layout->disableLayout();
         if ($this->_request->isPost () && $this->getRequest ()->isXmlHttpRequest()) { 
			$academic_year_id = $this->_getParam("academic_year_id");
			$employee_id = $this->_getParam("employee_id");
			$department_id = $this->_getParam("department_id");
			$course_id = $this->_getParam("course_id");
			$term_id = $this->_getParam("term_id");
			$grade_allocate_id = $this->_getParam("grade_allocate_id");
			//print_r($term_id);die;
			$GradeAllocation_model = new Application_Model_GradeAllocation();
			$EvaluationComponentsItems_model = new Application_Model_EvaluationComponentsItems();
			$weight = $EvaluationComponentsItems_model->getWeightage($academic_year_id,$employee_id,$department_id,$course_id,$term_id);
                        
                        $ReferenceGradeMasterItems_model = new Application_Model_ReferenceGradeMasterItems();
                        $ref_grades = $ReferenceGradeMasterItems_model->getRecordsByAcademicId($academic_year_id);
                        $this->view->ref_grades = $ref_grades;
			
			$this->view->weightage = $weight;
			$StudentPortal_model = new Application_Model_StudentPortal();
                        
                        //Fetch Penalty items
                        $penalty_all_items = array();
                        $Coursewisepenalties_model= new Application_Model_Coursewisepenalties();
                        $penalty = $Coursewisepenalties_model->getStudentAbsenceRecord($academic_year_id, $term_id);
                        if(!empty($penalty)){
                            $penalty_master_id = $penalty['id'];
                            $CoursewisepenaltiesItems_model = new Application_Model_CoursewisepenaltiesItems();
                            $penalty_all_items = $CoursewisepenaltiesItems_model->getPenaltyItemByMasterIdTermId($penalty_master_id, $term_id);
                        }
                        
                        $this->view->penalty_all_items = $penalty_all_items;
                        $this->view->course_id = $course_id;
                        
			if($grade_allocate_id){
				$result = $GradeAllocation_model->getStudentRecords($grade_allocate_id);
			//	print_r($result);die;
				$this->view->result = $result;
				
			}else{
			   if($academic_year_id){
					  $Category_data=$StudentPortal_model->getstudentsdetails($academic_year_id);
					  $this->view->category_data = $Category_data;
			   }

				
			}
       }
	}

		public function ajaxGetTermsAction(){
		 $this->_helper->layout->disableLayout();
        if ($this->_request->isPost () && $this->getRequest ()->isXmlHttpRequest ()) {
			 $department_id = $this->_getParam("department_id");
			 $employee_id = $this->_getParam("employee_id");
			 $academic_year_id = $this->_getParam("academic_year_id");
			 $grade_allocation_id = $this->_getParam("grade_allocate_id");
			
			 if($grade_allocation_id){
				
				  $GradeAllocation_model = new Application_Model_GradeAllocation();
				  $grade_allocate_result =  $GradeAllocation_model->getRecord($grade_allocation_id);
				 
				 
			 }
			 
				$EmployeeAllotment_model= new Application_Model_EmployeeAllotment();
				$result= $EmployeeAllotment_model->getTerms($academic_year_id,$department_id,$employee_id);
				echo '<div class="col-sm-3 employee_class">';
                echo  '<div class="form-group">';
                 echo  '<label class="control-label">Terms</label>';
				echo '<select type="text" name="term_id" id="term_id" class="form-control">';
				echo '<option value="">Select</option>';
				foreach($result as $k => $val)
				{
					$selected ='';
					if($k == $grade_allocate_result['term_id']){
						
						$selected = "selected";
					}	
					echo '<option value="'.$k.'" '.$selected.' >'.$val.'</option>';	
					
				}
				echo '</select>';
				echo '</div></div>';
				
		}die;
		
		
	}
	public function ajaxGetCoursesAction()
	{
		 $this->_helper->layout->disableLayout();
        if ($this->_request->isPost () && $this->getRequest ()->isXmlHttpRequest ()) {
			 $department_id = $this->_getParam("department_id");
			 $employee_id = $this->_getParam("employee_id");
			 $academic_year_id = $this->_getParam("academic_year_id");
			 $term_id = $this->_getParam("term_id");
			 $grade_allocation_id = $this->_getParam("grade_allocate_id");
			 if($grade_allocation_id){
				  $GradeAllocation_model = new Application_Model_GradeAllocation();
				  $grade_allocate_result =  $GradeAllocation_model->getRecord($grade_allocation_id);
				 
			 }
				$EmployeeAllotment_model= new Application_Model_EmployeeAllotment();
				$result= $EmployeeAllotment_model->getComponentCourses($academic_year_id,$department_id,$employee_id,$term_id);
				echo '<div class="col-sm-3 employee_class">';
                echo  '<div class="form-group">';
                 echo  '<label class="control-label">Courses</label>';
				echo '<select type="text" name="course_id" id="course_id" class="form-control">';
				echo '<option value="">Select</option>';
				foreach($result as $k => $val)
				{
					$selected ='';
					if($k == $grade_allocate_result['course_id']){
						
						$selected = "selected";
					}
					echo '<option value="'.$k.'" '.$selected.'>'.$val.'</option>';	
					
				}
				echo '</select>';
				echo '</div></div>';
		
	}die;
	}
	public function ajaxGetCoursesEditAction()
	{
		 $this->_helper->layout->disableLayout();
        if ($this->_request->isPost () && $this->getRequest ()->isXmlHttpRequest ()) {
			 $department_id = $this->_getParam("department_id");
			 $employee_id = $this->_getParam("employee_id");
			 $academic_year_id = $this->_getParam("academic_year_id");
			 $term_id = $this->_getParam("term_id");
			 $grade_allocation_id = $this->_getParam("grade_allocate_id");
			 if($grade_allocation_id){
				  $GradeAllocation_model = new Application_Model_GradeAllocation();
				  $grade_allocate_result =  $GradeAllocation_model->getRecord($grade_allocation_id);
				 
			 }
				$EmployeeAllotment_model= new Application_Model_EmployeeAllotment();
				$result= $EmployeeAllotment_model->getEditComponentCourses($academic_year_id,$department_id,$employee_id,$term_id);
				echo '<div class="col-sm-3 employee_class">';
                echo  '<div class="form-group">';
                 echo  '<label class="control-label">Courses</label>';
				echo '<select type="text" name="course_id" id="course_id" class="form-control">';
				echo '<option value="">Select</option>';
				foreach($result as $k => $val)
				{
					$selected ='';
					if($k == $grade_allocate_result['course_id']){
						
						$selected = "selected";
					}
					echo '<option value="'.$k.'" '.$selected.'>'.$val.'</option>';	
					
				}
				echo '</select>';
				echo '</div></div>';
		
	}die;
	}
/// ramesh  //	
	public function ajaxGetEvaluationComponentsAction(){
		$this->_helper->layout->disableLayout();
        if ($this->_request->isPost () && $this->getRequest ()->isXmlHttpRequest ()) {
			 $department_id = $this->_getParam("department_id");
			 $employee_id = $this->_getParam("employee_id");
			 $academic_year_id = $this->_getParam("academic_year_id");
			 $term_id = $this->_getParam("term_id");
			 $course_id = $this->_getParam("course_id");
			 $grade_allocation_id = $this->_getParam("grade_allocate_id");
			 if($grade_allocation_id){
				  $GradeAllocation_model = new Application_Model_GradeAllocation();
				  $grade_allocate_result =  $GradeAllocation_model->getRecord($grade_allocation_id);
				 
			 }
				$EvaluationComponents_model= new Application_Model_EvaluationComponents();
				$result= $EvaluationComponents_model->getComponents($academic_year_id,$department_id,$employee_id,$term_id,$course_id);
				/*echo '<div class="col-sm-3 employee_class">';
                echo  '<div class="form-group">';
                 echo  '<label class="control-label">Components</label>';
				echo '<select type="text" name="component_id" id="component_id" class="form-control">';
				echo '<option value="">Select</option>';
				foreach($result as $k => $val)
				{
					$selected ='';
					if($k == $grade_allocate_result['component_id']){
						
						$selected = "selected";
					}
					echo '<option value="'.$k.'" '.$selected.'>'.$val.'</option>';	
					
				}
				echo '</select>';
				echo '</div></div>';*/
		
	}die;
		
	}
	
	public function ajaxGetGradeEvaluationComponentsAction(){
		$this->_helper->layout->disableLayout();
        if ($this->_request->isPost () && $this->getRequest ()->isXmlHttpRequest ()) {
			 $department_id = $this->_getParam("department_id");
			 $employee_id = $this->_getParam("employee_id");
			 $academic_year_id = $this->_getParam("academic_year_id");
			 $term_id = $this->_getParam("term_id");
			 $course_id = $this->_getParam("course_id");
			 
				$EvaluationComponents_model= new Application_Model_EvaluationComponents();
				$result= $EvaluationComponents_model->getGradeAddComponents($academic_year_id,$department_id,$employee_id,$term_id,$course_id);
				/*echo '<div class="col-sm-3 employee_class">';
                echo  '<div class="form-group">';
                 echo  '<label class="control-label">Components</label>';
				echo '<select type="text" name="component_id" id="component_id" class="form-control">';
				echo '<option value="">Select</option>';
				foreach($result as $k => $val)
				{
					
					echo '<option value="'.$k.'">'.$val.'</option>';	
					
				}
				echo '</select>';
				echo '</div></div>';*/
		
	}die;
		
	}
/// ramesh  //		
	public function ajaxGetEmployeeAction(){
		 $this->_helper->layout->disableLayout();
        if ($this->_request->isPost () && $this->getRequest ()->isXmlHttpRequest ()) {
			 $department_id = $this->_getParam("department_id");
			// print_r($academic_id); die;
			 $HRMModel_model= new Application_Model_HRMModel();
			 $result= $HRMModel_model->getEmployee($department_id);
                                $val = in_array($_SESSION['admin_login']['admin_login']->role_id, $this->roleConfig);
                                $empl_id = $_SESSION['admin_login']['admin_login']->empl_id;
                                
                                
			  if($empl_id && $val != 1){
                foreach($result as $key => $value)
                {
                              $result = array();
                       if($empl_id == $key){
                           $result[$key] = $value;
                           break;
                       }
                }
                 }
                 
                
				echo '<option value="">Select</option>';
				foreach($result as $k => $val){
					echo '<option value="'.$k.'" >'.$val.'</option>';	
				}	
			 
		}die;
	}

     public function gradeAllocationReportAction(){
	     
	    $this->view->action_name = 'Grade Allocation Report';
	    $this->view->sub_title_name = 'grade-allocation-report';
            $this->accessConfig->setAccess('SA_ACAD_REVIEW_PUBLISH');
        $GradeAllocationReport_model = new Application_Model_GradeAllocationReport();
		$GradeAllocationReport_form = new Application_Form_GradeAllocationReport();
		$GradeAllocationReportItems_model = new Application_Model_GradeAllocationReportItems();
		//$GradeAllocatonReportItems_model = new Application_Model_GradeAllocationReportItems();
		$grade_report_id = $this->_getParam("id");
		$type = $this->_getParam("type");
		$this->view->type = $type;
		$this->view->form = $GradeAllocationReport_form;
		
        switch ($type) {
            case "add":    
                if ($this->getRequest()->isPost()) {
                    if ($GradeAllocationReport_form->isValid($this->getRequest()->getPost())) {
                        $data = $GradeAllocationReport_form->getValues();
				//Add column "added_by" & "added_date" & "added_by_ip_address" in grade_allocation_master
                        $data['course_id'] = $this->getRequest()->getPost('course_id');
                        $data['added_by'] = $this->login_storage->id;
                        $data['added_date'] = $this->cur_datetime;
                        $data['added_by_ip_address'] = $_SERVER['REMOTE_ADDR'];	
						$last_insert_id = $GradeAllocationReport_model->insert($data);
						$EmployeeAllotment_model= new Application_Model_EmployeeAllotment();
						$employee_result = $EmployeeAllotment_model->getEmployeeTerms($data['academic_id'],$data['department_id'],$data['employee_id'],$data['term_id'], $data['course_id']);
						for($i=0;$i<count($employee_result);$i++)
						{
						$grade = $_POST['grade'];
									
						for($k=0;$k<count($_POST['grade']['student_id_'.$employee_result[$i]['term_id'].'_'.$employee_result[$i]['course_id']]);$k++){
						$item_data['report_id'] = $last_insert_id;
						$item_data['term_id']=$employee_result[$i]['term_id'];
						$item_data['course_id']=$employee_result[$i]['course_id'];
						$item_data['student_id'] = $grade['student_id_'.$employee_result[$i]['term_id'].'_'.$employee_result[$i]['course_id']][$k];
						$item_data['component_grades'] = $grade['grades_'.$employee_result[$i]['term_id'].'_'.$employee_result[$i]['course_id']][$k];
						$item_data['component_weightages'] = $grade['weightages_'.$employee_result[$i]['term_id'].'_'.$employee_result[$i]['course_id']][$k];
						$item_data['grade_point'] = $grade['grade_point_'.$employee_result[$i]['term_id'].'_'.$employee_result[$i]['course_id']][$k];
						
						$GradeAllocationReportItems_model->insert($item_data);
												
						   }
						}
						
						//echo '<pre>'; print_r($grade); die;
						/* $data['term_id'] = $this->getRequest()->getPost('term_id');
						$data['course_id'] = $this->getRequest()->getPost('course_id');
						$data['component_id'] = $this->getRequest()->getPost('component_id');
                        	$last_insert_id = $GradeAllocation_model->insert($data);	
							$grade = $this->getRequest()->getPost('grade');
							foreach(array_filter($grade['student_id']) as $k => $student_id){
								$grade['grade_allocation_id'] = $last_insert_id;
								$grade['student_id'] = $student_id;
								$grade['grade_value'] = $grade['grade_value'][$k];
								$GradeAllocatonItems_model->insert($grade);
							}	 */
					
                        $this->_flashMessenger->addMessage('Grade Report Successfully added');

                        $this->_redirect('grade-allocation/grade-allocation-report');
						 
						}
                    }

                
                break;
            /* case 'edit': 
                $result = $GradeAllocationReport_model->getRecord($grade_report_id);
				$GradeAllocationReport_form->populate($result);
				//$this->view->grade_allocate_id = $grade_report_id;
				$this->view->result = $result;
                if ($this->getRequest()->isPost()) {
                    if ($GradeAllocationReport_form->isValid($this->getRequest()->getPost())) {
                        $data = $GradeAllocationReport_form->getValues();
						//$data['term_id'] = $this->getRequest()->getPost('term_id');
						//$data['course_id'] = $this->getRequest()->getPost('course_id');
						//$data['component_id'] = $this->getRequest()->getPost('component_id');
                        $GradeAllocationReport_model->update($data, array('grade_report_id=?' => $grade_report_id));
						
						$grade = $this->getRequest()->getPost('grade');
						$GradeAllocatonItems_model ->trashItems($grade_id);
							foreach(array_filter($grade['student_id']) as $k => $student_id){
								$grade['grade_allocation_id'] = $grade_id;
								$grade['student_id'] = $student_id;
								$grade['grade_value'] = $grade['grade_value'][$k];
								$GradeAllocatonItems_model->insert($grade);
							} 
						
                        $this->_flashMessenger->addMessage('Grade Report Updated Successfully');
                        $this->_redirect('grade-allocation/grade-allocation-report');
                    } else {         
                    }
                }
                break;
            case 'delete':
                $data['status'] = 2;
                if ($structure_id){
                    $GradeAllocation_model->update($data, array('grade_id=?' => $grade_id));
					$this->_flashMessenger->addMessage('Details Deleted Successfully');
					$this->_redirect('grade-allocation/index');
				}
                break; */
            default:
                $messages = $this->_flashMessenger->getMessages();
                $this->view->messages = $messages;
                $role_id = $this->login_storage->role_id;
                $empl_id = $this->login_storage->empl_id;
                //echo $empl_id;exit;
                                  
                    $result = $GradeAllocationReport_model->getRecords();
                    foreach($result as $key => $course)
                    {
                       
                       $result[$key]['course_id'] = $GradeAllocationReport_model->getCourseInfo($course['course_id'])['course_name'];
                        
                    }
                
                
                $page = $this->_getParam('page', 1);
                $paginator_data = array(
                    'page' => $page,
                    'result' => $result
                );
                
                $this->view->paginator = $this->_act->pagination($paginator_data);
                break;
        }
	     
     }
	 
	    public function ajaxGetEmployeeValidationAction(){
		$this->_helper->layout->disableLayout();
        if ($this->_request->isPost () && $this->getRequest ()->isXmlHttpRequest ()) {
			 $academic_year_id = $this->_getParam("academic_year_id");
			 $department_id = $this->_getParam("department_id");
			 $employee_id = $this->_getParam("employee_id");
                         $term_id = $this->_getParam("term_id");
                         $course_id = $this->_getParam("course_id");
			 $GradeAllocationReport_model = new Application_Model_GradeAllocationReport();
			 $result= $GradeAllocationReport_model->getGradeAllocateCount($academic_year_id,$department_id,$employee_id,$term_id,$course_id);
			// echo'<pre>';print_r(count($result)); die;
			$counts = $result['employee_count'];
			// print_r($counts);die;
			 echo json_encode($counts);die;
			 //echo '<pre>'; print_r($counts); die;
			// $this->view->result = $result;
		}
	}
	 
	 
	 
}//