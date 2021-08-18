<?php

class EvaluationComponentsController extends Zend_Controller_Action {

    private $_siteurl = null;

    private $_db = null;

    private $_authontication = null;

    private $_agentsdata = null;

    private $_usersdata = null;

    private $_act = null;

    private $_adminsettings = null;

    private $_flashMessenger = null;
    private $login_storage = NULL;
    private $roleConfig = NULL;
private $accessConfig =NULL;
    public function init() {

        $zendConfig = new Zend_Config_Ini(

                APPLICATION_PATH . '/configs/application.ini', APPLICATION_ENV);
                require_once APPLICATION_PATH . '/configs/access_level.inc';
                        
        $this->accessConfig = new accessLevel();

        $config = $zendConfig->mainconfig->toArray();

        $this->view->mainconfig = $config;

        $this->_action = $this->getRequest()->getActionName();
        
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
	

        $this->_flashMessenger = $this->_helper->FlashMessenger;
        $this->authonticate();

        $this->_act = new Application_Model_Adminactions();

        $this->_db = Zend_Db_Table::getDefaultAdapter();

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

    public function indexAction() {
		$this->view->action_name = 'Evaluation Components';
        $this->view->sub_title_name = 'evaluationcomponents';
        $this->accessConfig->setAccess('SA_ACAD_EVALUATION_COMPONENT');
        $EvaluationComponents_model = new Application_Model_EvaluationComponents();
        
		$EvaluationComponentsItems_model = new Application_Model_EvaluationComponentsItems();
                $ExperientialEvaluationComponents_model = new Application_Model_ExperientialEvaluationComponents();
		$EvaluationComponents_form = new Application_Form_EvaluationComponents();
                $student_info = new Application_Model_StudentPortal();
		$ec_id = $this->_getParam("id");
		//print_r($ec_id);die;
        $type = $this->_getParam("type");
		$this->view->type = $type;
             
        $this->view->form = $EvaluationComponents_form;
		
        switch ($type) {
            case "add":    
                if ($this->getRequest()->isPost()) {
                    if ($EvaluationComponents_form->isValid($this->getRequest()->getPost())) {
                        
                        $exp_component_names = isset($_POST['exp_course_component_name'])?$_POST['exp_course_component_name']:array();                        
                        $exp_component_weightages = isset($_POST['exp_course_component_weightage'])?$_POST['exp_course_component_weightage']:array();
                        $exp_component_re_weightages = isset($_POST['exp_course_component_remaining_weightage'])?$_POST['exp_course_component_remaining_weightage']:array();
                        
                        $data = $EvaluationComponents_form->getValues();
                        $data['cc_id'] = $this->_getParam("course_type");
                        $data['term_id'] = $this->_getParam("term_id");
                        $data['course_id'] = $this->_getParam("course_id");
                        
                           $studentNum =$student_info->getstudents($data['academic_year_id']);
				//if(count($studentNum)>0){
                        $last_insert_id=$EvaluationComponents_model->insert($data);
						//$EmployeeAllotment_model= new Application_Model_EmployeeAllotment();
						//$employee_result= $EmployeeAllotment_model->getEmployeeAllTerms($data['academic_year_id'],$data['department_id'],$data['employee_id']);
						$component = $this->getRequest()->getPost('component');
						
                                                if($data['cc_id'] ==1){
						$components = $_POST['components'];
						
													
						for($k=0;$k<count($_POST['components']['component_name_'.$data['term_id'].'_'.$data['course_id']]);$k++){
						$item_data['ec_id'] = $last_insert_id;
						$item_data['term_id']=$data['term_id'];
						$item_data['course_id']=$data['course_id'];
						$item_data['component_name'] = $components['component_name_'.$data['term_id'].'_'.$data['course_id']][$k];
						$item_data['weightage'] = $components['weightage_'.$data['term_id'].'_'.$data['course_id']][$k];
						$item_data['remaining_weightage'] = $components['remaining_weightage_'.$data['term_id'].'_'.$data['course_id']][$k];
						$EvaluationComponentsItems_model->insert($item_data);
						
						}
                                                }
						
                                                
                                                
                                                
                                                
                                                //Saving Experiential Learning
                                                //$last_insert_id1 = $ExperientialComponents_model->insert($data);
                                                if($data['cc_id'] ==2){
                                                foreach($exp_component_names as $course_id => $components){
                                                    $cnt = 0;
                                                    foreach($components as $key => $comp_name){
                                                        $col_values = array(
                                                            'ec_id' => $last_insert_id,
                                                            'course_id' => $course_id,
                                                            'component_name' => $comp_name,
                                                            'weightage' => $exp_component_weightages[$course_id][$key],
                                                            'remaining_weightage' => $exp_component_re_weightages[$course_id][$key]
                                                        );
                                                        $ExperientialEvaluationComponents_model->insert($col_values);
                                                    }
                                                    
                                                }
                                                }
                                                    
                                               // $ExperientialEvaluationComponents_model->insert($data);
                                                
                                                
                                                
                        $this->_flashMessenger->addMessage('Evaluation  Components Successfully added');
                        $this->_redirect('evaluation-components/index');
                                
                                //else
                                  //  $this->_flashMessenger->addMessage('There is no Student in this batch !');
                                    //$this->_redirect('evaluation-components/index/type/add');
                        
						}
                    }

                
                break;
            case 'edit': 
                $result = $EvaluationComponents_model->getRecord($ec_id);
                //Getting Faculty name
                $HRMModel_model= new Application_Model_HRMModel();
                $faculty_detail = $HRMModel_model->getEmployeeData($result['employee_id']);
                $this->view->faculty_detail = $faculty_detail;
                if($result['cc_id'] == 1){
                    $evc_master = $EvaluationComponents_model->getClassRoomRecord($ec_id);
                }
                if($result['cc_id'] == 2){
                    $evc_master = $EvaluationComponents_model->getExpLearningRecord($ec_id);
                }
                $this->view->evc_master = $evc_master;
                //print_r($evc_master);exit;
                //print_r($result);exit;
				$item_result = $EvaluationComponents_model->getItemRecords($ec_id);
                                //print_r($item_result);exit;
				$this->view->item_result = $item_result;                                
                                //Fetching Experiential learning Evaluation Componenets Items
                                $item_result1 = $EvaluationComponents_model->getExperientialEvaluationComponentsItemRecords($ec_id);
				$this->view->item_result1 = $item_result1;                                
				//$EmployeeAllotment_model= new Application_Model_EmployeeAllotment();
				//$employee_result= $EmployeeAllotment_model->getEmployeeAllTerms($result['academic_year_id'],$result['department_id'],$result['employee_id']);
                                //$ExperientialAllotment_model = new Application_Model_ExperientialAllotment();
                                //$employee_result1 = $ExperientialAllotment_model->getEvaluationItems($result['academic_year_id'],$result['department_id'],$result['employee_id']);
				//$this->view->employee_result = $employee_result;
                                //$this->view->employee_result1 = $employee_result1;
				//echo '<pre>'; print_r($employee_result); die;
                                $role_id = $this->login_storage->role_id;
                                if(!in_array($role_id, $this->roleConfig)){
                                    
                                    $Academic_model = new Application_Model_Academic();
                                    $batch_record = $Academic_model->getBatchCodeRecord($result['academic_year_id']);
                                    $this->view->batch_short_code = $batch_record['short_code'];
                                    //print_r($batch_record);exit;
                                    $EvaluationComponents_form->getElement('employee_id')->setAttrib('disabled', 'disabled');
                                    //$employee_id = $EvaluationComponents_form->createElement('hidden','employee_id')->setAttrib('value', $result['employee_id']);
                                    //$EvaluationComponents_form->addElement($employee_id);
                                }
                                
                $EvaluationComponents_form->populate($result);
				$this->view->result = $result;
				$this->view->eval_component_id = $ec_id;
                if ($this->getRequest()->isPost()) {
                    if ($EvaluationComponents_form->isValid($this->getRequest()->getPost())) {
                        $data = $EvaluationComponents_form->getValues();
                        $data['cc_id'] = $this->_getParam("course_type");
                        $data['term_id'] = $this->_getParam("term_id");
                        $data['course_id'] = $this->_getParam("course_id");
                        
                        $exp_component_names = isset($_POST['exp_course_component_name'])?$_POST['exp_course_component_name']:array();
                        $exp_component_weightages = isset($_POST['exp_course_component_weightage'])?$_POST['exp_course_component_weightage']:array();
                        $exp_component_re_weightages = isset($_POST['exp_course_component_remaining_weightage'])?$_POST['exp_course_component_remaining_weightage']:array();
                        if($data['cc_id'] == 1){//If Class Room Learning
                            $GradeAllocation_model = new Application_Model_GradeAllocation();	
                            $isGradeExist = $GradeAllocation_model->isGradeAllocated($data['academic_year_id'], $data['employee_id'],$data['term_id'],$data['course_id']);
                        }
                        if($data['cc_id'] == 2){//If Experiential Learning
                            $ELAllocation_model = new Application_Model_ExperientialGradeAllocation();	
                            $isGradeExist = $ELAllocation_model->isGradeAllocated($data['academic_year_id'], $data['employee_id'],$data['course_id']);
                        }
			
                        
                        if($isGradeExist){
                            $this->_flashMessenger->addMessage('Grade already allocated. First delete grades before editing it.');
                            $this->_redirect('evaluation-components/index');
                        }
                        
                        //$EvaluationComponents_model->update($data, array('ec_id=?' => $ec_id));
						
						//$EmployeeAllotment_model = new Application_Model_EmployeeAllotment();
						//$employee_result= $EmployeeAllotment_model->getEmployeeAllTerms($data['academic_year_id'],$data['department_id'],$data['employee_id']);
						$component = $this->getRequest()->getPost('component');
						if($data['cc_id'] ==1){
                                                 $EvaluationComponentsItems_model->trashItems($ec_id);   
						$components = $_POST['components'];
													
						for($k=0;$k<count($_POST['components']['component_name_'.$data['term_id'].'_'.$data['course_id']]);$k++){
						$item_data['ec_id'] = $ec_id;
						$item_data['term_id']=$data['term_id'];
						$item_data['course_id']=$data['course_id'];
						$item_data['component_name'] = $components['component_name_'.$data['term_id'].'_'.$data['course_id']][$k];
						$item_data['weightage'] = $components['weightage_'.$data['term_id'].'_'.$data['course_id']][$k];
						$item_data['remaining_weightage'] = $components['remaining_weightage_'.$data['term_id'].'_'.$data['course_id']][$k];
						$EvaluationComponentsItems_model->insert($item_data);
						}
                                                }
						if($data['cc_id'] ==2){
                                                $ExperientialEvaluationComponents_model->trashItems($ec_id);
                                                //Saving Experiential Learning
                                                //$last_insert_id1 = $ExperientialComponents_model->insert($data);
                                                foreach($exp_component_names as $course_id => $components){
                                                    $cnt = 0;
                                                    foreach($components as $key => $comp_name){
                                                        $col_values = array(
                                                            'ec_id' => $ec_id,
                                                            'course_id' => $course_id,
                                                            'component_name' => $comp_name,
                                                            'weightage' => $exp_component_weightages[$course_id][$key],
                                                            'remaining_weightage' => $exp_component_re_weightages[$course_id][$key]
                                                        );
                                                        $ExperientialEvaluationComponents_model->insert($col_values);
                                                    }
                                                    
                                                }
                                                }
						
                        $this->_flashMessenger->addMessage('Evaluation Components Updated Successfully');
                        $this->_redirect('evaluation-components/index');
                    } else {         
                    }
                }
                break;
            case 'delete':
                $data['status'] = 2;
                if ($ec_id){
                    $EvaluationComponents_model->update($data, array('ec_id=?' => $ec_id));
					$EvaluationComponentsItems_model->update($data, array('eci_id=?' => $eci_id));
					$this->_flashMessenger->addMessage('Evaluation Component Deleted Successfully');
					$this->_redirect('evaluation-components/index');
				}
                break;
            default:
                $messages = $this->_flashMessenger->getMessages();
                $this->view->messages = $messages;
                //print_r($this->login_storage);exit;
                //Show only components for the logged in faculty
                $role_id = $this->login_storage->role_id;
                $empl_id = $this->login_storage->empl_id;
                if(in_array($role_id, $this->roleConfig)){
                    $result = $EvaluationComponents_model->getRecords();
                }
                else{
                    $result = $EvaluationComponents_model->getRecordsByFacultyId($empl_id);
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
	public function ajaxGetEmployeeAction(){
		 $this->_helper->layout->disableLayout();
        if ($this->_request->isPost () && $this->getRequest ()->isXmlHttpRequest ()) {
			 $department_id = $this->_getParam("department_id");
			// print_r($academic_id); die;
			 $HRMModel_model= new Application_Model_HRMModel();
			 $result= $HRMModel_model->getEmployee($department_id);
			
			
				echo '<option value="">Select</option>';
				foreach($result as $k => $val){
					
					echo '<option value="'.$k.'" >'.$val.'</option>';	
					
					
				}	
			 
		}die;
	}
	public function ajaxGetEmployeeTermsAction(){
		 $this->_helper->layout->disableLayout();
        if ($this->_request->isPost () && $this->getRequest ()->isXmlHttpRequest ()) {
			 $department_id = $this->_getParam("department_id");
			 $employee_id = $this->_getParam("employee_id");
			 $academic_year_id = $this->_getParam("academic_year_id");
                         $department_id = $this->_getParam("department_id");
			 $course_type = $this->_getParam("course_type");
			 $term_id = $this->_getParam("term_id");
			 $course_id = $this->_getParam("course_id");
			 //$this->view->eval_component_id = $eval_component_id;
			// print_r($academic_id);die;
                         
                         $this->view->department_id = $department_id;
                         $this->view->employee_id = $employee_id;
                         $this->view->academic_year_id = $academic_year_id;
                         $this->view->course_type = $course_type;
                         $this->view->term_id = $term_id;
                         $this->view->course_id = $course_id;
                         
                         
			
                         /*
				$EmployeeAllotment_model= new Application_Model_EmployeeAllotment();
				$result= $EmployeeAllotment_model->getEmployeeAllTerms($academic_year_id,$department_id,$employee_id);
                          * 
                          */
				//echo '<pre>'; print_r($result); die;
                         /*
                                $ExperientialAllotment_model = new Application_Model_ExperientialAllotment();
                                $result1 = $ExperientialAllotment_model->getEvaluationItems($academic_year_id,$department_id,$employee_id);
                          * 
                          */
 		//$EvaluationComponentsItems_model = new Application_Model_EvaluationComponentsItems();
                                
				//$this->view->result = $result;
                                //$this->view->result1 = $result1;
                                
                                
		}
	}
	
	public function ajaxGetTermsAction(){
		 $this->_helper->layout->disableLayout();
        if ($this->_request->isPost () && $this->getRequest ()->isXmlHttpRequest ()) {
			 $department_id = $this->_getParam("department_id");
			 $employee_id = $this->_getParam("employee_id");
			 $academic_year_id = $this->_getParam("academic_year_id");
			 $component_grade_id = $this->_getParam("component_grade_id");
			 if($component_grade_id){
				  $ComponentGrade_model = new Application_Model_ComponentGrade();
				  $grade_result =  $ComponentGrade_model->getRecord($component_grade_id);
				 
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
					if($k == $grade_result['term_id']){
						
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
			 $component_grade_id = $this->_getParam("component_grade_id");
			 if($component_grade_id){
				  $ComponentGrade_model = new Application_Model_ComponentGrade();
				  $grade_result =  $ComponentGrade_model->getRecord($component_grade_id);
				 
			 }
				$EmployeeAllotment_model= new Application_Model_EmployeeAllotment();
				$result= $EmployeeAllotment_model->getCourses($academic_year_id,$department_id,$employee_id,$term_id);
				echo '<div class="col-sm-3 employee_class">';
                echo  '<div class="form-group">';
                 echo  '<label class="control-label">Courses</label>';
				echo '<select type="text" name="course_id" id="course_id" class="form-control">';
				echo '<option value="">Select</option>';
				foreach($result as $k => $val)
				{
					$selected ='';
					if($k == $grade_result['course_id']){
						
						$selected = "selected";
					}
					echo '<option value="'.$k.'" '.$selected.'>'.$val.'</option>';	
					
				}
				echo '</select>';
				echo '</div></div>';
		
	}die;
	}	
	public function ajaxGetEvaluationComponentsAction(){
		$this->_helper->layout->disableLayout();
        if ($this->_request->isPost () && $this->getRequest ()->isXmlHttpRequest ()) {
			 $department_id = $this->_getParam("department_id");
			 $employee_id = $this->_getParam("employee_id");
			 $academic_year_id = $this->_getParam("academic_year_id");
			 $term_id = $this->_getParam("term_id");
			 $course_id = $this->_getParam("course_id");
			 $component_grade_id = $this->_getParam("component_grade_id");
			 if($component_grade_id){
				  $ComponentGrade_model = new Application_Model_ComponentGrade();
				  $grade_result =  $ComponentGrade_model->getRecord($component_grade_id);
				 
			 }
				$EvaluationComponents_model= new Application_Model_EvaluationComponents();
				$result= $EvaluationComponents_model->getComponents($academic_year_id,$department_id,$employee_id,$term_id,$course_id);
				echo '<div class="col-sm-3 employee_class">';
                echo  '<div class="form-group">';
                 echo  '<label class="control-label">Components</label>';
				echo '<select type="text" name="component_id" id="component_id" class="form-control">';
				echo '<option value="">Select</option>';
				foreach($result as $k => $val)
				{
					$selected ='';
					if($k == $grade_result['component_id']){
						
						$selected = "selected";
					}
					echo '<option value="'.$k.'" '.$selected.'>'.$val.'</option>';	
					
				}
				echo '</select>';
				echo '</div></div>';
		
	}die;
		
	}
	public function ajaxGetAddEvaluationComponentsAction(){
		$this->_helper->layout->disableLayout();
        if ($this->_request->isPost () && $this->getRequest ()->isXmlHttpRequest ()) {
			 $department_id = $this->_getParam("department_id");
			 $employee_id = $this->_getParam("employee_id");
			 $academic_year_id = $this->_getParam("academic_year_id");
			 $term_id = $this->_getParam("term_id");
			 $course_id = $this->_getParam("course_id");
			 $component_grade_id = $this->_getParam("component_grade_id");
			 if($component_grade_id){
				  $ComponentGrade_model = new Application_Model_ComponentGrade();
				  $grade_result =  $ComponentGrade_model->getRecord($component_grade_id);
				 
			 }
				$EvaluationComponents_model= new Application_Model_EvaluationComponents();
				$result= $EvaluationComponents_model->getAddComponents($academic_year_id,$department_id,$employee_id,$term_id,$course_id);
				echo '<div class="col-sm-3 employee_class">';
                echo  '<div class="form-group">';
                 echo  '<label class="control-label">Components</label>';
				echo '<select type="text" name="component_id" id="component_id" class="form-control">';
				echo '<option value="">Select</option>';
				foreach($result as $k => $val)
				{
					$selected ='';
					if($k == $grade_result['component_id']){
						
						$selected = "selected";
					}
					echo '<option value="'.$k.'" '.$selected.'>'.$val.'</option>';	
					
				}
				echo '</select>';
				echo '</div></div>';
		
	}die;
		
	}
	public function componentGradeAction() {
		
		
		 $this->view->action_name = 'Component Grade';
        $this->view->sub_title_name = 'component-grade';
        $ComponentGrade_model = new Application_Model_ComponentGrade();
		$ComponentGrade_form = new Application_Form_ComponentGrade();
		$ComponentGradeItems_model = new Application_Model_ComponentGradeItems();
		$component_grade_id = $this->_getParam("id");
        $type = $this->_getParam("type");
		$this->view->type = $type;
        $this->view->form = $ComponentGrade_form;
		
        switch ($type) {
            case "add":    
                if ($this->getRequest()->isPost()) {
                    if ($ComponentGrade_form->isValid($this->getRequest()->getPost())) {
                        $data = $ComponentGrade_form->getValues();	
						 $data['term_id'] = $this->getRequest()->getPost('term_id');
						 $data['course_id'] = $this->getRequest()->getPost('course_id');
						 $data['component_id'] = $this->getRequest()->getPost('component_id');
						 $data['weightage'] = $this->getRequest()->getPost('weightage');
						 
                       $last_insert_id =  $ComponentGrade_model->insert($data);
					   $grade = $this->getRequest()->getPost('grade');
					   foreach(array_filter($grade['letter_grade']) as $k => $letter_grade){
						   $grade_data['component_grade_id'] = $last_insert_id;
						   $grade_data['letter_grade'] = $letter_grade;
						   $grade_data['number_grade'] = $grade['number_grade'][$k];
						   $ComponentGradeItems_model->insert($grade_data);
					   }
				
                        $this->_flashMessenger->addMessage('Grades Successfully added');

                        $this->_redirect('evaluation-components/component-grade');
						 	
					} 
				}
                
                break;
            case 'edit': 
                $result = $ComponentGrade_model->getRecord($component_grade_id);
                $ComponentGrade_form->populate($result);
				$this->view->result = $result;
				$this->view->grade_id = $result['component_grade_id'];
				$item_result = $ComponentGradeItems_model->getRecords($component_grade_id);
				$this->view->item_result = $item_result;
				
                if ($this->getRequest()->isPost()) 
				{
                    if ($ComponentGrade_form->isValid($this->getRequest()->getPost())) {
                        $data = $ComponentGrade_form->getValues();
						 $data['term_id'] = $this->getRequest()->getPost('term_id');
						 $data['course_id'] = $this->getRequest()->getPost('course_id');
						 $data['component_id'] = $this->getRequest()->getPost('component_id');
						 $data['weightage'] = $this->getRequest()->getPost('weightage');
						 //print_r($data); die;
						  $ComponentGrade_model->update($data, array('component_grade_id=?' => $component_grade_id));
						$grade = $this->getRequest()->getPost('grade');
						$ComponentGradeItems_model->trashItems($component_grade_id);
					   foreach(array_filter($grade['letter_grade']) as $k => $letter_grade){
						   $grade_data['component_grade_id'] = $component_grade_id;
						   $grade_data['letter_grade'] = $letter_grade;
						   $grade_data['number_grade'] = $grade['number_grade'][$k];
						   $ComponentGradeItems_model->insert($grade_data);
					   }
                       						
                        $this->_flashMessenger->addMessage('Grades Updated Successfully');
                       $this->_redirect('evaluation-components/component-grade');
                    } else {         
                    }
                }
                break;
            case 'delete':
                $data['status'] = 2;
                if ($batch_id){
                    $ComponentGrade_model->update($data, array('component_grade_id=?' => $component_grade_id));
					$this->_flashMessenger->addMessage('Grades Deleted Successfully');
					$this->_redirect('evaluation-components/component-grade');
				}
                break;
            default:
                $messages = $this->_flashMessenger->getMessages();
                $this->view->messages = $messages;
                //Show only components for the logged in faculty
                if($th)
                $result = $ComponentGrade_model->getRecords();
                $page = $this->_getParam('page', 1);
                $paginator_data = array(
                    'page' => $page,
                    'result' => $result
                );
                $this->view->paginator = $this->_act->pagination($paginator_data);
                break;
        }
		
		
	}
	public function ajaxGetGradeEvaluationComponentsAction()
	{
		$this->_helper->layout->disableLayout();
        if ($this->_request->isPost () && $this->getRequest ()->isXmlHttpRequest ()) {
			 $department_id = $this->_getParam("department_id");
			 $employee_id = $this->_getParam("employee_id");
			 $academic_year_id = $this->_getParam("academic_year_id");
			 $term_id = $this->_getParam("term_id");
			 $course_id = $this->_getParam("course_id");
			 
			 
				$EvaluationComponents_model= new Application_Model_EvaluationComponents();
				$result= $EvaluationComponents_model->getComponents($academic_year_id,$department_id,$employee_id,$term_id,$course_id);
				echo '<div class="col-sm-3 employee_class">';
                echo  '<div class="form-group">';
                 echo  '<label class="control-label">Components</label>';
				echo '<select type="text" name="component_id" id="component_id" class="form-control">';
				echo '<option value="">Select</option>';
				foreach($result as $k => $val)
				{
					
					echo '<option value="'.$k.'">'.$val.'</option>';	
					
				}
				echo '</select>';
				echo '</div></div>';
		
	}die;
		
   }
   
   public function ajaxGetEmployeeValidationAction()
   {
		$this->_helper->layout->disableLayout();
        if ($this->_request->isPost () && $this->getRequest ()->isXmlHttpRequest ()) {
			 $academic_year_id = $this->_getParam("academic_year_id");
			 $department_id = $this->_getParam("department_id");
			 $employee_id = $this->_getParam("employee_id");
                         $course_type = $this->_getParam("course_type");
			 $term_id = $this->_getParam("term_id");
			 $course_id = $this->_getParam("course_id");
			//print_r($academic_id); die;
			 $EvaluationComponents_model= new Application_Model_EvaluationComponents();
			 $result= $EvaluationComponents_model->getEvlComponentCount($academic_year_id,$department_id,$employee_id,$course_type,$term_id,$course_id);
			 $counts = count($result['ec_id']);
			 echo json_encode($counts);die;
			 //echo '<pre>'; print_r($counts); die;
			// $this->view->result = $result;
		}
	}

	
	//rajesh code to get weightage
	
	public function ajaxGetWeightagesAction()
	{
		$this->_helper->layout->disableLayout();
        if ($this->_request->isPost () && $this->getRequest ()->isXmlHttpRequest ()) {
			 $academic_year_id = $this->_getParam("academic_year_id");
			 $department_id = $this->_getParam("department_id");
			 $employee_id = $this->_getParam("employee_id");
			 $course_id = $this->_getParam("course_id");
			 $component_id = $this->_getParam("component_id");
			
			 $term_id = $this->_getParam("term_id");
			 
			//print_r($academic_id); die;
			 $EvaluationComponents_model= new Application_Model_EvaluationComponents();
			 $result= $EvaluationComponents_model->getweightages($academic_year_id,$department_id,$employee_id,$course_id,$component_id,$term_id);
			 echo json_encode($result);die;
			 //echo '<pre>'; print_r($result); die;
			 $this->view->result = $result;
			 
		}
	}
	public function ajaxEvaluationComponentsViewAction()
	{
		$this->_helper->layout->disableLayout();
		if($this->_request->isPost () && $this->getRequest ()->isXmlHttpRequest ()){
			$evaluation_id = $this->_getParam("evaluation_id");
			$EvaluationComponents_model = new Application_Model_EvaluationComponents();
			$result = $EvaluationComponents_model->getComponentsView($evaluation_id);
			$this->view->result = $result;
			
		}
	}	

}