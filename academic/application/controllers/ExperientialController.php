<?php
class ExperientialController extends Zend_Controller_Action {

    private $_siteurl = null;
    private $_db = null;
    private $_flashMessenger = null;
    private $_authontication = null;
    private $_agentsdata = null;
    private $_usersdata = null;
    private $_act = null;
    private $_adminsettings = null;
	Private $_unit_id = null;
        private $cur_datetime;
        public $year_id = array(
            1 => 'First Year',
            2 => 'Second Year'        
        );
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
            $this->_redirect('student-portal/student-dashboard');
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

    public function learningAllotmentAction() {
        $this->view->action_name = 'learningallotment';
        $this->view->sub_title_name = 'Learning Allotment';
        $this->accessConfig->setAccess('SA_ACAD_LEARNING_ALOTMENT');
        $ExperientialAllotment_model = new Application_Model_ExperientialAllotment();
		$ExperientialAllotment_form = new Application_Form_ExperientialAllotment();
		$ExperientialAllotmentItems_model = new Application_Model_ExperientialAllotmentItems();
		$allotment_id = $this->_getParam("id");
        $type = $this->_getParam("type");
		$this->view->type = $type;
        $this->view->form = $ExperientialAllotment_form;
		
        switch ($type) {
            case "add":    
                if ($this->getRequest()->isPost()) {
                    if ($ExperientialAllotment_form->isValid($this->getRequest()->getPost())) {
                        $data = $ExperientialAllotment_form->getValues();					
                        $last_insert_id = $ExperientialAllotment_model->insert($data);
						//print_r($last_insert_id);die;
						$employee_allocation = $this->getRequest()->getPost('employee');
						//print_r($employee_allocation);die;
					    foreach($employee_allocation['credit_id'] as $key=>$credit_id){
							//print_r($employee_allocation);die;
                        $item_data['allotment_id'] = $last_insert_id;
						$item_data['course_id'] = $employee_allocation['course_id'][$key];
						$item_data['year_id'] = $employee_allocation['year_id'][$key];
						$item_data['credit_id'] = $credit_id;
						$item_data['department_id'] = $employee_allocation['department_id'][$key];
						$item_data['employee_id'] = $employee_allocation['employee_id'][$key];
						//print_r($item_data);die;
						$ExperientialAllotmentItems_model->insert($item_data);
						}
                        $this->_flashMessenger->addMessage('Employee Successfully added');
                        $this->_redirect('experiential/learning-allotment'); 
						}
                    }
				
                
                break;
            case 'edit': 
                $result = $ExperientialAllotment_model->getRecord($allotment_id);
				$this->view->emp_allotment_id = $allotment_id;
				$item_result= $ExperientialAllotmentItems_model->getRecords($allotment_id);
				//print_r($item_result);die;
				//$this->view->item_result = $item_result;
                $ExperientialAllotment_form->populate($result);
				$this->view->result = $result;
                if ($this->getRequest()->isPost()) {
                    if ($ExperientialAllotment_form->isValid($this->getRequest()->getPost())) {
                        $data = $ExperientialAllotment_form->getValues();
						$ExperientialAllotment_model->update($data, array('allotment_id=?' => $allotment_id));
						$employee_allocation = $this->getRequest()->getPost('employee');
						
				         //print_r($employee_allocation);die;
					    $employee_allocation = $this->getRequest()->getPost('employee');
						//print_r($employee_allocation);die;
						$ExperientialAllotmentItems_model->trashItems($allotment_id);
					    foreach($employee_allocation['credit_id'] as $key=>$credit_id){
					   //print_r($employee_allocation);die;
                        $item_data['allotment_id'] = $allotment_id;
						$item_data['course_id'] = $employee_allocation['course_id'][$key];
						$item_data['year_id'] = $employee_allocation['year_id'][$key];
						$item_data['credit_id'] = $credit_id;
						$item_data['department_id'] = $employee_allocation['department_id'][$key];
						$item_data['employee_id'] = $employee_allocation['employee_id'][$key];
						//print_r($item_data);die;
						//$ExperientialAllotmentItems_model->update($item_data, array('allotment_id=?' => $allotment_id));
						$ExperientialAllotmentItems_model->insert($item_data);
						}
											
                        $this->_flashMessenger->addMessage('Details Updated Successfully');
                        $this->_redirect('experiential/learning-allotment');
                    } else {         
                    }
                }
                break;
            case 'delete':
                $data['status'] = 2;
                if ($allotment_id){
                    $ExperientialAllotment_model->update($data, array('allotment_id=?' => $allotment_id));
					$this->_flashMessenger->addMessage('Details Deleted Successfully');
					$this->_redirect('experiential/learning-allotment');
				}
                break;
            default:
                $messages = $this->_flashMessenger->getMessages();
                $this->view->messages = $messages;
                $result = $ExperientialAllotment_model->getRecords();
                $page = $this->_getParam('page', 1);
                $paginator_data = array(
                    'page' => $page,
                    'result' => $result
                );
                $this->view->paginator = $this->_act->pagination($paginator_data);
                break;
        }
    }
	
	
	  public function ajaxEmployeeDetailsViewAction(){
		   $this->_helper->layout->disableLayout();
          if ($this->_request->isPost () && $this->getRequest ()->isXmlHttpRequest ()) {			
			$academic_year_id = $this->_getParam("batch_id");
			//echo $academic_year_id;die;
			$this->view->academic_year_id = $academic_year_id;
			$emp_allotment = $this->_getParam("emp_allotment");
			//echo $emp_allotment;die;
			$AllotmentItems_model = new Application_Model_ExperientialAllotmentItems();
			//echo $emp_allotment;die;
			if(!empty($emp_allotment)){
			
			$item_result = $AllotmentItems_model->getItemsRecords($emp_allotment);
			//print_r($item_result);die;
			$this->view->item_result=$item_result;
			//$ExperientialLearning_model = new Application_Model_ExperientialLearning();			
			//$result = $ExperientialLearning_model->getExperientialRecords($academic_year_id);
			//$this->view->experiential_result = $result;
			$HRMModel_model = new Application_Model_HRMModel();
			  $department = $HRMModel_model->getDepartments();
			  //print_r($department);die;
			  $this->view->department = $department;
			  $HRMModel_model = new Application_Model_HRMModel();
			  $employee = $HRMModel_model->getEmployeeIds();
			  //print_r($department);die;
			  $this->view->employee = $employee;
			}
			else{ 
		   if($academic_year_id)
            { 
			$ExperientialLearning_model = new Application_Model_ExperientialLearning();			
			$result = $ExperientialLearning_model->getExperientialRecords($academic_year_id);
			
			$this->view->experiential_result = $result;
			$HRMModel_model = new Application_Model_HRMModel();
			  $department = $HRMModel_model->getDepartments();
			  //print_r($department);die;
			  $this->view->department = $department;
			  $HRMModel_model = new Application_Model_HRMModel();
			  $employee = $HRMModel_model->getEmployeeIds();
			  //print_r($department);die;
			  $this->view->employee = $employee;
				
			}
		  }
			   
         }        
		
	}
	
	
	public function ajaxEmployeeDetailsViewDataAction(){
		$this->_helper->layout->disableLayout();
        if ($this->_request->isPost () && $this->getRequest ()->isXmlHttpRequest ()) {
			$batch_id = $this->_getParam("batch_id");
			//$year_id = $this->_getParam("year_id");
			//echo $academic_year_id;die;
			$ExperientialAllotment_model = new Application_Model_ExperientialAllotment();
			$grade_result= $ExperientialAllotment_model->getValidEmployeeRecord($batch_id);
			$counts = count($grade_result['allotment_id']);
			//print_r($counts);die;
			echo json_encode($counts);die;
			$this->view->grade_result = $grade_result;
		}
	}
	
