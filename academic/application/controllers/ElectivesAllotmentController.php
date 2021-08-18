<?php



class ElectivesAllotmentController extends Zend_Controller_Action {



    private $_siteurl = null;

    private $_db = null;

    private $_authontication = null;

    private $_agentsdata = null;

    private $_usersdata = null;

    private $_act = null;

    private $_adminsettings = null;

    private $_flashMessenger = null;
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

        $this->view->action_name = 'Electives Faculty Allotment';

        $this->view->sub_title_name = 'electives-faculty-allotment';

        $ElectivesFacultyAllotment_model = new Application_Model_ElectivesFacultyAllotment();
		
		$ElectivesFacultyAllotmentItems_model = new Application_Model_ElectivesFacultyAllotmentItems();
		
		$CoursesFacultyAllotmentItems_model = new Application_Model_CoursesFacultyAllotmentItems();
		
		$ElectivesFacultyAllotment_form = new Application_Form_ElectivesFacultyAllotment();

        $faculty_allotment_id = $this->_getParam("id");

        $type = $this->_getParam("type");

        switch ($type) {

            case "add":

                $this->view->type = $type;

                $this->view->form = $ElectivesFacultyAllotment_form;

                if ($this->getRequest()->isPost()) {

                    if ($ElectivesFacultyAllotment_form->isValid($this->getRequest()->getPost())) {

                        $data = $ElectivesFacultyAllotment_form->getValues();

                       $last_insert_id =  $ElectivesFacultyAllotment_model->insert($data);
					   $faculty = $this->getRequest()->getPost('faculty');
					   foreach(array_filter($faculty['term_id']) as $k => $term_id){
						   
						   $faculty_data['faculty_allotment_id'] = $last_insert_id;
						   $faculty_data['term_id'] = $term_id;
						   $faculty_data['cc_id'] = $faculty['cc_id'][$k];
						   $faculty_data['course_type'] = 1;
						   $faculty_data['course_id'] = $faculty['course_id'][$k];
						   $faculty_data['credit_value'] = $faculty['credit_value'][$k];
						   $faculty_data['department_id'] = $faculty['department_id'][$k];
						   $faculty_data['employee_id'] = $faculty['employee_id'][$k];
						   
							$CoursesFacultyAllotmentItems_model->insert($faculty_data);
					   }
					   $elective  = $this->getRequest()->getPost('elective');
					 
					   foreach(array_filter($elective['elective_term_id']) as $key => $elective_term_id){
						   $elective_data['faculty_allotment_id'] = $last_insert_id;
						   $elective_data['term_id'] = $elective_term_id;
						   $elective_data['elective_id'] = $elective['elective_id'][$key];
						   $elective_data['department_id'] = $elective['elective_department_id'][$key];
						    $elective_data['course_type'] = 2;
						   $elective_data['credit_value'] = 1;
						   $elective_data['employee_id'] = $elective['elective_employee_id'][$key];
						   $ElectivesFacultyAllotmentItems_model->insert($elective_data);
					   }
                        $this->_flashMessenger->addMessage('Details Added Successfully ');

                        $this->_redirect('electives-allotment/index');

                    } 

                }

                break;

            case 'edit':

                $this->view->type = $type;

                $this->view->form = $ElectivesFacultyAllotment_form;

                $result = $ElectivesFacultyAllotment_model->getRecord($faculty_allotment_id); 
				
				$this->view->alloted_id = $result['faculty_allotment_id'];

                $ElectivesFacultyAllotment_form->populate($result);

                if ($this->getRequest()->isPost()) {

                    if ($ElectivesFacultyAllotment_form->isValid($this->getRequest()->getPost())) {

                        $data = $ElectivesFacultyAllotment_form->getValues();

                        $ElectivesFacultyAllotment_model->update($data, array('faculty_allotment_id=?' => $faculty_allotment_id));
						 $faculty = $this->getRequest()->getPost('faculty');
						 $CoursesFacultyAllotmentItems_model->trashItems($faculty_allotment_id);
					   foreach(array_filter($faculty['term_id']) as $k => $term_id){
						   
						   $faculty_data['faculty_allotment_id'] = $faculty_allotment_id;
						   $faculty_data['term_id'] = $term_id;
						   $faculty_data['cc_id'] = $faculty['cc_id'][$k];
						   $faculty_data['course_id'] = $faculty['course_id'][$k];
						   $faculty_data['course_type'] = 1;
						   $faculty_data['credit_value'] = $faculty['credit_value'][$k];
						 $faculty_data['department_id'] = $faculty['department_id'][$k];
						   $faculty_data['employee_id'] = $faculty['employee_id'][$k];
						   
							$CoursesFacultyAllotmentItems_model->insert($faculty_data);
					   }
					   $elective  = $this->getRequest()->getPost('elective');
					   $ElectivesFacultyAllotmentItems_model->trashItems($faculty_allotment_id);
					   foreach(array_filter($elective['elective_term_id']) as $key => $elective_term_id){
						   $elective_data['faculty_allotment_id'] = $faculty_allotment_id;
						   $elective_data['term_id'] = $elective_term_id;
						   $elective_data['elective_id'] = $elective['elective_id'][$key];
						 $elective_data['department_id'] = $elective['elective_department_id'][$key];
						   $elective_data['credit_value'] = 1;
						    $elective_data['course_type'] = 2;
						   $elective_data['employee_id'] = $elective['elective_employee_id'][$key];
						   $ElectivesFacultyAllotmentItems_model->insert($elective_data);
					   }
                        $this->_flashMessenger->addMessage('Details  Updated Successfully');

                        $this->_redirect('electives-allotment/index');

                    }

                }

                break;

            case 'delete':

                $data['status'] = 2;

                if ($faculty_allotment_id)

                    $ElectivesFacultyAllotment_model->update($data, array('faculty_allotment_id=?' => $faculty_allotment_id));

                $this->_flashMessenger->addMessage('Details Deleted Successfully');

                $this->_redirect('electives-allotment/index');

                break;

            default:

                $messages = $this->_flashMessenger->getMessages();

                $this->view->messages = $messages;

                $result = $ElectivesFacultyAllotment_model->getRecords();
				//print_r($result); die;

                $page = $this->_getParam('page', 1);

                $paginator_data = array(

                    'page' => $page,

                    'result' => $result

                );

                $this->view->paginator = $this->_act->pagination($paginator_data);

                break;

        }

    }
	public function ajaxGetSecondYearTermsAction()
    {
        $this->_helper->layout->disableLayout();
        if($this->_request->isPost () && $this->getRequest ()->isXmlHttpRequest ())
        {
            $academic_year_id = $this->_getParam("academic_year_id");  
			$alloted_id = $this->_getParam("alloted_id");	
           $this->view->alloted_id = $alloted_id;			
			if(!empty($alloted_id)){
				
				$ElectivesFacultyAllotmentItems_model = new Application_Model_ElectivesFacultyAllotmentItems();
				$electives_result = $ElectivesFacultyAllotmentItems_model->getRecords($alloted_id);
				$this->view->electives_result = $electives_result;
				
				$CoursesFacultyAllotmentItems_model = new Application_Model_CoursesFacultyAllotmentItems();
				$result = $CoursesFacultyAllotmentItems_model->getRecords($alloted_id);
				$this->view->result = $result;
				
					// $HRMModel_model = new Application_Model_HRMModel();
					// $department = $HRMModel_model->getDepartments();
					// $this->view->department = $department;
						
				  $HRMModel_model = new Application_Model_HRMModel();
				  $employee = $HRMModel_model->getEmployeeIds();
				  $this->view->employee = $employee;
				
			}else{
				if(!empty($academic_year_id)){
			$Corecourselearning_model = new Application_Model_Corecourselearning();
			$result = $Corecourselearning_model->getSecondyearCourses($academic_year_id);
			$this->view->result = $result;
			// $HRMModel_model = new Application_Model_HRMModel();
			//  $department = $HRMModel_model->getDepartments();
			//  $this->view->department = $department;
			  $HRMModel_model = new Application_Model_HRMModel();
			  $employee = $HRMModel_model->getEmployeeIds();
			  $this->view->employee = $employee;
			  
			  $ElectiveSelection_model = new Application_Model_ElectiveSelection();
			  $electives_result = $ElectiveSelection_model->getElectives($academic_year_id);
			  $this->view->electives_result = $electives_result;
				}
			}
            
        }
    }
	public function ajaxGetemployeeAction(){
        $this->_helper->layout->disableLayout();
         if ($this->_request->isPost () && $this->getRequest ()->isXmlHttpRequest ()) {
			$department_id = $this->_getParam("department_id");			
			if($department_id){ 
				$HRMModel = new Application_Model_HRMModel();
				$department = $HRMModel->getEmployee($department_id);
			
				echo '<option value="">Select </option>';
				foreach($department as $k => $val){
					echo '<option value="'.$k.'" >'.$val.'</option>';	
				}
							
			}               
        }die;  
	}	
	
	public function ajaxGetSecondYearTermsDataAction(){
		$this->_helper->layout->disableLayout();
        if ($this->_request->isPost () && $this->getRequest ()->isXmlHttpRequest ()) {
			$academic_year_id = $this->_getParam("academic_year_id");
			//$year_id = $this->_getParam("year_id");
			//echo $academic_year_id;die;
			$ElectivesFacultyAllotment_model = new Application_Model_ElectivesFacultyAllotment();
			$grade_result= $ElectivesFacultyAllotment_model->getValidFacultyDataRecord($academic_year_id);
			$counts = count($grade_result['faculty_allotment_id']);
			//print_r($counts);die;
			echo json_encode($counts);die;
			$this->view->grade_result = $grade_result;
		}
	}
	
	
	public function evaluationComponentsForElectivesAction()
	{
			
		$this->view->action_name = 'Evaluation Components For Electives';

        $this->view->sub_title_name = 'evaluation-components-for-electives';

        $ElectivesEvaluationComponents_model = new Application_Model_ElectivesEvaluationComponents();
		
		$ElectivesEvaluationComponentsItems_model = new Application_Model_ElectivesEvaluationComponentsItems();
		
		$CoursesEvaluationComponentsItems_model = new Application_Model_CoursesEvaluationComponentsItems();
		
		$ElectivesEvaluationComponents_form = new Application_Form_ElectivesEvaluationComponents();

        $ele_com_id = $this->_getParam("id");

        $type = $this->_getParam("type");

        switch ($type) {

            case "add":

                $this->view->type = $type;

                $this->view->form = $ElectivesEvaluationComponents_form;

                if ($this->getRequest()->isPost()) {

                    if ($ElectivesEvaluationComponents_form->isValid($this->getRequest()->getPost())) {

                        $data = $ElectivesEvaluationComponents_form->getValues();

                       $last_insert_id =  $ElectivesEvaluationComponents_model->insert($data);
						$ElectivesFacultyAllotment_model= new Application_Model_ElectivesFacultyAllotment();
					$employee_result= $ElectivesFacultyAllotment_model->getElectiveEmployeeTerms($data['academic_year_id'],$data['department_id'],$data['employee_id']);
						for($i=0;$i<count($employee_result);$i++)
						{
						$components = $_POST['components'];
						
													
						for($k=0;$k<count($_POST['components']['component_name_'.$employee_result[$i]['term_id'].'_'.$employee_result[$i]['course_id']]);$k++){
						$item_data['el_ec_id'] = $last_insert_id;
						$item_data['term_id']=$employee_result[$i]['term_id'];
						$item_data['course_id']=$employee_result[$i]['course_id'];
						$item_data['course_type'] = 1;
						$item_data['component_name'] = $components['component_name_'.$employee_result[$i]['term_id'].'_'.$employee_result[$i]['course_id']][$k];
						$item_data['weightage'] = $components['weightage_'.$employee_result[$i]['term_id'].'_'.$employee_result[$i]['course_id']][$k];
						$item_data['remaining_weightage'] = $components['remaining_weightage_'.$employee_result[$i]['term_id'].'_'.$employee_result[$i]['course_id']][$k];
						//echo '<pre>'; print_r($item_data); die;
						$CoursesEvaluationComponentsItems_model->insert($item_data);
												
						
						}
						}
					
				$electives_result = $ElectivesFacultyAllotment_model->getElectiveEmployeeElectives($data['academic_year_id'],$data['department_id'],$data['employee_id']);
					for($j=0;$j<count($electives_result);$j++)
						{
						$electives = $_POST['electives'];
						
													
						for($l=0;$l<count($_POST['electives']['component_name_'.$electives_result[$j]['term_id'].'_'.$electives_result[$j]['elective_id']]);$l++){
						$ele_item_data['el_ec_id'] = $last_insert_id;
						$ele_item_data['term_id']=$electives_result[$j]['term_id'];
						$ele_item_data['elective_id']=$electives_result[$j]['elective_id'];
						$ele_item_data['course_type'] = 2;
						$ele_item_data['component_name'] = $electives['component_name_'.$electives_result[$j]['term_id'].'_'.$electives_result[$j]['elective_id']][$l];
						$ele_item_data['weightage'] = $electives['weightage_'.$electives_result[$j]['term_id'].'_'.$electives_result[$j]['elective_id']][$l];
						$ele_item_data['remaining_weightage'] = $electives['remaining_weightage_'.$electives_result[$j]['term_id'].'_'.$electives_result[$j]['elective_id']][$l];
						$ElectivesEvaluationComponentsItems_model->insert($ele_item_data);
						
												
						
						}
						}
				
                        $this->_flashMessenger->addMessage('Details Added Successfully ');

                        $this->_redirect('electives-allotment/evaluation-components-for-electives');

                    } 

                }

                break;

            case 'edit':

                $this->view->type = $type;

                $this->view->form = $ElectivesEvaluationComponents_form;

                $result = $ElectivesEvaluationComponents_model->getRecord($ele_com_id); 

                $ElectivesEvaluationComponents_form->populate($result);
					$ElectivesFacultyAllotment_model= new Application_Model_ElectivesFacultyAllotment();
					$employee_result= $ElectivesFacultyAllotment_model->getElectiveEmployeeTerms($result['academic_year_id'],$result['department_id'],$result['employee_id']);
					$this->view->employee_result = $employee_result;
					$electives_result = $ElectivesFacultyAllotment_model->getElectiveEmployeeElectives($result['academic_year_id'],$result['department_id'],$result['employee_id']);
					$this->view->electives_result = $electives_result;
					$courses_item_result = $CoursesEvaluationComponentsItems_model->getRecords($ele_com_id);
					$this->view->courses_item_result = $courses_item_result;
					$electives_item_result = $ElectivesEvaluationComponentsItems_model->getRecords($ele_com_id);
					$this->view->electives_item_result = $electives_item_result;

                if ($this->getRequest()->isPost()) {

                    if ($ElectivesEvaluationComponents_form->isValid($this->getRequest()->getPost())) {

                        $data = $ElectivesEvaluationComponents_form->getValues();

                        $ElectivesEvaluationComponents_model->update($data, array('el_ec_id=?' => $ele_com_id));
						$CoursesEvaluationComponentsItems_model->trashItems($ele_com_id);$ElectivesEvaluationComponentsItems_model->trashItems($ele_com_id);
						
						$ElectivesFacultyAllotment_model= new Application_Model_ElectivesFacultyAllotment();
					$employee_result1= $ElectivesFacultyAllotment_model->getElectiveEmployeeTerms($data['academic_year_id'],$data['department_id'],$data['employee_id']);
						for($i=0;$i<count($employee_result1);$i++)
						{
						$components = $_POST['components'];
						
													
						for($k=0;$k<count($_POST['components']['component_name_'.$employee_result1[$i]['term_id']]);$k++){
						$item_data['el_ec_id'] = $ele_com_id;
						$item_data['term_id']=$employee_result1[$i]['term_id'];
						$item_data['course_id']=$employee_result1[$i]['course_id'];
						$item_data['course_type'] = 1;
						$item_data['component_name'] = $components['component_name_'.$employee_result1[$i]['term_id']][$k];
						$item_data['weightage'] = $components['weightage_'.$employee_result1[$i]['term_id']][$k];
						$item_data['remaining_weightage'] = $components['remaining_weightage_'.$employee_result1[$i]['term_id']][$k];
						$CoursesEvaluationComponentsItems_model->insert($item_data);
												
						
						}
						}
					
				$electives_result1 = $ElectivesFacultyAllotment_model->getElectiveEmployeeElectives($data['academic_year_id'],$data['department_id'],$data['employee_id']);
					for($j=0;$j<count($electives_result1);$j++)
						{
						$electives = $_POST['electives'];
						
						for($l=0;$l<count($_POST['electives']['component_name_'.$electives_result1[$j]['term_id'].'_'.$electives_result1[$j]['elective_id']]);$l++){
						$ele_item_data['el_ec_id'] = $ele_com_id;
						$ele_item_data['term_id']=$electives_result1[$j]['term_id'];
						$ele_item_data['elective_id']=$electives_result1[$j]['elective_id'];
						$ele_item_data['course_type'] = 2;
						$ele_item_data['component_name'] = $electives['component_name_'.$electives_result1[$j]['term_id'].'_'.$electives_result1[$j]['elective_id']][$l];
						$ele_item_data['weightage'] = $electives['weightage_'.$electives_result1[$j]['term_id'].'_'.$electives_result1[$j]['elective_id']][$l];
						$ele_item_data['remaining_weightage'] = $electives['remaining_weightage_'.$electives_result1[$j]['term_id'].'_'.$electives_result1[$j]['elective_id']][$l];
						//echo '<pre>'; print_r($ele_item_data); 
						$ElectivesEvaluationComponentsItems_model->insert($ele_item_data);
						
												
						
						}
						}
				

                        $this->_flashMessenger->addMessage('Details  Updated Successfully');

                      $this->_redirect('electives-allotment/evaluation-components-for-electives');

                    }

                }

                break;

            case 'delete':

                $data['status'] = 2;

                if ($ele_com_id)

                    $ElectivesEvaluationComponents_model->update($data, array('el_ec_id=?' => $ele_com_id));

                $this->_flashMessenger->addMessage('Details Deleted Successfully');

               $this->_redirect('electives-allotment/evaluation-components-for-electives');

                break;

            default:

                $messages = $this->_flashMessenger->getMessages();

                $this->view->messages = $messages;

                $result = $ElectivesEvaluationComponents_model->getRecords();
				//print_r($result); die;

                $page = $this->_getParam('page', 1);

                $paginator_data = array(

                    'page' => $page,

                    'result' => $result

                );

                $this->view->paginator = $this->_act->pagination($paginator_data);

                break;

        }

		}	
		public function ajaxGetElectiveEmployeeTermsAction(){
		 $this->_helper->layout->disableLayout();
        if ($this->_request->isPost () && $this->getRequest ()->isXmlHttpRequest ()) {
			 $department_id = $this->_getParam("department_id");
			 $employee_id = $this->_getParam("employee_id");
			 $academic_year_id = $this->_getParam("academic_year_id");
			 
				$ElectivesFacultyAllotment_model= new Application_Model_ElectivesFacultyAllotment();
				$result= $ElectivesFacultyAllotment_model->getElectiveEmployeeTerms($academic_year_id,$department_id,$employee_id);
				$this->view->result = $result;
				$electives_result = $ElectivesFacultyAllotment_model->getElectiveEmployeeElectives($academic_year_id,$department_id,$employee_id);
				$this->view->electives_result = $electives_result;
				
				
		}
	}
	
	public function ajaxGetElectiveEmployeeTermsDataAction(){
		$this->_helper->layout->disableLayout();
        if ($this->_request->isPost () && $this->getRequest ()->isXmlHttpRequest ()) {
			 $academic_year_id = $this->_getParam("academic_year_id");
			 $department_id = $this->_getParam("department_id");
			 $employee_id = $this->_getParam("employee_id");
			//print_r($academic_id); die;
			 $ElectivesEvaluationComponents_model= new Application_Model_ElectivesEvaluationComponents();
			 $result= $ElectivesEvaluationComponents_model->getEEComponentCount($academic_year_id,$department_id,$employee_id);
			 $counts = count($result['el_ec_id']);
			 echo json_encode($counts);die;
			 //echo '<pre>'; print_r($counts); die;
			// $this->view->result = $result;
		}
	}
	
	
	
	public function electivesGradeAllocationAction(){
		$this->view->action_name = 'Electives Grade Allocation';
        $this->view->sub_title_name = 'electives-grade-allocation';
        $ElectivesGradeAllocation_model = new Application_Model_ElectivesGradeAllocation();
		$ElectivesGradeAllocation_form = new Application_Form_ElectivesGradeAllocation();
		$ElectivesGradeAllocatonItems_model = new Application_Model_ElectivesGradeAllocationItems();
		$elective_grade_id = $this->_getParam("id");
		//print_r($feehead_id);die;
        $type = $this->_getParam("type");
		$this->view->type = $type;
        $this->view->form = $ElectivesGradeAllocation_form;
		
        switch ($type) {
            case "add":    
                if ($this->getRequest()->isPost()) {
                    if ($ElectivesGradeAllocation_form->isValid($this->getRequest()->getPost())) {
                        $data = $ElectivesGradeAllocation_form->getValues();
						$data['term_id'] = $this->getRequest()->getPost('term_id');
						$data['course_id'] = $this->getRequest()->getPost('course_id');
						/* if($data['course_type'] == '1'){
						$data['component_id'] = $this->getRequest()->getPost('component_id');
						$data['elective_component_id'] = 0;
						}else if($data['course_type'] == '2'){
							$data['elective_component_id'] = $this->getRequest()->getPost('component_id');
							$data['component_id'] = 0;
						} */
                        	$last_insert_id = $ElectivesGradeAllocation_model->insert($data);	
						 $grade = $this->getRequest()->getPost('grade');
							foreach(array_filter($grade['student_id']) as $k => $student_id){
								$grade_data['elective_grade_id'] = $last_insert_id;
								$grade_data['student_id'] = $student_id;
								if($data['course_type'] == '1'){ 
								$grade_data['component_id'] = implode(",",$grade['component_id']);
								$grade_data['elective_component_id'] = '';
								}
								else if($data['course_type'] == '2'){
									
									$grade_data['elective_component_id'] = implode(",",$grade['component_id']);
									$grade_data['component_id'] = '';
								}
								$grade_data['grade_value'] = implode(",",$grade['grade_value_'.$student_id.'']);
								$ElectivesGradeAllocatonItems_model->insert($grade_data);
							}	 
					
                        $this->_flashMessenger->addMessage('Electives Grade Allocation Successfully added');

                        $this->_redirect('electives-allotment/electives-grade-allocation');
						 
						}
                    }

                
                break;
            case 'edit': 
                $result = $ElectivesGradeAllocation_model->getRecord($elective_grade_id);
				$ElectivesGradeAllocation_form->populate($result);
				$this->view->grade_elective_allocate_id = $elective_grade_id;
				$this->view->result = $result;
                if ($this->getRequest()->isPost()) {
                    if ($ElectivesGradeAllocation_form->isValid($this->getRequest()->getPost())) {
                        $data = $ElectivesGradeAllocation_form->getValues();
						$data['term_id'] = $this->getRequest()->getPost('term_id');
						$data['course_id'] = $this->getRequest()->getPost('course_id');
						/* if($data['course_type'] == '1'){
						$data['component_id'] = $this->getRequest()->getPost('component_id');
						$data['elective_component_id'] = 0;
						}else if($data['course_type'] == '2'){
							$data['elective_component_id'] = $this->getRequest()->getPost('component_id');
							$data['component_id'] = 0;
						} */
                        $ElectivesGradeAllocation_model->update($data, array('elective_grade_id=?' => $elective_grade_id));
						
						  $grade = $this->getRequest()->getPost('grade');
						  $ElectivesGradeAllocatonItems_model->trashItems($elective_grade_id);
							foreach(array_filter($grade['student_id']) as $k => $student_id){
								$grade_data['elective_grade_id'] = $elective_grade_id;
								$grade_data['student_id'] = $student_id;
								if($data['course_type'] == '1'){ 
								$grade_data['component_id'] = implode(",",$grade['component_id']);
								$grade_data['elective_component_id'] = '';
								}
								else if($data['course_type'] == '2'){
									
									$grade_data['elective_component_id'] = implode(",",$grade['component_id']);
									$grade_data['component_id'] = '';
								}
								$grade_data['grade_value'] = implode(",",$grade['grade_value_'.$student_id.'']);
								$ElectivesGradeAllocatonItems_model->insert($grade_data);
							}
						
                        $this->_flashMessenger->addMessage('Details Updated Successfully');
                         $this->_redirect('electives-allotment/electives-grade-allocation');
                    } 
                }
                break;
            case 'delete':
                $data['status'] = 2;
                if ($elective_grade_id){
                    $ElectivesGradeAllocation_model->update($data, array('elective_grade_id=?' => $elective_grade_id));
					$this->_flashMessenger->addMessage('Details Deleted Successfully');
					$this->_redirect('electives-allotment/electives-grade-allocation');
				}
                break;
            default:
                $messages = $this->_flashMessenger->getMessages();
                $this->view->messages = $messages;
                $result = $ElectivesGradeAllocation_model->getRecords();
                $page = $this->_getParam('page', 1);
                $paginator_data = array(
                    'page' => $page,
                    'result' => $result
                );
                $this->view->paginator = $this->_act->pagination($paginator_data);
                break;
        }
	}
	public function ajaxGetTermsCourseWiseAction(){
		 $this->_helper->layout->disableLayout();
        if ($this->_request->isPost () && $this->getRequest ()->isXmlHttpRequest ()) {
			 $department_id = $this->_getParam("department_id");
			 $employee_id = $this->_getParam("employee_id");
			 $academic_year_id = $this->_getParam("academic_year_id");
			 $course_type = $this->_getParam("course_type");
			 $elective_grade_id = $this->_getParam("grade_elective_allocate_id");
			 if($elective_grade_id){
				 $ElectivesGradeAllocation_model = new Application_Model_ElectivesGradeAllocation();
				 $elective_grade_result = $ElectivesGradeAllocation_model->getRecord($elective_grade_id);
				 
			 }
				$ElectivesFacultyAllotment_model= new Application_Model_ElectivesFacultyAllotment();
				$result= $ElectivesFacultyAllotment_model->getTermsCoursewise($academic_year_id,$department_id,$employee_id,$course_type);
				echo '<div class="col-sm-3 employee_class">';
                echo  '<div class="form-group">';
                 echo  '<label class="control-label">Terms</label>';
				echo '<select type="text" name="term_id" id="term_id" class="form-control">';
				echo '<option value="">Select</option>';
				foreach($result as $k => $val)
				{
					
					$selected = '';
					if($k == $elective_grade_result['term_id']){
						
						$selected = "selected";
					}
					echo '<option value="'.$k.'" '.$selected.' >'.$val.'</option>';	
					
				}
				echo '</select>';
				echo '</div></div>';
				
				
				
		}die;
	}
	
	public function ajaxGetTermsCourseWiseForGradesAction(){
		 $this->_helper->layout->disableLayout();
        if ($this->_request->isPost () && $this->getRequest ()->isXmlHttpRequest ()) {
			 $department_id = $this->_getParam("department_id");
			 $employee_id = $this->_getParam("employee_id");
			 $academic_year_id = $this->_getParam("academic_year_id");
			 $course_type = $this->_getParam("course_type");
			 $elective_component_grade_id = $this->_getParam("elective_component_grade_id");
			 if($elective_component_grade_id){
				 $ElectivesEvaluationComponentsGradeMaster_model = new Application_Model_ElectivesEvaluationComponentsGradeMaster();
				 $elective_component_grade_result = $ElectivesEvaluationComponentsGradeMaster_model->getRecord($elective_component_grade_id);
				 
			 } 
				$ElectivesFacultyAllotment_model= new Application_Model_ElectivesFacultyAllotment();
				$result= $ElectivesFacultyAllotment_model->getTermsCoursewise($academic_year_id,$department_id,$employee_id,$course_type);
				echo '<div class="col-sm-3 employee_class">';
                echo  '<div class="form-group">';
                 echo  '<label class="control-label">Terms</label>';
				echo '<select type="text" name="term_id" id="term_id" class="form-control">';
				echo '<option value="">Select</option>';
				foreach($result as $k => $val)
				{
					
					$selected = "";
					if($k == $elective_component_grade_result['term_id']){
						$selected = "selected";
						
					}
					echo '<option value="'.$k.'"  '.$selected.'>'.$val.'</option>';	
					
				}
				echo '</select>';
				echo '</div></div>';
				
				
				
		}die;
	}
	
	public function ajaxGetTermsElectivesWiseAction(){
		 $this->_helper->layout->disableLayout();
        if ($this->_request->isPost () && $this->getRequest ()->isXmlHttpRequest ()) {
			 $department_id = $this->_getParam("department_id");
			 $employee_id = $this->_getParam("employee_id");
			 $academic_year_id = $this->_getParam("academic_year_id");
			 $course_type = $this->_getParam("course_type");
			 $elective_grade_id = $this->_getParam("grade_elective_allocate_id");
			 if($elective_grade_id){
				 $ElectivesGradeAllocation_model = new Application_Model_ElectivesGradeAllocation();
				 $elective_grade_result = $ElectivesGradeAllocation_model->getRecord($elective_grade_id);
				 
			 }
				$ElectivesFacultyAllotment_model= new Application_Model_ElectivesFacultyAllotment();
				$result= $ElectivesFacultyAllotment_model->getTermsElectivesWise($academic_year_id,$department_id,$employee_id,$course_type);
				echo '<div class="col-sm-3 employee_class">';
                echo  '<div class="form-group">';
                 echo  '<label class="control-label">Terms</label>';
				echo '<select type="text" name="term_id" id="term_id" class="form-control">';
				echo '<option value="">Select</option>';
				foreach($result as $k => $val)
				{
					
					$selected = '';
					if($k == $elective_grade_result['term_id']){
						
						$selected = "selected";
					}
					echo '<option value="'.$k.'"  '.$selected.'>'.$val.'</option>';	
					
				}
				echo '</select>';
				echo '</div></div>';
				
				
				
		}die;
	}
	public function ajaxGetTermsElectivesWiseForGradesAction(){
		 $this->_helper->layout->disableLayout();
        if ($this->_request->isPost () && $this->getRequest ()->isXmlHttpRequest ()) {
			 $department_id = $this->_getParam("department_id");
			 $employee_id = $this->_getParam("employee_id");
			 $academic_year_id = $this->_getParam("academic_year_id");
			 $course_type = $this->_getParam("course_type");
			 $elective_component_grade_id = $this->_getParam("elective_component_grade_id");
			 if($elective_component_grade_id){
				 $ElectivesEvaluationComponentsGradeMaster_model = new Application_Model_ElectivesEvaluationComponentsGradeMaster();
				 $elective_component_grade_result = $ElectivesEvaluationComponentsGradeMaster_model->getRecord($elective_component_grade_id);
				 
			 } 
				$ElectivesFacultyAllotment_model= new Application_Model_ElectivesFacultyAllotment();
				$result= $ElectivesFacultyAllotment_model->getTermsElectivesWise($academic_year_id,$department_id,$employee_id,$course_type);
				echo '<div class="col-sm-3 employee_class">';
                echo  '<div class="form-group">';
                 echo  '<label class="control-label">Terms</label>';
				echo '<select type="text" name="term_id" id="term_id" class="form-control">';
				echo '<option value="">Select</option>';
				foreach($result as $k => $val)
				{
					
					$selected = '';
					if($k == $elective_component_grade_result['term_id']){
						
						$selected = "selected";
					}
					echo '<option value="'.$k.'"  '.$selected.'>'.$val.'</option>';	
					
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
			 $course_type = $this->_getParam("course_type");
			 $term_id = $this->_getParam("term_id");
			 $elective_grade_id = $this->_getParam("grade_elective_allocate_id");
			 if($elective_grade_id){
				 $ElectivesGradeAllocation_model = new Application_Model_ElectivesGradeAllocation();
				 $elective_grade_result = $ElectivesGradeAllocation_model->getRecord($elective_grade_id);
				 
			 }
				$ElectivesFacultyAllotment_model= new Application_Model_ElectivesFacultyAllotment();
				$result= $ElectivesFacultyAllotment_model->getComponentCourses($academic_year_id,$department_id,$employee_id,$course_type,$term_id);
				echo '<div class="col-sm-3 employee_class">';
                echo  '<div class="form-group">';
                 echo  '<label class="control-label">Courses</label>';
				echo '<select type="text" name="course_id" id="course_id" class="form-control">';
				echo '<option value="">Select</option>';
				foreach($result as $k => $val)
				{
					$selected = '';
					if($k == $elective_grade_result['course_id']){
						
						$selected = "selected";
						
					}
					echo '<option value="'.$k.'" '.$selected.' >'.$val.'</option>';	
					
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
			 $course_type = $this->_getParam("course_type");
			 $term_id = $this->_getParam("term_id");
			 $elective_grade_id = $this->_getParam("grade_elective_allocate_id");
			 if($elective_grade_id){
				 $ElectivesGradeAllocation_model = new Application_Model_ElectivesGradeAllocation();
				 $elective_grade_result = $ElectivesGradeAllocation_model->getRecord($elective_grade_id);
				 
			 }
				$ElectivesFacultyAllotment_model= new Application_Model_ElectivesFacultyAllotment();
				$result= $ElectivesFacultyAllotment_model->getEditComponentCourses($academic_year_id,$department_id,$employee_id,$course_type,$term_id);
				echo '<div class="col-sm-3 employee_class">';
                echo  '<div class="form-group">';
                 echo  '<label class="control-label">Courses</label>';
				echo '<select type="text" name="course_id" id="course_id" class="form-control">';
				echo '<option value="">Select</option>';
				foreach($result as $k => $val)
				{
					$selected = '';
					if($k == $elective_grade_result['course_id']){
						
						$selected = "selected";
						
					}
					echo '<option value="'.$k.'" '.$selected.' >'.$val.'</option>';	
					
				}
				echo '</select>';
				echo '</div></div>';
				
				
				
		}die;
		
	}	
	
	public function ajaxGetCoursesForGradesAction()
	{
		$this->_helper->layout->disableLayout();
        if ($this->_request->isPost () && $this->getRequest ()->isXmlHttpRequest ()) {
			 $department_id = $this->_getParam("department_id");
			 $employee_id = $this->_getParam("employee_id");
			 $academic_year_id = $this->_getParam("academic_year_id");
			 $course_type = $this->_getParam("course_type");
			 $term_id = $this->_getParam("term_id");
			 $elective_component_grade_id = $this->_getParam("elective_component_grade_id");
			 if($elective_component_grade_id){
				 $ElectivesEvaluationComponentsGradeMaster_model = new Application_Model_ElectivesEvaluationComponentsGradeMaster();
				 $elective_component_grade_result = $ElectivesEvaluationComponentsGradeMaster_model->getRecord($elective_component_grade_id);
				 
			 } 
				$ElectivesFacultyAllotment_model= new Application_Model_ElectivesFacultyAllotment();
				$result= $ElectivesFacultyAllotment_model->getCourses($academic_year_id,$department_id,$employee_id,$course_type,$term_id);
				echo '<div class="col-sm-3 employee_class">';
                echo  '<div class="form-group">';
                 echo  '<label class="control-label">Courses</label>';
				echo '<select type="text" name="course_id" id="course_id" class="form-control">';
				echo '<option value="">Select</option>';
				foreach($result as $k => $val)
				{
					$selected = "";
					
					if($k == $elective_component_grade_result["course_id"]){
						$selected = "selected";
						
					}
					
					echo '<option value="'.$k.'" '.$selected.'>'.$val.'</option>';	
					
				}
				echo '</select>';
				echo '</div></div>';
				
				
				
		}die;
		
	}	
	public function ajaxGetElectivesAction()
	{
		$this->_helper->layout->disableLayout();
        if ($this->_request->isPost () && $this->getRequest ()->isXmlHttpRequest ()) {
			 $department_id = $this->_getParam("department_id");
			 $employee_id = $this->_getParam("employee_id");
			 $academic_year_id = $this->_getParam("academic_year_id");
			 $course_type = $this->_getParam("course_type");
			 $term_id = $this->_getParam("term_id");
			  $elective_grade_id = $this->_getParam("grade_elective_allocate_id");
			 if($elective_grade_id){
				 $ElectivesGradeAllocation_model = new Application_Model_ElectivesGradeAllocation();
				 $elective_grade_result = $ElectivesGradeAllocation_model->getRecord($elective_grade_id);
				 
			 }
				$ElectivesFacultyAllotment_model= new Application_Model_ElectivesFacultyAllotment();
				$result= $ElectivesFacultyAllotment_model->getElectivesCourses($academic_year_id,$department_id,$employee_id,$course_type,$term_id);
				echo '<div class="col-sm-3 employee_class">';
                echo  '<div class="form-group">';
                 echo  '<label class="control-label">Electives</label>';
				echo '<select type="text" name="course_id" id="course_id" class="form-control">';
				echo '<option value="">Select</option>';
				foreach($result as $k => $val)
				{
					$selected = '';
					if($k == $elective_grade_result['course_id']){
						
						$selected = "selected";
						
					}
					echo '<option value="'.$k.'" '.$selected.'  >'.$val.'</option>';	
					
				}
				echo '</select>';
				echo '</div></div>';
				
				
				
		}die;
		
	}	
	public function ajaxGetElectivesEditAction()
	{
		$this->_helper->layout->disableLayout();
        if ($this->_request->isPost () && $this->getRequest ()->isXmlHttpRequest ()) {
			 $department_id = $this->_getParam("department_id");
			 $employee_id = $this->_getParam("employee_id");
			 $academic_year_id = $this->_getParam("academic_year_id");
			 $course_type = $this->_getParam("course_type");
			 $term_id = $this->_getParam("term_id");
			  $elective_grade_id = $this->_getParam("grade_elective_allocate_id");
			 if($elective_grade_id){
				 $ElectivesGradeAllocation_model = new Application_Model_ElectivesGradeAllocation();
				 $elective_grade_result = $ElectivesGradeAllocation_model->getRecord($elective_grade_id);
				 
			 }
				$ElectivesFacultyAllotment_model= new Application_Model_ElectivesFacultyAllotment();
				$result= $ElectivesFacultyAllotment_model->getElectivesCoursesEdit($academic_year_id,$department_id,$employee_id,$course_type,$term_id);
				echo '<div class="col-sm-3 employee_class">';
                echo  '<div class="form-group">';
                 echo  '<label class="control-label">Electives</label>';
				echo '<select type="text" name="course_id" id="course_id" class="form-control">';
				echo '<option value="">Select</option>';
				foreach($result as $k => $val)
				{
					$selected = '';
					if($k == $elective_grade_result['course_id']){
						
						$selected = "selected";
						
					}
					echo '<option value="'.$k.'" '.$selected.'  >'.$val.'</option>';	
					
				}
				echo '</select>';
				echo '</div></div>';
				
				
				
		}die;
		
	}	
	public function ajaxGetElectivesForGradesAction()
	{
		$this->_helper->layout->disableLayout();
        if ($this->_request->isPost () && $this->getRequest ()->isXmlHttpRequest ()) {
			 $department_id = $this->_getParam("department_id");
			 $employee_id = $this->_getParam("employee_id");
			 $academic_year_id = $this->_getParam("academic_year_id");
			 $course_type = $this->_getParam("course_type");
			 $term_id = $this->_getParam("term_id");
			  $elective_component_grade_id = $this->_getParam("elective_component_grade_id");
			 if($elective_component_grade_id){
				 $ElectivesEvaluationComponentsGradeMaster_model = new Application_Model_ElectivesEvaluationComponentsGradeMaster();
				 $elective_component_grade_result = $ElectivesEvaluationComponentsGradeMaster_model->getRecord($elective_component_grade_id);
				 
			 } 
				$ElectivesFacultyAllotment_model= new Application_Model_ElectivesFacultyAllotment();
				$result= $ElectivesFacultyAllotment_model->getElectives($academic_year_id,$department_id,$employee_id,$course_type,$term_id);
				echo '<div class="col-sm-3 employee_class">';
                echo  '<div class="form-group">';
                 echo  '<label class="control-label">Electives</label>';
				echo '<select type="text" name="course_id" id="course_id" class="form-control">';
				echo '<option value="">Select</option>';
				foreach($result as $k => $val)
				{
					$selected = '';
					if($k == $elective_component_grade_result['course_id']){
						$selected = "selected";
						
					}
					echo '<option value="'.$k.'" '.$selected.'>'.$val.'</option>';	
					
				}
				echo '</select>';
				echo '</div></div>';
				
				
				
		}die;
		
	}	
	public function ajaxGetComponentsAction()
	{
		$this->_helper->layout->disableLayout();
        if ($this->_request->isPost () && $this->getRequest ()->isXmlHttpRequest ()) {
			 $department_id = $this->_getParam("department_id");
			 $employee_id = $this->_getParam("employee_id");
			 $academic_year_id = $this->_getParam("academic_year_id");
			 $course_type = $this->_getParam("course_type");
			 $term_id = $this->_getParam("term_id");
			 $course_id = $this->_getParam("course_id");
			  $elective_grade_id = $this->_getParam("grade_elective_allocate_id");
			 if($elective_grade_id){
				 $ElectivesGradeAllocation_model = new Application_Model_ElectivesGradeAllocation();
				 $elective_grade_result = $ElectivesGradeAllocation_model->getRecord($elective_grade_id);
				 
			 }
				$ElectivesEvaluationComponents_model= new Application_Model_ElectivesEvaluationComponents();
				$result= $ElectivesEvaluationComponents_model->getComponents($academic_year_id,$department_id,$employee_id,$course_type,$term_id,$course_id);
				
				echo '<div class="col-sm-3 employee_class">';
                echo  '<div class="form-group">';
                 echo  '<label class="control-label">Components</label>';
				echo '<select type="text" name="component_id" id="component_id" class="form-control">';
				echo '<option value="">Select</option>';
				foreach($result as $k => $val)
				{
					$selected = '';
					if($k == $elective_grade_result['component_id']){
						$selected = "selected";
						
					}
					echo '<option value="'.$k.'" '.$selected.' >'.$val.'</option>';	
					
				}
				echo '</select>';
				echo '</div></div>';
				
				
				
		}die;
		
	}	
	public function ajaxGetComponentsAddAction()
	{
		$this->_helper->layout->disableLayout();
        if ($this->_request->isPost () && $this->getRequest ()->isXmlHttpRequest ()) {
			 $department_id = $this->_getParam("department_id");
			 $employee_id = $this->_getParam("employee_id");
			 $academic_year_id = $this->_getParam("academic_year_id");
			 $course_type = $this->_getParam("course_type");
			 $term_id = $this->_getParam("term_id");
			 $course_id = $this->_getParam("course_id");
			 
				$ElectivesEvaluationComponents_model= new Application_Model_ElectivesEvaluationComponents();
				$result= $ElectivesEvaluationComponents_model->getComponentsForGradeAllocation($academic_year_id,$department_id,$employee_id,$course_type,$term_id,$course_id);
				
				echo '<div class="col-sm-3 employee_class">';
                echo  '<div class="form-group">';
                 echo  '<label class="control-label">Components</label>';
				echo '<select type="text" name="component_id" id="component_id" class="form-control">';
				echo '<option value="">Select</option>';
				foreach($result as $k => $val)
				{
					
					echo '<option value="'.$k.'"  >'.$val.'</option>';	
					
				}
				echo '</select>';
				echo '</div></div>';
				
				
				
		}die;
		
	}	
	public function ajaxGetElectivesComponentsAction()
	{
		$this->_helper->layout->disableLayout();
        if ($this->_request->isPost () && $this->getRequest ()->isXmlHttpRequest ()) {
			 $department_id = $this->_getParam("department_id");
			 $employee_id = $this->_getParam("employee_id");
			 $academic_year_id = $this->_getParam("academic_year_id");
			 $course_type = $this->_getParam("course_type");
			 $term_id = $this->_getParam("term_id");
			 $course_id = $this->_getParam("course_id");
			  $elective_grade_id = $this->_getParam("grade_elective_allocate_id");
			 if($elective_grade_id){
				 $ElectivesGradeAllocation_model = new Application_Model_ElectivesGradeAllocation();
				 $elective_grade_result = $ElectivesGradeAllocation_model->getRecord($elective_grade_id);
				 
			 }
				$ElectivesEvaluationComponents_model= new Application_Model_ElectivesEvaluationComponents();
				$result= $ElectivesEvaluationComponents_model->getElectivesComponents($academic_year_id,$department_id,$employee_id,$course_type,$term_id,$course_id);
				
				echo '<div class="col-sm-3 employee_class">';
                echo  '<div class="form-group">';
                 echo  '<label class="control-label">Components</label>';
				echo '<select type="text" name="component_id" id="component_id" class="form-control">';
				echo '<option value="">Select</option>';
				foreach($result as $k => $val)
				{
					$selected = '';
					if($k == $elective_grade_result['elective_component_id']){
						$selected = "selected";
						
					}
					echo '<option value="'.$k.'" '.$selected.' >'.$val.'</option>';	
					
				}
				echo '</select>';
				echo '</div></div>';
				
				
				
		}die;
		
	}
	public function ajaxGetElectivesComponentsAddAction()
	{
		$this->_helper->layout->disableLayout();
        if ($this->_request->isPost () && $this->getRequest ()->isXmlHttpRequest ()) {
			 $department_id = $this->_getParam("department_id");
			 $employee_id = $this->_getParam("employee_id");
			 $academic_year_id = $this->_getParam("academic_year_id");
			 $course_type = $this->_getParam("course_type");
			 $term_id = $this->_getParam("term_id");
			 $course_id = $this->_getParam("course_id");
			  
				$ElectivesEvaluationComponents_model= new Application_Model_ElectivesEvaluationComponents();
				$result= $ElectivesEvaluationComponents_model->getElectivesComponentsForGradeAllocation($academic_year_id,$department_id,$employee_id,$course_type,$term_id,$course_id);
				
				echo '<div class="col-sm-3 employee_class">';
                echo  '<div class="form-group">';
                 echo  '<label class="control-label">Components</label>';
				echo '<select type="text" name="component_id" id="component_id" class="form-control">';
				echo '<option value="">Select</option>';
				foreach($result as $k => $val)
				{
					
					echo '<option value="'.$k.'" '.$selected.' >'.$val.'</option>';	
					
				}
				echo '</select>';
				echo '</div></div>';
				
				
				
		}die;
		
	}
	public function ajaxGetComponentsForGradesAction()
	{
		$this->_helper->layout->disableLayout();
        if ($this->_request->isPost () && $this->getRequest ()->isXmlHttpRequest ()) {
			 $department_id = $this->_getParam("department_id");
			 $employee_id = $this->_getParam("employee_id");
			 $academic_year_id = $this->_getParam("academic_year_id");
			 $course_type = $this->_getParam("course_type");
			 $term_id = $this->_getParam("term_id");
			 $course_id = $this->_getParam("course_id");
			  $elective_component_grade_id = $this->_getParam("elective_component_grade_id");
			 if($elective_component_grade_id){
				 $ElectivesEvaluationComponentsGradeMaster_model = new Application_Model_ElectivesEvaluationComponentsGradeMaster();
				 $elective_component_grade_result = $ElectivesEvaluationComponentsGradeMaster_model->getRecord($elective_component_grade_id);
				 
			 }
				$ElectivesEvaluationComponents_model= new Application_Model_ElectivesEvaluationComponents();
				$result= $ElectivesEvaluationComponents_model->getComponents($academic_year_id,$department_id,$employee_id,$course_type,$term_id,$course_id);
				
				echo '<div class="col-sm-3 employee_class">';
                echo  '<div class="form-group">';
                 echo  '<label class="control-label">Components</label>';
				echo '<select type="text" name="component_id" id="component_id" class="form-control">';
				echo '<option value="">Select</option>';
				foreach($result as $k => $val)
				{
					$selected = '';
					if($k == $elective_component_grade_result['component_id'])
					{
						
						$selected = "selected";
						
					}
					
					echo '<option value="'.$k.'" '.$selected.' >'.$val.'</option>';	
					
				}
				echo '</select>';
				echo '</div></div>';
				
				
				
		}die;
		
	}	
	public function ajaxGetComponentsForGradesAddAction()
	{
		$this->_helper->layout->disableLayout();
        if ($this->_request->isPost () && $this->getRequest ()->isXmlHttpRequest ()) {
			 $department_id = $this->_getParam("department_id");
			 $employee_id = $this->_getParam("employee_id");
			 $academic_year_id = $this->_getParam("academic_year_id");
			 $course_type = $this->_getParam("course_type");
			 $term_id = $this->_getParam("term_id");
			 $course_id = $this->_getParam("course_id");
			  
				$ElectivesEvaluationComponents_model= new Application_Model_ElectivesEvaluationComponents();
				$result= $ElectivesEvaluationComponents_model->getAddComponents($academic_year_id,$department_id,$employee_id,$course_type,$term_id,$course_id);
				
				echo '<div class="col-sm-3 employee_class">';
                echo  '<div class="form-group">';
                 echo  '<label class="control-label">Components</label>';
				echo '<select type="text" name="component_id" id="component_id" class="form-control">';
				echo '<option value="">Select</option>';
				foreach($result as $k => $val)
				{
					$selected = '';
					
					
					echo '<option value="'.$k.'"  >'.$val.'</option>';	
					
				}
				echo '</select>';
				echo '</div></div>';
				
				
				
		}die;
		
	}	
	public function ajaxGetElectivesComponentsForGradesAction()
	{
		$this->_helper->layout->disableLayout();
        if ($this->_request->isPost () && $this->getRequest ()->isXmlHttpRequest ()) {
			 $department_id = $this->_getParam("department_id");
			 $employee_id = $this->_getParam("employee_id");
			 $academic_year_id = $this->_getParam("academic_year_id");
			 $course_type = $this->_getParam("course_type");
			 $term_id = $this->_getParam("term_id");
			 $course_id = $this->_getParam("course_id");
			  $elective_component_grade_id = $this->_getParam("elective_component_grade_id");
			 if($elective_component_grade_id){
				 $ElectivesEvaluationComponentsGradeMaster_model = new Application_Model_ElectivesEvaluationComponentsGradeMaster();
				 $elective_component_grade_result = $ElectivesEvaluationComponentsGradeMaster_model->getRecord($elective_component_grade_id);
				 
			 }
			 
				$ElectivesEvaluationComponents_model= new Application_Model_ElectivesEvaluationComponents();
				$result= $ElectivesEvaluationComponents_model->getElectivesComponents($academic_year_id,$department_id,$employee_id,$course_type,$term_id,$course_id);
				echo '<div class="col-sm-3 employee_class">';
                echo  '<div class="form-group">';
                 echo  '<label class="control-label">Components</label>';
				echo '<select type="text" name="component_id" id="component_id" class="form-control">';
				echo '<option value="">Select</option>';
				foreach($result as $k => $val)
				{
					$selected = '';
					if($k == $elective_component_grade_result['elective_component_id']){
						$selected = "selected";
						
					}
					echo '<option value="'.$k.'"  '.$selected.'>'.$val.'</option>';	
					
				}
				echo '</select>';
				echo '</div></div>';
				
				
				
		}die;
		
	}
	public function ajaxGetElectivesComponentsForGradesAddAction()
	{
		$this->_helper->layout->disableLayout();
        if ($this->_request->isPost () && $this->getRequest ()->isXmlHttpRequest ()) {
			 $department_id = $this->_getParam("department_id");
			 $employee_id = $this->_getParam("employee_id");
			 $academic_year_id = $this->_getParam("academic_year_id");
			 $course_type = $this->_getParam("course_type");
			 $term_id = $this->_getParam("term_id");
			 $course_id = $this->_getParam("course_id");
			  
			 
				$ElectivesEvaluationComponents_model= new Application_Model_ElectivesEvaluationComponents();
				$result= $ElectivesEvaluationComponents_model->getElectivesAddComponents($academic_year_id,$department_id,$employee_id,$course_type,$term_id,$course_id);
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
	/* public function ajaxGetStudentDetailsAction(){ 
         $this->_helper->layout->disableLayout();
         if ($this->_request->isPost () && $this->getRequest ()->isXmlHttpRequest()) { 
			$academic_year_id = $this->_getParam("academic_year_id");
			$component_name = $this->_getParam("component_name");
			$course_type = $this->_getParam("course_type");
			$this->view->component_name = $component_name;
			$component_id = $this->_getParam("component_id");
			$course_id = $this->_getParam("course_id");
			$term_id = $this->_getParam("term_id");
			$grade_elective_allocate_id = $this->_getParam("grade_elective_allocate_id");
			$ElectivesGradeAllocation_model = new Application_Model_ElectivesGradeAllocation();
			if($grade_elective_allocate_id){
			
					$result = $ElectivesGradeAllocation_model->getStudentRecordsForElectivesAndCourses($grade_elective_allocate_id);
					$this->view->result = $result;
					if($course_type == 1){
						$CoursesEvaluationComponentsItems_model = new Application_Model_CoursesEvaluationComponentsItems();
						$weight = $CoursesEvaluationComponentsItems_model->getWeightage($component_id);
						$this->view->weightage = $weight['weightage'];
						
					}
					elseif($course_type == 2){
						
					$ElectivesEvaluationComponentsItems_model = new Application_Model_ElectivesEvaluationComponentsItems();
						$weight = $ElectivesEvaluationComponentsItems_model->getWeightage($component_id);
						$this->view->weightage = $weight['weightage'];
					}	
				
			}else {
			if($course_type == 1){
			$CoursesEvaluationComponentsItems_model = new Application_Model_CoursesEvaluationComponentsItems();
			$weight = $CoursesEvaluationComponentsItems_model->getWeightage($component_id);
			$this->view->weightage = $weight['weightage'];
			$StudentPortal_model = new Application_Model_StudentPortal();
			
			   if($academic_year_id){
					  $Category_data=$StudentPortal_model->getstudentsdetails($academic_year_id);
					  $this->view->category_data = $Category_data;
			  
						
				
			}
			}
			else if($course_type == 2){
				
				$ElectivesEvaluationComponentsItems_model = new Application_Model_ElectivesEvaluationComponentsItems();
				$weight = $ElectivesEvaluationComponentsItems_model->getWeightage($component_id);
				$this->view->weightage = $weight['weightage'];
				$ElectiveSelection_model = new Application_Model_ElectiveSelection();
				if($academic_year_id){
					$Category_data = $ElectiveSelection_model->getStudentsForElective($academic_year_id,$course_id,$term_id);
					$this->view->category_data = $Category_data;
					
				}
			}
			}			
			
       }
	} */
	public function ajaxGetStudentDetailsAction(){ 
         $this->_helper->layout->disableLayout();
         if ($this->_request->isPost () && $this->getRequest ()->isXmlHttpRequest()) { 
		 $academic_year_id = $this->_getParam("academic_year_id");
		 $term_id = $this->_getParam("term_id");
		 $course_id = $this->_getParam("course_id");
		 $course_type = $this->_getParam("course_type");
		 $this->view->course_type = $course_type;
		 $employee_id = $this->_getParam("employee_id");
		 $department_id = $this->_getParam("department_id");
		  $ElectivesEvaluationComponents_model = new Application_Model_ElectivesEvaluationComponents();
		  $grade_elective_allocate_id = $this->_getParam("grade_elective_allocate_id");
			$ElectivesGradeAllocation_model = new Application_Model_ElectivesGradeAllocation();
			if($grade_elective_allocate_id){
			
					$result = $ElectivesGradeAllocation_model->getStudentRecordsForElectivesAndCourses($grade_elective_allocate_id);
					$this->view->result = $result;
					 if($course_type == '1'){
		
		 $courses_components = $ElectivesEvaluationComponents_model->getAllCoursesComponents($academic_year_id,$department_id,$employee_id,$term_id,$course_id);
		 $this->view->components  = $courses_components;
		 
			
			   
		 }
		 else if($course_type == '2'){
			$electives_components = $ElectivesEvaluationComponents_model->getAllElectivesComponents($academic_year_id,$department_id,$employee_id,$term_id,$course_id);
			  $this->view->components  = $electives_components;
			  
		 }
			}else{
		 if($course_type == '1'){
		
		 $courses_components = $ElectivesEvaluationComponents_model->getAllCoursesComponents($academic_year_id,$department_id,$employee_id,$term_id,$course_id);
		 $this->view->components  = $courses_components;
		 $StudentPortal_model = new Application_Model_StudentPortal();
			
			   if($academic_year_id){
					  $Category_data=$StudentPortal_model->getstudentsdetails($academic_year_id);
					 
					  $this->view->category_data = $Category_data;
			  
						
				
			}
		 }
		 else if($course_type == '2'){
			$electives_components = $ElectivesEvaluationComponents_model->getAllElectivesComponents($academic_year_id,$department_id,$employee_id,$term_id,$course_id);
			  $this->view->components  = $electives_components;
			  $StudentPortal_model = new Application_Model_StudentPortal();
			$ElectiveSelection_model = new Application_Model_ElectiveSelection();
			   if($academic_year_id){
					  $Category_data = $ElectiveSelection_model->getStudentsForElective($academic_year_id,$course_id,$term_id);
					$this->view->category_data = $Category_data;
			  
						
				
			}
		 }
			}		 
			 
			 
		 
		 }
		 
	}
	public function electivesEvaluationComponentsGradeMasterAction(){
		$this->view->action_name = 'Electives Evaluation Components Grade Master';
        $this->view->sub_title_name = 'electives-evaluation-components-grade-master';
        $ElectivesEvaluationComponentsGradeMaster_model = new Application_Model_ElectivesEvaluationComponentsGradeMaster();
		$ElectivesEvaluationComponentsGradeMaster_form = new Application_Form_ElectivesEvaluationComponentsGradeMaster();
			$ElectivesEvaluationComponentsGradeMasterItems_model = new Application_Model_ElectivesEvaluationComponentsGradeMasterItems();
		$elective_component_grade_id = $this->_getParam("id");
		//print_r($feehead_id);die;
        $type = $this->_getParam("type");
		$this->view->type = $type;
        $this->view->form = $ElectivesEvaluationComponentsGradeMaster_form;
		
        switch ($type) {
            case "add":    
                if ($this->getRequest()->isPost()) {
                    if ($ElectivesEvaluationComponentsGradeMaster_form->isValid($this->getRequest()->getPost())) {
                        $data = $ElectivesEvaluationComponentsGradeMaster_form->getValues();
						$data['term_id'] = $this->getRequest()->getPost('term_id');
						$data['course_id'] = $this->getRequest()->getPost('course_id');
						if($data['course_type'] == '1'){
						$data['component_id'] = $this->getRequest()->getPost('component_id');
						$data['elective_component_id'] = 0;
						}else if($data['course_type'] == '2'){
						$data['elective_component_id'] = $this->getRequest()->getPost('component_id');
						$data['component_id'] = 0;
						}
                        $last_insert_id = $ElectivesEvaluationComponentsGradeMaster_model->insert($data);	
						
						 $grade = $this->getRequest()->getPost('grade');
							foreach(array_filter($grade['letter_grade']) as $k => $letter_grade){
								$grade_data['elective_component_grade_id'] = $last_insert_id;
								$grade_data['letter_grade'] = $letter_grade;
								$grade_data['number_grade'] = $grade['number_grade'][$k];
								$ElectivesEvaluationComponentsGradeMasterItems_model->insert($grade_data);
							}	 
					
                        $this->_flashMessenger->addMessage('Details Successfully added');

                        $this->_redirect('electives-allotment/electives-evaluation-components-grade-master');
						 
						}
                    }

                
                break;
            case 'edit': 
                $result = $ElectivesEvaluationComponentsGradeMaster_model->getRecord($elective_component_grade_id);
				$ElectivesEvaluationComponentsGradeMaster_form->populate($result);
				$item_result = $ElectivesEvaluationComponentsGradeMasterItems_model->getRecords($elective_component_grade_id);
				$this->view->item_result = $item_result;
				$this->view->elective_component_grade_id = $elective_component_grade_id;
				$this->view->result = $result;
                if ($this->getRequest()->isPost()) {
                    if ($ElectivesEvaluationComponentsGradeMaster_form->isValid($this->getRequest()->getPost())) {
                        $data = $ElectivesEvaluationComponentsGradeMaster_form->getValues();
						$data['term_id'] = $this->getRequest()->getPost('term_id');
						$data['course_id'] = $this->getRequest()->getPost('course_id');
						if($data['course_type'] == '1'){
						$data['component_id'] = $this->getRequest()->getPost('component_id');
						$data['elective_component_id'] = 0;
						}else if($data['course_type'] == '2'){
						$data['elective_component_id'] = $this->getRequest()->getPost('component_id');
						$data['component_id'] = 0;
						}
                        $ElectivesEvaluationComponentsGradeMaster_model->update($data, array('elective_component_grade_id=?' => $elective_component_grade_id));
						
						  $grade = $this->getRequest()->getPost('grade');
						  $ElectivesEvaluationComponentsGradeMasterItems_model->trashItems($elective_component_grade_id);
							foreach(array_filter($grade['letter_grade']) as $k => $letter_grade){
								$grade_data['elective_component_grade_id'] = $elective_component_grade_id;
								$grade_data['letter_grade'] = $letter_grade;
								$grade_data['number_grade'] = $grade['number_grade'][$k];
								$ElectivesEvaluationComponentsGradeMasterItems_model->insert($grade_data);
							} 
						
                        $this->_flashMessenger->addMessage('Details Updated Successfully');
                        $this->_redirect('electives-allotment/electives-evaluation-components-grade-master');
                    } 
                }
                break;
            case 'delete':
                $data['status'] = 2;
                if ($elective_component_grade_id){
                    $ElectivesEvaluationComponentsGradeMaster_model->update($data, array('elective_component_grade_id=?' => $elective_component_grade_id));
					$this->_flashMessenger->addMessage('Details Deleted Successfully');
				$this->_redirect('electives-allotment/electives-evaluation-components-grade-master');
				}
                break;
            default:
                $messages = $this->_flashMessenger->getMessages();
                $this->view->messages = $messages;
                $result = $ElectivesEvaluationComponentsGradeMaster_model->getRecords();
                $page = $this->_getParam('page', 1);
                $paginator_data = array(
                    'page' => $page,
                    'result' => $result
                );
                $this->view->paginator = $this->_act->pagination($paginator_data);
                break;
        }
	}
	public function electivesGradeAllocationReportAction(){
		$this->view->action_name = 'Electives Grade Allocation Report';
        $this->view->sub_title_name = 'electives-grade-allocation-report';
        $ElectivesGradeAllocationReport_model = new Application_Model_ElectivesGradeAllocationReport();
		$ElectivesGradeAllocationReport_form = new Application_Form_ElectivesGradeAllocationReport();
		$ElectivesGradeAllocationReportItems_model = new Application_Model_ElectivesGradeAllocationReportItems();
		$CoursesGradeAllocationReportItems_model = new Application_Model_CoursesGradeAllocationReportItems();
		$elective_grade_report_id = $this->_getParam("id");
		//print_r($feehead_id);die;
        $type = $this->_getParam("type");
		$this->view->type = $type;
        $this->view->form = $ElectivesGradeAllocationReport_form;
		
        switch ($type) {
            case "add":    
                if ($this->getRequest()->isPost()) {
                    if ($ElectivesGradeAllocationReport_form->isValid($this->getRequest()->getPost())) {
                        $data = $ElectivesGradeAllocationReport_form->getValues();
						$last_insert_id = $ElectivesGradeAllocationReport_model->insert($data);
						$ElectivesFacultyAllotment_model= new Application_Model_ElectivesFacultyAllotment();
						$courses_result= $ElectivesFacultyAllotment_model->getElectiveEmployeeTerms($data['academic_id'],$data['department_id'],$data['employee_id']);
						
				
                        for($i=0;$i<count($courses_result);$i++)
						{
						$course = $_POST['course'];
						
													
						for($k=0;$k<count($_POST['course']['student_id_'.$courses_result[$i]['term_id']]);$k++){
						$course_data['elective_grade_report_id'] = $last_insert_id;
						$course_data['term_id']=$courses_result[$i]['term_id'];
						$course_data['course_id']=$courses_result[$i]['course_id'];
						$course_data['student_id'] = $course['student_id_'.$courses_result[$i]['term_id']][$k];
						$course_data['component_grades'] = $course['grades_'.$courses_result[$i]['term_id']][$k];
						$course_data['component_weightages'] = $course['weightages_'.$courses_result[$i]['term_id']][$k];
						$course_data['component_ids'] = $course['comp_ids_'.$courses_result[$i]['term_id']][$k];
						$course_data['grade_point'] = $course['grade_point_'.$courses_result[$i]['term_id']][$k];
						
						$CoursesGradeAllocationReportItems_model->insert($course_data);
												
						
						}
						}
						$electives_result = $ElectivesFacultyAllotment_model->getElectiveEmployeeElectives($data['academic_id'],$data['department_id'],$data['employee_id']);
						for($j=0;$j<count($electives_result);$j++)
						{
						$elective = $_POST['elective'];
						
													
						for($l=0;$l<count($_POST['elective']['student_id_'.$electives_result[$j]['term_id'].'_'.$electives_result[$j]['elective_id']]);$l++){
						$elective_data['elective_grade_report_id'] = $last_insert_id;
						$elective_data['term_id']=$electives_result[$j]['term_id'];
						$elective_data['elective_id']=$electives_result[$j]['elective_id'];
						$elective_data['student_id'] = $elective['student_id_'.$electives_result[$j]['term_id'].'_'.$electives_result[$j]['elective_id']][$l];
						$elective_data['component_grades'] = $elective['grades_'.$electives_result[$j]['term_id'].'_'.$electives_result[$j]['elective_id']][$l];
						$elective_data['component_weightages'] = $elective['weightages_'.$electives_result[$j]['term_id'].'_'.$electives_result[$j]['elective_id']][$l];
						$elective_data['component_ids'] = $elective['comp_ids_'.$electives_result[$j]['term_id'].'_'.$electives_result[$j]['elective_id']][$l];
						$elective_data['grade_point'] = $elective['grade_point_'.$electives_result[$j]['term_id'].'_'.$electives_result[$j]['elective_id']][$l];
						
					
						$ElectivesGradeAllocationReportItems_model->insert($elective_data);
												
						
						}
						}
					
                        $this->_flashMessenger->addMessage('Details Successfully added');

                        $this->_redirect('electives-allotment/electives-grade-allocation-report');
						 
						}
                    }

                
                break;
            case 'edit': 
                $result = $ElectivesGradeAllocationReport_model->getRecord($elective_grade_id);
				$ElectivesGradeAllocationReport_form->populate($result);
				$this->view->grade_elective_allocate_id = $elective_grade_id;
				$this->view->result = $result;
                if ($this->getRequest()->isPost()) {
                    if ($ElectivesGradeAllocationReport_form->isValid($this->getRequest()->getPost())) {
                        $data = $ElectivesGradeAllocationReport_form->getValues();
						$data['term_id'] = $this->getRequest()->getPost('term_id');
						$data['course_id'] = $this->getRequest()->getPost('course_id');
						$data['component_id'] = $this->getRequest()->getPost('component_id');
                        $ElectivesGradeAllocationReport_model->update($data, array('elective_grade_id=?' => $elective_grade_id));
						
						  $grade = $this->getRequest()->getPost('grade');
						  $ElectivesGradeAllocationReport_model->trashItems($elective_grade_id);
							foreach(array_filter($grade['student_id']) as $k => $student_id){
								$grade_data['elective_grade_id'] = $elective_grade_id;
								$grade_data['student_id'] = $student_id;
								$grade_data['grade_value'] = $grade['grade_value'][$k];
								$ElectivesGradeAllocationReportItems_model->insert($grade_data);
							}
						
                        $this->_flashMessenger->addMessage('Details Updated Successfully');
                         $this->_redirect('electives-allotment/electives-grade-allocation-report');
                    } 
                }
                break;
            case 'delete':
                $data['status'] = 2;
                if ($elective_grade_id){
                    $ElectivesGradeAllocationReport_model->update($data, array('elective_grade_id=?' => $elective_grade_id));
					$this->_flashMessenger->addMessage('Details Deleted Successfully');
					$this->_redirect('electives-allotment/electives-grade-allocation-report');
				}
                break;
            default:
                $messages = $this->_flashMessenger->getMessages();
                $this->view->messages = $messages;
                $result = $ElectivesGradeAllocationReport_model->getRecords();
                $page = $this->_getParam('page', 1);
                $paginator_data = array(
                    'page' => $page,
                    'result' => $result
                );
                $this->view->paginator = $this->_act->pagination($paginator_data);
                break;
        }
	}
	public function ajaxGetElectivesGradeDetailsAction(){
		 $this->_helper->layout->disableLayout();
        if ($this->_request->isPost () && $this->getRequest ()->isXmlHttpRequest ()) {
			 $department_id = $this->_getParam("department_id");
			 $employee_id = $this->_getParam("employee_id");
			 $academic_year_id = $this->_getParam("academic_year_id");
				$ElectivesFacultyAllotment_model= new Application_Model_ElectivesFacultyAllotment();
				$result= $ElectivesFacultyAllotment_model->getElectiveEmployeeTerms($academic_year_id,$department_id,$employee_id);
				$this->view->result = $result;
				
				$electives_result = $ElectivesFacultyAllotment_model->getElectiveEmployeeElectives($academic_year_id,$department_id,$employee_id);
				$this->view->electives_result = $electives_result;
		}
	}
	public function ajaxGetElectivesGradeDetailsViewAction(){
		 $this->_helper->layout->disableLayout();
        if ($this->_request->isPost () && $this->getRequest ()->isXmlHttpRequest ()) {
			 $id = $this->_getParam("id");
			 $ElectivesGradeAllocationReport_model = new Application_Model_ElectivesGradeAllocationReport();
			 $res = $ElectivesGradeAllocationReport_model->getRecord($id);
			 $academic_year_id = $res['academic_id'];
			 $department_id = $res['department_id'];
			 $employee_id = $res['employee_id'];
				$ElectivesFacultyAllotment_model= new Application_Model_ElectivesFacultyAllotment();
				$result= $ElectivesFacultyAllotment_model->getElectiveEmployeeTerms($academic_year_id,$department_id,$employee_id);
				$this->view->result = $result;
				
				$electives_result = $ElectivesFacultyAllotment_model->getElectiveEmployeeElectives($academic_year_id,$department_id,$employee_id);
				$this->view->electives_result = $electives_result;
		}
	}
	
	public function ajaxGetElectivesGradeDetailsDataAction(){
		$this->_helper->layout->disableLayout();
        if ($this->_request->isPost () && $this->getRequest ()->isXmlHttpRequest ()) {
			 $academic_year_id = $this->_getParam("academic_year_id");
			 $department_id = $this->_getParam("department_id");
			 $employee_id = $this->_getParam("employee_id");
			//print_r($academic_id); die;
			 $ElectivesGradeAllocationReport_model= new Application_Model_ElectivesGradeAllocationReport();
			 $result= $ElectivesGradeAllocationReport_model->getEGARCount($academic_year_id,$department_id,$employee_id);
			 $counts = count($result['elective_grade_report_id']);
			 echo json_encode($counts);die;
			 //echo '<pre>'; print_r($counts); die;
			// $this->view->result = $result;
		}
	}
	public function ajaxElectiveEvaluationComponentsViewAction()
	{
		$this->_helper->layout->disableLayout();
		if($this->_request->isPost () && $this->getRequest ()->isXmlHttpRequest ()){
			$evaluation_id = $this->_getParam("evaluation_id");
			$ElectivesEvaluationComponents_model = new Application_Model_ElectivesEvaluationComponents();
			$courses_result = $ElectivesEvaluationComponents_model->getCoursesComponentsView($evaluation_id);
			$this->view->cr_result = $courses_result;
			$electives_result = $ElectivesEvaluationComponents_model->getElectivesComponentsView($evaluation_id);
			$this->view->el_result = $electives_result;
			
		}
	}	

}