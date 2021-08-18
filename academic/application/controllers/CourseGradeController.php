<?php

class CourseGradeController extends Zend_Controller_Action {

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

        $this->view->action_name = 'Course Grade';

        $this->view->sub_title_name = 'course-grade';
        
        $this->accessConfig->setAccess('SA_ACAD_GRADE_REPORT');

        $CourseGrade_model = new Application_Model_CourseGrade();
		
		$CourseGrade_form = new Application_Form_CourseGrade();
		
		$CourseGradeItems_model = new Application_Model_CourseGradeItems();

        $id = $this->_getParam("id");

        $type = $this->_getParam("type");

        switch ($type) {

            case "add":

                $this->view->type = $type;

                $this->view->form = $CourseGrade_form;

                if ($this->getRequest()->isPost()) {

                    if ($CourseGrade_form->isValid($this->getRequest()->getPost())) {

                        $data = $CourseGrade_form->getValues();

                      $last_insert_id = $CourseGrade_model->insert($data);
					  if($data['year_id'] == 1){
					$TermMaster_model= new Application_Model_TermMaster();
					$term_result= $TermMaster_model->getTermRecords($data['academic_id'],$data['year_id']);
					for($i=0;$i<count($term_result);$i++) {   
					$course = $_POST['course'];
							for($k=0;$k<count($_POST['course']['student_id_'.$term_result[$i]['term_id']]);$k++){
									$item_data['id'] = $last_insert_id;
									$item_data['term_id'] = $term_result[$i]['term_id'];
									$item_data['student_id'] = 	$course['student_id_'.$term_result[$i]['term_id']][$k];
									$item_data['academic_courses'] = 	$course['academic_courses_'.$term_result[$i]['term_id']][$k];
									$item_data['academic_credits'] = 	$course['academic_credits_'.$term_result[$i]['term_id']][$k];
									$item_data['academic_grades'] = $course['academic_grades_'.$term_result[$i]['term_id']][$k];
									$item_data['total_grade'] =		$course['total_academic_grade_'.$term_result[$i]['term_id']][$k];
									$CourseGradeItems_model->insert($item_data);
								}
					}
					  }
					  if($data['year_id'] == 2){
						  $TermMaster_model= new Application_Model_TermMaster();
					$term_result= $TermMaster_model->getTermRecords($data['academic_id'],$data['year_id']);
					for($i=0;$i<count($term_result);$i++) {   
					$course = $_POST['course'];
					//echo'<pre>';print_r($course);die;
							for($k=0;$k<count($_POST['course']['student_id_'.$term_result[$i]['term_id']]);$k++){
									$item_data['id'] = $last_insert_id;
									$item_data['term_id'] = $term_result[$i]['term_id'];
									$item_data['student_id'] = 	$course['student_id_'.$term_result[$i]['term_id']][$k];
									$item_data['academic_courses'] = 	$course['academic_courses_'.$term_result[$i]['term_id']][$k];
									$item_data['academic_credits'] = 	$course['academic_credits_'.$term_result[$i]['term_id']][$k];
									$item_data['academic_grades'] = 	$course['academic_grades_'.$term_result[$i]['term_id']][$k];
									$item_data['academic_electives'] = $course['academic_electives_'.$term_result[$i]['term_id']][$k];
									$item_data['elective_values'] = $course['elective_values_'.$term_result[$i]['term_id']][$k];
									$item_data['total_grade'] =		$course['total_academic_grade_'.$term_result[$i]['term_id']][$k];
									//echo'<pre>';print_r($item_data['elective_values']);die;
									$CourseGradeItems_model->insert($item_data);
								}
					}
						  
						  
					  }

                        $this->_flashMessenger->addMessage('Courses Grade Added Successfully ');

                        $this->_redirect('course-grade/index');

                    } 
                }

                break;

         /*    case 'edit':

                $this->view->type = $type;

                $this->view->form = $CourseGrade_form;

                $result = $CourseGrade_model->getRecord($id); 

                $CourseGrade_form->populate($result);

                if ($this->getRequest()->isPost()) {

                    if ($CourseGrade_form->isValid($this->getRequest()->getPost())) {

                        $data = $CourseGrade_form->getValues();

                        $CourseGrade_model->update($data, array('id=?' => $id));

                        $this->_flashMessenger->addMessage('Courses Grade  Updated Successfully');

                        $this->_redirect('course-grade/index');

                    }

                }

                break;

            case 'delete':

                $data['status'] = 2;

                if ($account_id)

                    $Account_model->update($data, array('account_id=?' => $account_id));

                $this->_flashMessenger->addMessage('Account Master Deleted Successfully');

                $this->_redirect('account/master');

                break; */

            default:

                $messages = $this->_flashMessenger->getMessages();

                $this->view->messages = $messages;

                $result = $CourseGrade_model->getRecords();
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
	public function ajaxGetTermsAction(){
		 $this->_helper->layout->disableLayout();
        if ($this->_request->isPost () && $this->getRequest ()->isXmlHttpRequest ()) {
			 $academic_year_id = $this->_getParam("academic_year_id");
			 $term_id = $this->_getParam("term_id");
				$TermMaster_model= new Application_Model_TermMaster();
				$term_result= $TermMaster_model->getTermRecords($academic_year_id,$term_id);
				//print_r($term_result);die;
				$this->view->term_result = $term_result;
		}
	}
	
	public function ajaxGetTermsDataAction(){
		$this->_helper->layout->disableLayout();
        if ($this->_request->isPost () && $this->getRequest ()->isXmlHttpRequest ()) {
			$academic_year_id = $this->_getParam("academic_year_id");
			$term_id = $this->_getParam("term_id");
			//echo $academic_year_id;die;
			$CourseGrade_model = new Application_Model_CourseGrade();
			$grade_result= $CourseGrade_model->getValidTermsRecord($academic_year_id,$term_id);
			$counts = count($grade_result['id']);
			//print_r($counts);die;
			echo json_encode($counts);die;
			$this->view->grade_result = $grade_result;
		}
	}
	
	
	public function ajaxGetSecondYearTermsAction(){
		 $this->_helper->layout->disableLayout();
        if ($this->_request->isPost () && $this->getRequest ()->isXmlHttpRequest ()) {
			 $academic_year_id = $this->_getParam("academic_year_id");
			 $year_id = $this->_getParam("year_id");
				$TermMaster_model= new Application_Model_TermMaster();
				$term_result= $TermMaster_model->getTermRecords($academic_year_id,$year_id);
				$this->view->term_result = $term_result;
		}
	}
	
	
	public function ajaxGetSecondYearTermsDataAction(){
		$this->_helper->layout->disableLayout();
        if ($this->_request->isPost () && $this->getRequest ()->isXmlHttpRequest ()) {
			$academic_year_id = $this->_getParam("academic_year_id");
			$year_id = $this->_getParam("year_id");
			//echo $academic_year_id;die;
			$CourseGrade_model = new Application_Model_CourseGrade();
			$grade_result= $CourseGrade_model->getValidSecondTermsRecord($academic_year_id,$year_id);
			$counts = count($grade_result['id']);
			//print_r($counts);die;
			echo json_encode($counts);die;
			$this->view->grade_result = $grade_result;
		}
	}
        /**
         * This function will display report of Penalties
         */
        public function coursesWisePenaltiesReportAction(){
            $this->view->action_name = 'Courses Wise Penalties Report';

            $this->view->sub_title_name = 'courses-wise-penalties-report';
            
            $Coursewisepenalties_model = new Application_Model_Coursewisepenalties();
		
		$Coursewisepenalties_form = new Application_Form_Coursewisepenalties();

		$CoursewisepenaltiesItems_model = new Application_Model_CoursewisepenaltiesItems();

        $messages = $this->_flashMessenger->getMessages();

                $this->view->messages = $messages;

                $result = $Coursewisepenalties_model->getRecords();
				//print_r($result); die;

                $page = $this->_getParam('page', 1);

                $paginator_data = array(

                    'page' => $page,

                    'result' => $result

                );

                $this->view->paginator = $this->_act->pagination($paginator_data);

        }

	 public function coursesWisePenaltiesAction()
	{             
		 $this->view->action_name = 'Courses Wise Penalties';

        $this->view->sub_title_name = 'courses-wise-penalties';
        
        $this->accessConfig->setAccess('SA_ACAD_PENALTIES_REPORT');

        $Coursewisepenalties_model = new Application_Model_Coursewisepenalties();
		
		$Coursewisepenalties_form = new Application_Form_Coursewisepenalties();

		$CoursewisepenaltiesItems_model = new Application_Model_CoursewisepenaltiesItems();

        $id = $this->_getParam("id");

        $type = $this->_getParam("type");

        switch ($type) {

            case "add":

                $this->view->type = $type;

                $this->view->form = $Coursewisepenalties_form;

                if ($this->getRequest()->isPost()) {

                    if ($Coursewisepenalties_form->isValid($this->getRequest()->getPost())) {

                        $data = $Coursewisepenalties_form->getValues();

                      $last_insert_id = $Coursewisepenalties_model->insert($data);

                    if($data['year_id'] == 1){
					$TermMaster_model= new Application_Model_TermMaster();
					$term_result= $TermMaster_model->getTermRecords($data['academic_id'],$data['year_id']);
					for($i=0;$i<count($term_result);$i++) {   
					$student = $_POST['student'];
			
							for($k=0;$k<count($_POST['student']['student_id_'.$term_result[$i]['term_id']]);$k++){
									$item_data['id'] = $last_insert_id;
									$item_data['term_id'] = $term_result[$i]['term_id'];
									$item_data['student_id'] = 	$student['student_id_'.$term_result[$i]['term_id']][$k];
									$item_data['academic_courses'] = 	$student['academic_courses_'.$term_result[$i]['term_id']][$k];
									$item_data['academic_credits'] = 	$student['academic_credits_'.$term_result[$i]['term_id']][$k];
									
									if(is_array($student['absence_'.$term_result[$i]['term_id'].'_'.$item_data['student_id']])){
									
										$item_data['absence'] = implode(',',$student['absence_'.$term_result[$i]['term_id'].'_'.$item_data['student_id']]);
									 
									 }
	
									$CoursewisepenaltiesItems_model->insert($item_data);
								}
				           	}
                      }
                    if($data['year_id'] == 2){
				    $TermMaster_model= new Application_Model_TermMaster();
					$term_result= $TermMaster_model->getTermRecords($data['academic_id'],$data['year_id']);
					for($i=0;$i<count($term_result);$i++) {   
					$student = $_POST['student'];

							for($k=0;$k<count($_POST['student']['student_id_'.$term_result[$i]['term_id']]);$k++){
									$item_data['id'] = $last_insert_id;
									$item_data['term_id'] = $term_result[$i]['term_id'];
									$item_data['student_id'] = 	$student['student_id_'.$term_result[$i]['term_id']][$k];
									$item_data['academic_courses'] = 	$student['academic_courses_'.$term_result[$i]['term_id']][$k];
									$item_data['academic_credits'] = 	$student['academic_credits_'.$term_result[$i]['term_id']][$k];
									

									if(is_array($student['absence_'.$term_result[$i]['term_id'].'_'.$item_data['student_id']])){
									
										$item_data['absence'] = implode(',',$student['absence_'.$term_result[$i]['term_id'].'_'.$item_data['student_id']]).',';
									
									 }
									if(is_array($student['academic_electives_'.$term_result[$i]['term_id'].'_'.$item_data['student_id']])){
									
										$item_data['academic_electives'] = implode(',',$student['academic_electives_'.$term_result[$i]['term_id'].'_'.$item_data['student_id']]).',';
									 
								    }
									if(is_array($student['academic_electives_ids_'.$term_result[$i]['term_id'].'_'.$item_data['student_id']])){
									
										$item_data['academic_electives_ids'] = implode(',',$student['academic_electives_ids_'.$term_result[$i]['term_id'].'_'.$item_data['student_id']]);
									 
								    }
									
									$CoursewisepenaltiesItems_model->insert($item_data);
							}
					     }  
					  
					  }				  

                        $this->_flashMessenger->addMessage('Penalties Added Successfully ');

                        $this->_redirect('course-grade/courses-wise-penalties');

                    } 
                }

                break;

         case 'edit': 
             $this->view->type = $type;
             $this->view->form = $Coursewisepenalties_form;
             $absense_penalties_id = $this->_getParam("id");
                $Coursewisepenalties_model= new Application_Model_Coursewisepenalties();
                $penalties_report = $Coursewisepenalties_model->getRecord($absense_penalties_id);
                $academic_year_id = $penalties_report['academic_id'];                
			 $year_id = $penalties_report['year_id'];
             if (isset($_POST['edit_form_submitted']) && $_POST['edit_form_submitted']) {
                
                        
                 
                    if($year_id == 1){
					$TermMaster_model= new Application_Model_TermMaster();
					$term_result= $TermMaster_model->getTermRecords($academic_year_id,$year_id);                                        
					for($i=0;$i<count($term_result);$i++) {   
					$student = $_POST['student'];
							for($k=0;$k<count($_POST['student']['student_id_'.$term_result[$i]['term_id']]);$k++){
									
									$item_data['term_id'] = $term_result[$i]['term_id'];
									$item_data['student_id'] = 	$student['student_id_'.$term_result[$i]['term_id']][$k];
									$item_data['academic_courses'] = 	$student['academic_courses_'.$term_result[$i]['term_id']][$k];
									$item_data['academic_credits'] = 	$student['academic_credits_'.$term_result[$i]['term_id']][$k];
									
									if(is_array($student['absence_'.$term_result[$i]['term_id'].'_'.$item_data['student_id']])){
									
										$item_data['absence'] = implode(',',$student['absence_'.$term_result[$i]['term_id'].'_'.$item_data['student_id']]);
									 
									 }
                                                                         
                                                                         $penaliy_id = $_POST['absence_penalty_id_'.$term_result[$i]['term_id']][$k];
                                                                         $where = 'item_id = '.$penaliy_id;
                                                                         
                                                                        
                                                                         if($penaliy_id != ''){
                                                                             $CoursewisepenaltiesItems_model->update($item_data,$where);
                                                                         }
									
								}
				           	}
                      }
                    if($year_id == 2){
				    $TermMaster_model= new Application_Model_TermMaster();
					$term_result= $TermMaster_model->getTermRecords($academic_year_id,$year_id);
					for($i=0;$i<count($term_result);$i++) {   
					$student = $_POST['student'];

							for($k=0;$k<count($_POST['student']['student_id_'.$term_result[$i]['term_id']]);$k++){
									
									$item_data['term_id'] = $term_result[$i]['term_id'];
									$item_data['student_id'] = 	$student['student_id_'.$term_result[$i]['term_id']][$k];
									$item_data['academic_courses'] = 	$student['academic_courses_'.$term_result[$i]['term_id']][$k];
									$item_data['academic_credits'] = 	$student['academic_credits_'.$term_result[$i]['term_id']][$k];
									

									if(is_array($student['absence_'.$term_result[$i]['term_id'].'_'.$item_data['student_id']])){
									
										$item_data['absence'] = implode(',',$student['absence_'.$term_result[$i]['term_id'].'_'.$item_data['student_id']]).',';
									
									 }
									if(is_array($student['academic_electives_'.$term_result[$i]['term_id'].'_'.$item_data['student_id']])){
									
										$item_data['academic_electives'] = implode(',',$student['academic_electives_'.$term_result[$i]['term_id'].'_'.$item_data['student_id']]).',';
									 
								    }
									if(is_array($student['academic_electives_ids_'.$term_result[$i]['term_id'].'_'.$item_data['student_id']])){
									
										$item_data['academic_electives_ids'] = implode(',',$student['academic_electives_ids_'.$term_result[$i]['term_id'].'_'.$item_data['student_id']]);
									 
								    }
                                                                     $penaliy_id = $_POST['absence_penalty_id_'.$term_result[$i]['term_id']][$k];
                                                                         $where = 'item_id = '.$penaliy_id;
                                                                      
									
									$CoursewisepenaltiesItems_model->update($item_data,$where);
							}
					     }  
					  
					  }				  

                        $this->_flashMessenger->addMessage('Penalties Updated Successfully ');

                        $this->_redirect('course-grade/courses-wise-penalties');

                    
                }
                
                         $this->view->academic_id = $academic_year_id;
                $this->view->year_id = $year_id;
				$TermMaster_model= new Application_Model_TermMaster();
				$term_result= $TermMaster_model->getTermRecords($academic_year_id,$year_id);
                                //print_r($term_result);
                                $Academic_model= new Application_Model_Academic();
                                $this->view->academic_batch = $Academic_model->getBatchCodeRecord($academic_year_id);
                               $this->view->term_result = $term_result;
                                
				
                                
                break;

            default:

                $messages = $this->_flashMessenger->getMessages();

                $this->view->messages = $messages;

                $result = $Coursewisepenalties_model->getRecords();
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

	public function ajaxGetStudentAbsenceAction(){
		 $this->_helper->layout->disableLayout();
        if ($this->_request->isPost () && $this->getRequest ()->isXmlHttpRequest ()) {
			 $academic_year_id = $this->_getParam("academic_year_id");
			 $year_id = $this->_getParam("year_id");
				$TermMaster_model= new Application_Model_TermMaster();
				$term_result= $TermMaster_model->getTermRecords($academic_year_id,$year_id);
				$this->view->term_result = $term_result;
		}
	}
	public function ajaxGetStudentAbsenceViewAction(){
		 $this->_helper->layout->disableLayout();
        if ($this->_request->isPost () && $this->getRequest ()->isXmlHttpRequest ()) {
			 $academic_year_id = $this->_getParam("academic_year_id");
			 $term_id = $this->_getParam("term_id");
				$TermMaster_model= new Application_Model_TermMaster();
				$term_result= $TermMaster_model->getTermRecords($academic_year_id,$term_id);
				$this->view->term_result = $term_result;
		}
	}
	
	public function ajaxGetStudentAbsenceDataAction(){
		$this->_helper->layout->disableLayout();
        if ($this->_request->isPost () && $this->getRequest ()->isXmlHttpRequest ()) {
			$academic_year_id = $this->_getParam("academic_year_id");
			$year_id = $this->_getParam("year_id");
			//echo $academic_year_id;die;
			$Coursewisepenalties_model = new Application_Model_Coursewisepenalties();
			$grade_result= $Coursewisepenalties_model->getStudentAbsenceRecord($academic_year_id,$year_id);
			$counts = count($grade_result['id']);
			//print_r($counts);die;
			echo json_encode($counts);die;
			$this->view->grade_result = $grade_result;
		}
	}
	
	public function ajaxGetSecondYearPenaltytermsAction(){
		 $this->_helper->layout->disableLayout();
        if ($this->_request->isPost () && $this->getRequest ()->isXmlHttpRequest ()) {
			 $academic_year_id = $this->_getParam("academic_year_id");
			 $year_id = $this->_getParam("year_id");
				$TermMaster_model= new Application_Model_TermMaster();
				$term_result= $TermMaster_model->getTermRecords($academic_year_id,$year_id);
				$this->view->term_result = $term_result;
		}
	}
	public function ajaxGetSecondYearPenaltytermsViewAction(){
		 $this->_helper->layout->disableLayout();
        if ($this->_request->isPost () && $this->getRequest ()->isXmlHttpRequest ()) {
			 $academic_year_id = $this->_getParam("academic_year_id");
			 $year_id = $this->_getParam("year_id");
				$TermMaster_model= new Application_Model_TermMaster();
				$term_result= $TermMaster_model->getTermRecords($academic_year_id,$year_id);
				$this->view->term_result = $term_result;
		}
	}
	
	public function ajaxGetSecondYearPenaltytermsDataAction(){
		$this->_helper->layout->disableLayout();
        if ($this->_request->isPost () && $this->getRequest ()->isXmlHttpRequest ()) {
			$academic_year_id = $this->_getParam("academic_year_id");
			$year_id = $this->_getParam("year_id");
			//echo $academic_year_id;die;
			$Coursewisepenalties_model = new Application_Model_Coursewisepenalties();
			$grade_result= $Coursewisepenalties_model->getStudentAbsenceRecord($academic_year_id,$year_id);
			$counts = count($grade_result['id']);
			//print_r($counts);die;
			echo json_encode($counts);die;
			$this->view->grade_result = $grade_result;
		}
	}
        public function ajaxGetTermListAfterPenaltiesAction(){
            $academic_id = $this->getRequest()->getPost('academic_id');
            $TermMaster_model= new Application_Model_TermMaster();            
            $term_list = $TermMaster_model->getRecordByAcademicId($academic_id);
            echo '<option value="">Select</option>';
				foreach($term_list as $k => $val){
					
					echo '<option value="'.$val['term_id'].'" >'.$val['term_name'].'</option>';	
					
					
				}
            exit;
        }
	
 public function coursesGradeAfterPenaltiesAction()
	{
		 $this->view->action_name = 'Courses Grade Points After Penalties';

        $this->view->sub_title_name = 'courses-grade-after-penalties';
        $this->accessConfig->setAccess('SA_ACAD_AFTER_PENALTIES');

        $CourseGradeAfterpenalties_model = new Application_Model_CourseGradeAfterpenalties();
		
		$CourseGradeAfterpenalties_form = new Application_Form_CourseGradeAfterpenalties();

		$CourseGradeAfterpenaltiesItems_model = new Application_Model_CourseGradeAfterpenaltiesItems();

        $id = $this->_getParam("id");

        $type = $this->_getParam("type");

        switch ($type) {

            case "add":

                $this->view->type = $type;

                $this->view->form = $CourseGradeAfterpenalties_form;

                if ($this->getRequest()->isPost()) {

                    if ($CourseGradeAfterpenalties_form->isValid($this->getRequest()->getPost())) {
                        $TermMaster_model= new Application_Model_TermMaster();
                        
                        $data = $CourseGradeAfterpenalties_form->getValues();
                        
                        $term_detail = $TermMaster_model->getRecord($data['term_id']);
                        $year_id = $term_detail['year_id'];

                      $last_insert_id = $CourseGradeAfterpenalties_model->insert($data);

                   if($year_id == 1){
					
					$term_result= $TermMaster_model->getTermRecordsByYear($data['academic_id'],$year_id);
					for($i=0;$i<count($term_result);$i++) {   
					$course = $_POST['course'];

							for($k=0;$k<count($_POST['course']['student_id_'.$term_result[$i]['term_id']]);$k++){
									$item_data['id'] = $last_insert_id;
									$item_data['term_id'] = $term_result[$i]['term_id'];
									$item_data['student_id'] = 	$course['student_id_'.$term_result[$i]['term_id']][$k];
									$item_data['academic_courses'] = 	$course['academic_courses_'.$term_result[$i]['term_id']][$k];
									$item_data['academic_credits'] = 	$course['academic_credits_'.$term_result[$i]['term_id']][$k];
									$item_data['academic_grades'] = 	$course['academic_grades_'.$term_result[$i]['term_id']][$k];
									$item_data['final_grade'] = 	$course['final_grade_'.$term_result[$i]['term_id']][$k];
									$item_data['fee_percent'] = 	$course['fee_percent_'.$term_result[$i]['term_id']][$k];
                                                                        $item_data['cgpa'] = 	$course['cgpa_'.$term_result[$i]['term_id']][$k];

									$CourseGradeAfterpenaltiesItems_model->insert($item_data);
								}
					     }
                    }
               		if($year_id == 2){
				    $TermMaster_model= new Application_Model_TermMaster();
					$term_result= $TermMaster_model->getTermRecordsByYear($data['academic_id'],$year_id);
					for($i=0;$i<count($term_result);$i++) {   
					$course = $_POST['course'];
					//echo'<pre>';print_r($course); die;
							for($k=0;$k<count($_POST['course']['student_id_'.$term_result[$i]['term_id']]);$k++){
									$item_data['id'] = $last_insert_id;
									$item_data['term_id'] = $term_result[$i]['term_id'];
									$item_data['student_id'] = 	$course['student_id_'.$term_result[$i]['term_id']][$k];
									$item_data['academic_courses'] = 	$course['academic_courses_'.$term_result[$i]['term_id']][$k];
									$item_data['academic_credits'] = 	$course['academic_credits_'.$term_result[$i]['term_id']][$k];
									$item_data['academic_grades'] = 	$course['academic_grades_'.$term_result[$i]['term_id']][$k];
									$item_data['final_grade'] = 	$course['final_grade_'.$term_result[$i]['term_id']][$k];
									$item_data['fee_percent'] = 	$course['fee_percent_'.$term_result[$i]['term_id']][$k];
                                                                        $item_data['cgpa'] = 	$course['cgpa_'.$term_result[$i]['term_id']][$k];
                                   
								 // echo'<pre>';print_r($item_data);die;
									$CourseGradeAfterpenaltiesItems_model->insert($item_data);
								}
                     }
              }				 
                        $this->_flashMessenger->addMessage('Penalties Added Successfully ');

                        $this->_redirect('course-grade/courses-grade-after-penalties');

                    } 
                }

                break;

         /*    case 'edit':

                $this->view->type = $type;

                $this->view->form = $CourseGrade_form;

                $result = $CourseGrade_model->getRecord($id); 

                $CourseGrade_form->populate($result);

                if ($this->getRequest()->isPost()) {

                    if ($CourseGrade_form->isValid($this->getRequest()->getPost())) {

                        $data = $CourseGrade_form->getValues();

                        $CourseGrade_model->update($data, array('id=?' => $id));

                        $this->_flashMessenger->addMessage('Courses Grade  Updated Successfully');

                        $this->_redirect('course-grade/index');

                    }

                }

                break;

            case 'delete':

                $data['status'] = 2;

                if ($account_id)

                    $Account_model->update($data, array('account_id=?' => $account_id));

                $this->_flashMessenger->addMessage('Account Master Deleted Successfully');

                $this->_redirect('account/master');

                break; */

            default:

                $messages = $this->_flashMessenger->getMessages();

                $this->view->messages = $messages;

                $result = $CourseGradeAfterpenalties_model->getRecords();
				//print_r($result); die;
                //print_r($result);exit;

                $page = $this->_getParam('page', 1);

                $paginator_data = array(

                    'page' => $page,

                    'result' => $result

                );

                $this->view->paginator = $this->_act->pagination($paginator_data);

                break;

        }

	}
public function ajaxGetCoursesGradesAfterPenaltiesAction(){
		 $this->_helper->layout->disableLayout();
        if ($this->_request->isPost () && $this->getRequest ()->isXmlHttpRequest ()) {
            $this->view->academic_year_id = $academic_year_id = $this->_getParam("academic_year_id");
            $this->view->term_id = $term_id = $this->_getParam("term_id");
                         
				$TermMaster_model= new Application_Model_TermMaster();
				$term_result= $TermMaster_model->getTermRecords($academic_year_id,$term_id);
				$this->view->term_result = $term_result;
		}
	}
	
	public function ajaxGetCoursesGradesAfterPenaltiesDataAction(){
		$this->_helper->layout->disableLayout();
        if ($this->_request->isPost () && $this->getRequest ()->isXmlHttpRequest ()) {
			$academic_year_id = $this->_getParam("academic_year_id");
			$term_id = $this->_getParam("term_id");
			//echo $academic_year_id;die;
			$CourseGradeAfterpenalties_model = new Application_Model_CourseGradeAfterpenalties();
			$grade_result= $CourseGradeAfterpenalties_model->getCourseGradeRecord($academic_year_id,$term_id);
			$counts = count($grade_result['id']);
			//print_r($counts);die;
                        if($counts > 0){
                            echo json_encode($counts);die;
                        }
                        //Check if the previous Term/EL component is processed
                        $academic_model = new Application_Model_Academic();
                        $result = $academic_model->getAcademicDesignOrderByDate($academic_year_id);
                        $term_index_pos = NULL;
                        foreach ($result as $key => $row) {//Getting current term/el id
                            if(($row['id'] == $term_id) && ($row['c_type'] == 'term') ){
                                $term_index_pos = $key;
                            }
                        }
                        //Getting previosue Term/EL components id
                        if($term_index_pos > 0){
                            $pre_term_detail = $result[$term_index_pos - 1];
                            if($pre_term_detail['c_type'] == 'el'){//If the previous component was EL
                                $GradeAllocation_model = new Application_Model_ExperientialGradeAllocation();
                                $found = $GradeAllocation_model->isGradeAllocated2($academic_year_id, $pre_term_detail['id']);
                                if (!$found) {
                                    echo 'The previous Experiential Learning component "'.$pre_term_detail['term_name'].'" is not processed yet. Please process it first';
                                }
                            }
                            elseif($pre_term_detail['c_type'] == 'term'){//If the previous component was Term
                                $grade_result= $CourseGradeAfterpenalties_model->getCourseGradeRecord($academic_year_id,$pre_term_detail['id']);
                                $counts = count($grade_result['id']);
                                if($counts == 0){//If the previouse term is already processed
                                    echo 'The previous Term "'.$pre_term_detail['term_name'].'" is not processed yet. Please process it first';
                                }
                                
                            }
                        }
                        
			
			//$this->view->grade_result = $grade_result;
		}
                die;
	}
	
	
	
	
	public function ajaxGetSecondYearGradeaftrtermsAction(){
		 $this->_helper->layout->disableLayout();
        if ($this->_request->isPost () && $this->getRequest ()->isXmlHttpRequest ()) {
			 $academic_year_id = $this->_getParam("academic_year_id");
			 $year_id = $this->_getParam("year_id");
				$TermMaster_model= new Application_Model_TermMaster();
				$term_result= $TermMaster_model->getTermRecords($academic_year_id,$year_id);
				$this->view->term_result = $term_result;
		}
	}
	
	public function ajaxGetSecondYearGradeaftrtermsDataAction(){
		$this->_helper->layout->disableLayout();
        if ($this->_request->isPost () && $this->getRequest ()->isXmlHttpRequest ()) {
			$academic_year_id = $this->_getParam("academic_year_id");
			$year_id = $this->_getParam("year_id");
			//echo $academic_year_id;die;
			$CourseGradeAfterpenalties_model = new Application_Model_CourseGradeAfterpenalties();
			$grade_result= $CourseGradeAfterpenalties_model->getCourseGradeRecord($academic_year_id,$year_id);
			$counts = count($grade_result['id']);
			//print_r($counts);die;
			echo json_encode($counts);die;
			$this->view->grade_result = $grade_result;
		}
	}
public function ajaxGetCourseGradeDetailsViewAction(){
		
		 $CourseGrade_model = new Application_Model_CourseGrade();
		 $CourseGrade_form = new Application_Form_CourseGrade();
		 $CourseGradeItems_model = new Application_Model_CourseGradeItems();
		 $course_grade_id = $this->_getParam("id");
	
		
		 $this->_helper->layout->disableLayout();
        if ($this->_request->isPost () && $this->getRequest ()->isXmlHttpRequest ()) {
			
				
				$result= $CourseGrade_model->getRecord($course_grade_id);
				$this->view->result = $result;
				//print_r($result);
				$items_id = $result['id'];
			// print_r($department_id);die;
			
				$result1= $CourseGradeItems_model->getRecords($items_id);
				
				$this->view->result1 = $result1;
		}
	}
	public function ajaxGetTermsViewAction(){
		 $this->_helper->layout->disableLayout();
        if ($this->_request->isPost () && $this->getRequest ()->isXmlHttpRequest ()) {
			 $academic_year_id = $this->_getParam("academic_year_id");
			 $term_id = $this->_getParam("term_id");
				$TermMaster_model= new Application_Model_TermMaster();
				$term_result= $TermMaster_model->getTermRecords($academic_year_id,$term_id);
				//print_r($term_result);die;
				$this->view->term_result = $term_result;
		}
	}
	public function ajaxGetSecondYearTermsViewAction(){
		 $this->_helper->layout->disableLayout();
        if ($this->_request->isPost () && $this->getRequest ()->isXmlHttpRequest ()) {
			 $academic_year_id = $this->_getParam("academic_year_id");
			 $year_id = $this->_getParam("year_id");
				$TermMaster_model= new Application_Model_TermMaster();
				$term_result= $TermMaster_model->getTermRecords($academic_year_id,$year_id);
				$this->view->term_result = $term_result;
		}
	}
	public function ajaxGetCoursesGradesAfterPenaltiesViewAction(){
		 $this->_helper->layout->disableLayout();
        if ($this->_request->isPost () && $this->getRequest ()->isXmlHttpRequest ()) {
			 $academic_year_id = $this->_getParam("academic_year_id");
			 $term_id = $this->_getParam("term_id");
				$TermMaster_model= new Application_Model_TermMaster();
				$term_result= $TermMaster_model->getTermRecords($academic_year_id,$term_id);
				$this->view->term_result = $term_result;
		}
	}
	public function ajaxGetSecondYearGradeaftrtermsViewAction(){
		 $this->_helper->layout->disableLayout();
        if ($this->_request->isPost () && $this->getRequest ()->isXmlHttpRequest ()) {
			 $academic_year_id = $this->_getParam("academic_year_id");
			 $year_id = $this->_getParam("year_id");
				$TermMaster_model= new Application_Model_TermMaster();
				$term_result= $TermMaster_model->getTermRecords($academic_year_id,$year_id);
				$this->view->term_result = $term_result;
		}
	}
}