	public function ajaxGetDesignationAction(){
        $this->_helper->layout->disableLayout();
         if ($this->_request->isPost () && $this->getRequest ()->isXmlHttpRequest ()) {
			$desig_name = $this->_getParam("desig_name");			
			if($desig_name){ 
				$HRMModel = new Application_Model_HRMModel();
				$designation = $HRMModel->getDesignationDropDownList($desig_name);
				//print_r($SubProgram);die;
				echo '<option value="">Select </option>';
				foreach($designation as $k => $val){
					echo '<option value="'.$k.'" >'.$val.'</option>';	
				}
				die;				
			}               
        }  
	}
	
	public function ajaxGetDepartmentNameAction(){
        $this->_helper->layout->disableLayout();
         if ($this->_request->isPost () && $this->getRequest ()->isXmlHttpRequest ()) {
			$department_name = $this->_getParam("department_name");			
			if($department_name){ 
				$HRMModel = new Application_Model_HRMModel();
				$department = $HRMModel->getEmployee($department_name);
				//print_r($SubProgram);die;
				echo '<option value="">Select </option>';
				foreach($department as $k => $val){
					echo '<option value="'.$k.'" >'.$val.'</option>';	
				}
							
			}               
        }die;  
	}
    public function experientialLearningProjectAction() {

        $this->view->year_id = $this->year_id;
        $this->view->action_name = 'experiential-learning-project';
        $this->view->sub_title_name = 'Experiential Learning Project';
        $this->accessConfig->setAccess('SA_ACAD_LEARNING_PROJECT');
        $ExperientialLearningProject_model = new Application_Model_ExperientialLearningProject();
        $ExperientialLearningProject_form = new Application_Form_ExperientialLearningProject();
        $el_project_id = $this->_getParam("id");
        $type = $this->_getParam("type");
        $this->view->type = $type;
        $this->view->form = $ExperientialLearningProject_form;
        switch ($type) {
            case "add":
                if ($this->getRequest()->isPost()) {
                    if ($ExperientialLearningProject_form->isValid($this->getRequest()->getPost())) {
                        $data = $ExperientialLearningProject_form->getValues();   
                     //   echo "<pre>"; print_r($data);exit;
                        $data['added_date'] = $this->cur_datetime;
                        $data['updated_date'] = $this->cur_datetime;
                        $data['added_by'] = $this->login_storage->id;
                        $data['updated_by'] = $this->login_storage->id;
                        $ExperientialLearningProject_model->insert($data);
                        $this->_flashMessenger->addMessage('Records Successfully added');
                        $this->_redirect('experiential/experiential-learning-project');
                    }
                }

                break;
            case 'edit':
                $result = $ExperientialLearningProject_model->getRecord($el_project_id);
                $ExperientialLearning_model = new Application_Model_ExperientialLearning();
                $el_courses = $ExperientialLearning_model->getExpCourseRecords($result['batch_id'],$result['year_id']);
                $el_courses_dd = array();
                foreach($el_courses as $key => $row){
                    $el_courses_dd[$row['elc_id']] = $row['elc_name'];
                }
                
                $ExperientialLearningProject_form->getElement('el_component_id')->addMultiOptions($el_courses_dd);
                
                $ExperientialLearningProject_form->populate($result);
                $this->view->global_setting_id = $el_project_id;
                if ($this->getRequest()->isPost()) {
                    if ($ExperientialLearningProject_form->isValid($this->getRequest()->getPost())) {
                        $data = $ExperientialLearningProject_form->getValues();
                        $data['added_date'] = $this->cur_datetime;
                        $data['updated_date'] = $this->cur_datetime;
                        $data['added_by'] = $this->login_storage->id;
                        $data['updated_by'] = $this->login_storage->id;
                        $ExperientialLearningProject_model->update($data, array('el_project_id=?' => $el_project_id));
                        $this->_flashMessenger->addMessage('Records Successfully updated');
                        $this->_redirect('experiential/experiential-learning-project');
                    }
                }
                break;
            case 'delete':
                $data['deleted'] = 1;
                if ($el_project_id) {
                    $ExperientialLearningProject_model->update($data, array('el_project_id=?' => $el_project_id));
                    $this->_flashMessenger->addMessage('Details Deleted Successfully');
                    $this->_redirect('experiential/experiential-learning-project');
                }
                break;
            default:
                
                $messages = $this->_flashMessenger->getMessages();
                $this->view->messages = $messages;
             $result = $ExperientialLearningProject_model->getRecords();
                $this->view->result = $result;
                break;
        }
    }
    public function experientialLearningProjectAllocationAction() {
       
        $this->view->year_id = $this->year_id;
        $this->view->action_name = 'experiential-learning-project-allocation';
        $this->view->sub_title_name = 'Experiential Learning Project Allocation';
        $this->accessConfig->setAccess('SA_ACAD_LEARN_PROJECT_ALLOCATION');
        $ExperientialLearningProjectAllocation_model = new Application_Model_ExperientialLearningProjectAllocation();
        $ExperientialLearningProjectAllocation_form = new Application_Form_ExperientialLearningProjectAllocation();
        $id = $this->_getParam("id");
        $type = $this->_getParam("type");
        $this->view->type = $type;
        $this->view->form = $ExperientialLearningProjectAllocation_form;
        switch ($type) {
            case "add":
                if ($this->getRequest()->isPost()) {
                    if ($ExperientialLearningProjectAllocation_form->isValid($this->getRequest()->getPost())) {
                        $data = $ExperientialLearningProjectAllocation_form->getValues();
                        $data['student_ids'] = implode(',', $data['student_ids']);
                        unset($data['year_id']);
                        unset($data['el_component_id']);
                        $data['added_date'] = $this->cur_datetime;
                        $data['updated_date'] = $this->cur_datetime;
                        $data['added_by'] = $this->login_storage->id;
                        $data['updated_by'] = $this->login_storage->id;
                        $ExperientialLearningProjectAllocation_model->insert($data);
                        $this->_flashMessenger->addMessage('Records Successfully added');
                        $this->_redirect('experiential/experiential-learning-project-allocation');
                    }
                }

                break;
            case 'edit':
                $result = $ExperientialLearningProjectAllocation_model->getRecord($id);
                $this->view->result = $result;
                
                $ExperientialLearning_model = new Application_Model_ExperientialLearning();
                $el_courses = $ExperientialLearning_model->getExpCourseRecords($result['batch_id'],$result['year_id']);
                $el_courses_dd = array();
                foreach($el_courses as $key => $row){
                    $el_courses_dd[$row['elc_id']] = $row['elc_name'];
                }
                
                $ExperientialLearningProjectAllocation_form->getElement('el_component_id')->addMultiOptions($el_courses_dd);
                
                //Fetching Projects
                $ExperientialLearningProject_model = new Application_Model_ExperientialLearningProject();
                $el_projects = $ExperientialLearningProject_model->getExpProjectRecords($result['batch_id'],$result['year_id'], $result['el_component_id']);
                
                $el_projects_dd = array();
                foreach($el_projects as $key => $row){
                    $el_projects_dd[$row['el_project_id']] = $row['project_name'];
                }
                $ExperientialLearningProjectAllocation_form->getElement('el_project_id')->addMultiOptions($el_projects_dd);
                
                $ExperientialLearningProjectAllocation_form->populate($result);                
                if ($this->getRequest()->isPost()) {
                    if ($ExperientialLearningProjectAllocation_form->isValid($this->getRequest()->getPost())) {
                        $data = $ExperientialLearningProjectAllocation_form->getValues();
                        unset($data['year_id']);
                        unset($data['el_component_id']);
                        $data['student_ids'] = implode(',', $data['student_ids']);
                        $data['added_date'] = $this->cur_datetime;
                        $data['updated_date'] = $this->cur_datetime;
                        $data['added_by'] = $this->login_storage->id;
                        $data['updated_by'] = $this->login_storage->id;
                        $ExperientialLearningProjectAllocation_model->update($data, array('id=?' => $id));
                        $this->_flashMessenger->addMessage('Records Successfully updated');
                        $this->_redirect('experiential/experiential-learning-project-allocation');
                    }
                }
                break;
            case 'delete':
                $data['deleted'] = 1;
                if ($id) {
                    $ExperientialLearningProjectAllocation_model->update($data, array('id=?' => $id));
                    $this->_flashMessenger->addMessage('Details Deleted Successfully');
                    $this->_redirect('experiential/experiential-learning-project-allocation');
                }
                break;
            default:
                $messages = $this->_flashMessenger->getMessages();
                $this->view->messages = $messages;
                
                $result = $ExperientialLearningProjectAllocation_model->getRecords();
                
                $this->view->result = $result;
                break;
        }
    }
    public function ajaxGetElComponentsAction(){
        $this->_helper->layout->disableLayout();
         if ($this->_request->isPost () && $this->getRequest ()->isXmlHttpRequest ()) {
            $batch_id = $this->_getParam("batch_id");	
            $year_id = $this->_getParam("year_id");
            if($batch_id && $year_id){ 
                    $ExperientialLearning_model = new Application_Model_ExperientialLearning();
                    $el_courses = $ExperientialLearning_model->getExpCourseRecords($batch_id,$year_id);
                    
                    echo '<option value="">Select </option>';
                    foreach($el_courses as $k => $val){
                            echo '<option value="'.$val['elc_id'].'" >'.$val['elc_name'].'</option>';	
                    }
                    				
            }               
        }
        die;
    }
    public function ajaxGetElProjectsAction(){
        $this->_helper->layout->disableLayout();
         if ($this->_request->isPost () && $this->getRequest ()->isXmlHttpRequest ()) {
            $batch_id = $this->_getParam("batch_id");	
            $year_id = $this->_getParam("year_id");
            $el_component_id = $this->_getParam("el_component_id");
            if($batch_id && $year_id && $el_component_id){ 
                    $ExperientialLearningProject_model = new Application_Model_ExperientialLearningProject();
                    $el_projects = $ExperientialLearningProject_model->getExpProjectRecords($batch_id,$year_id,$el_component_id);
                    
                    echo '<option value="">Select </option>';
                    foreach($el_projects as $k => $val){
                            echo '<option value="'.$val['el_project_id'].'" >'.$val['project_name'].'</option>';	
                    }
                    				
            }               
        }
        die;
    }
    
    
    
    
    public function ajaxGetElAllocatedStudentAction(){
        $this->_helper->layout->disableLayout();
         if ($this->_request->isPost () && $this->getRequest ()->isXmlHttpRequest ()) {
            $batch_id = $this->_getParam("batch_id");	
            $year_id = $this->_getParam("year_id");
            $el_component_id = $this->_getParam("el_component_id");
            if($batch_id && $year_id && $el_component_id){ 
                    $ExperientialLearningProjectAllocation_model = new Application_Model_ExperientialLearningProjectAllocation();
                    $el_students = $ExperientialLearningProjectAllocation_model->getELAllocatedStudents($batch_id,$year_id,$el_component_id);
                   $student_list = array();
                   foreach($el_students as $row){
                       $sudent_list1 = explode(',', $row['student_ids']);
                       foreach($sudent_list1 as $value){
                           $student_list[] = $value;
                       }
                   }
                   $student_list = array_unique($student_list);
                   echo json_encode($student_list);
                    				
            }               
        }
        die;
    }

}