<?php

class ToDoController extends Zend_Controller_Action {

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
    private $mainconfig =NULL;
    private $accessConfig =NULL;

    public function init() {

        $zendConfig = new Zend_Config_Ini(
                APPLICATION_PATH . '/configs/application.ini', APPLICATION_ENV);
                require_once APPLICATION_PATH . '/configs/access_level.inc';
                        
                $this->accessConfig = new accessLevel();

       $this->mainconfig=$config = $zendConfig->mainconfig->toArray();

        $this->view->mainconfig = $config;

        $this->_action = $this->getRequest()->getActionName();

        $this->roleConfig = $config_role = $zendConfig->role_administrator->toArray();
        $this->view->administrator_role = $config_role;
        $storage = new Zend_Session_Namespace("admin_login");
        $this->login_storage = $data = $storage->admin_login;
        $this->view->login_storage = $data;
        //print_r($data);exit;
        if (isset($data)) {
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
  if($data->role_id==0)
        $this->_redirect("student-portal/student-dashboard");
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
        $this->view->action_name = 'ToDo';
        $this->view->sub_title_name = 'ToDo';
        $this->accessConfig->setAccess('SA_ACAD_TO_DO_LIST');
        $toDo_model = new Application_Model_ToDo();

        $toDo_form = new Application_Form_ToDo();
        $ec_id = $this->_getParam("id");
        $type = $this->_getParam("type");
        $this->view->type = $type;
        $this->view->form = $toDo_form;
        
        switch ($type) {
            case "add":
                 unset($_SESSION['myVal']);
                //  print_r($_SESSION);exit;
                if ($this->getRequest()->isPost()) {

                    if ($this->getRequest()->getPost()) {
                       
                        $data = $this->getRequest()->getPost();
                        $data['toDo_assigned_by'] = $_SESSION['admin_login']['admin_login']->id;
                        $data['toDo_assigned_by_name'] = $_SESSION['admin_login']['admin_login']->real_name;

                        $last_insert_id = $toDo_model->insert($data);

                        //   print_r($data);exit;
                        $dirPath = APPLICATION_PATH . '/../public/ToDo/' . '/' . $last_insert_id . '/ToDo_Details/';
                        //print_r($dirPath);exit;	
                        if (!file_exists($dirPath)) {
                            mkdir($dirPath, 755, true);
                        }
                        $file_name = $_FILES["file"]["name"];

                        $tem_name = $_FILES["file"]["tmp_name"];

                        if (move_uploaded_file($tem_name[0], $dirPath . $file_name[0])) {
                            //echo "File is valid, and was successfully uploaded"; 
                        } else {
                            // echo "Upload failed";  
                        }
                        $file_data['toDo_files'] = "public/ToDo/" . $last_insert_id . "/ToDo_Details/" . $file_name[0];

                        $toDo_model->update($file_data, array('toDo_id=?' => $last_insert_id));

                        $this->_flashMessenger->addMessage('Evaluation  Components Successfully added');

                        $this->_redirect('To-do/index');
                    }
                }


                break;
            case 'edit':
                $result = $toDo_model->getRecord($ec_id);
                $_SESSION['myVal']['toDo_assigned_to_id'] = $result[0]['toDo_assigned_to_id'];
                $toDo_form->populate($result[0]);
                $this->view->result = $result;
                if ($this->getRequest()->isPost()) {
                    if ($this->getRequest()->getPost()) {
                        $data = $this->getRequest()->getPost();
                        $data['toDo_assigned_by'] = $_SESSION['admin_login']['admin_login']->id;
                         $data['toDo_assigned_by_name'] = $_SESSION['admin_login']['admin_login']->real_name;
                        $dirPath = realpath(APPLICATION_PATH . '/../public/ToDo') . '/' . $ec_id . '/ToDo_Details/';

                        if (!file_exists($dirPath)) {
                            mkdir($dirPath, 755, true);
                        }
                        $file_name = $_FILES["file"]["name"];

                        $tem_name = $_FILES["file"]["tmp_name"];

                        if (move_uploaded_file($tem_name[0], $dirPath . $file_name[0])) {
                            //echo "File is valid, and was successfully uploaded"; 
                        } else {
                            // echo "Upload failed";  
                        }
                        if (!empty($file_name[0])) {
                            $data['toDo_files'] = 'public/ToDo/' . $ec_id . '/ToDo_Details/' . $file_name[0];
                        } 
                        $toDo_model->update($data, array('toDo_id=?' => $ec_id));
                        $this->_flashMessenger->addMessage('Assignment Sharing Successfully updated !');
                    $this->_redirect('To-do/index');
                    }
                    
                }



                break;
          
            case 'comp':
                  $category = array('1' => 'Academic',
                    '2' => 'Admission');
                $priority = array('1' => 'Critical',
                    '2' => 'High',
                    '3' => 'Low');
                $status = array('1' => 'Not Started',
                    '2' => 'In Progress',
                    '3' => 'Completed');

                $employee_model = new Application_Model_HRMModel();



                $messages = $this->_flashMessenger->getMessages();
                $this->view->messages = $messages;
                $result = $toDo_model->getRecords(3);

                $i = 0;
                foreach ($result as $key) {
                    // print_r($category[$cater]);exit;
                    $empl_name = $employee_model->getAllEmployee($key['toDo_assigned_to_id']);
                    $result[$i]['toDo_assigned_to_id'] = $empl_name[0]['name'];
                    $result[$i]['toDo_category'] = $category[$key['toDo_category']];
                    $result[$i]['toDo_priority'] = $priority[$key['toDo_priority']];
                    $result[$i]['toDo_status'] = $status[$key['toDo_status']];
                    $file_arr = explode('/', $key['toDo_files']);
                    $file_name = explode(".", $file_arr[count($file_arr) - 1]);
                    $result[$i]['filename1'] = $file_name[0];
                    $i++;
                }
                // print_r($result);exit;
                $page = $this->_getParam('page', 1);
                $paginator_data = array(
                    'page' => $page,
                    'result' => $result
                );
                $this->view->paginator = $this->_act->pagination($paginator_data);
                
                
                break;
                
                
            case 'in':
                 $category = array('1' => 'Academic',
                    '2' => 'Admission');
                $priority = array('1' => 'Critical',
                    '2' => 'High',
                    '3' => 'Low');
                $status = array('1' => 'Not Started',
                    '2' => 'In Progress',
                    '3' => 'Completed');

                $employee_model = new Application_Model_HRMModel();



                $messages = $this->_flashMessenger->getMessages();
                $this->view->messages = $messages;
                $result = $toDo_model->getRecords(2);

                $i = 0;
                foreach ($result as $key) {
                    // print_r($category[$cater]);exit;
                    $empl_name = $employee_model->getAllEmployee($key['toDo_assigned_to_id']);
                    $result[$i]['toDo_assigned_to_id'] = $empl_name[0]['name'];
                    $result[$i]['toDo_category'] = $category[$key['toDo_category']];
                    $result[$i]['toDo_priority'] = $priority[$key['toDo_priority']];
                    $result[$i]['toDo_status'] = $status[$key['toDo_status']];
                    $file_arr = explode('/', $key['toDo_files']);
                    $file_name = explode(".", $file_arr[count($file_arr) - 1]);
                    $result[$i]['filename1'] = $file_name[0];
                    $i++;
                }
                // print_r($result);exit;
                $page = $this->_getParam('page', 1);
                $paginator_data = array(
                    'page' => $page,
                    'result' => $result
                );
                $this->view->paginator = $this->_act->pagination($paginator_data);
                
                
                break;
            default:
                $category = array('1' => 'Academic',
                    '2' => 'Admission');
                $priority = array('1' => 'Critical',
                    '2' => 'High',
                    '3' => 'Low');
                $status = array('1' => 'Not Started',
                    '2' => 'In Progress',
                    '3' => 'Completed');

                $employee_model = new Application_Model_HRMModel();



                $messages = $this->_flashMessenger->getMessages();
                $this->view->messages = $messages;
                $result = $toDo_model->getRecords(1);

                $i = 0;
                foreach ($result as $key) {
                    // print_r($category[$cater]);exit;
                    $empl_name = $employee_model->getAllEmployee($key['toDo_assigned_to_id']);
                    $result[$i]['toDo_assigned_to_id'] = $empl_name[0]['name'];
                    $result[$i]['toDo_category'] = $category[$key['toDo_category']];
                    $result[$i]['toDo_priority'] = $priority[$key['toDo_priority']];
                    $result[$i]['toDo_status'] = $status[$key['toDo_status']];
                    $file_arr = explode('/', $key['toDo_files']);
                    $file_name = explode(".", $file_arr[count($file_arr) - 1]);
                    $result[$i]['filename1'] = $file_name[0];
                    $i++;
                }
                // print_r($result);exit;
                $page = $this->_getParam('page', 1);
                $paginator_data = array(
                    'page' => $page,
                    'result' => $result
                );
                $this->view->paginator = $this->_act->pagination($paginator_data);
                break;
        }
    }

    public function ajaxGetEmployeeAction() {
        $employee_model = new Application_Model_HRMModel();
        $assigned_to = $this->_getParam('assigned_to');
        $result = $employee_model->getAllEmployee($assigned_to);
        
        echo "<option value='' selected>Select</option>";
        if (count($result) > 0) {
            foreach ($result as $key => $value) {
                echo "<option value='" . $value['empl_id'] . "'>" . $value['name'] . "</option>";
            }die;
        }
        else
        {
            echo '0';die;
        }
    }

    public function ajaxGetNamesAction() {
        $empl_name = array();
        $toDo_model = new Application_Model_ToDo();
        $employee_model = new Application_Model_HRMModel();
        $new_status = $this->_getParam('new_status');
         $check = $this->_getParam('check');
         $result = array();
        $result = $toDo_model->getAllEmployee($new_status);
         if(count($result)==0){
             if($check == 'by_me')
                 $this->assignedTo($result);
             else
                 $this->assignedBy($result);
         }
        //making perfect array if u see the diffrence from b4 and after
        for($k=0; $k<count($result); $k++){
       $result[$k] = $result[$k]['toDo_assigned_to_id'];
        }
       
            $result =  array_unique($result);
        
        $j = 1;
         if($check == 'by_me'){
        for ($i = 0; $i < count($result); $i++) {
            $empl_name[$i] = $employee_model->getAllEmployee($result[$i])[0];  
        }
                echo '<option value="">Select</option>';
           if (count($empl_name) > 0) {
            foreach ($empl_name as $key => $value) {
                echo "<option value='" . $value['empl_id'] . "'>" . $value['name'] . "</option>";
                }die;
            }
          }
           
        
            $empl_name[0] = $employee_model->getAllEmployee($_SESSION['admin_login']['admin_login']->empl_id)[0];  
             foreach ($empl_name as $key => $value) {
                echo "<option value='" . $value['empl_id'] . "'>" . $value['name'] . "</option>";
                }die;
    }
    
    
    
    

    
    public function ajaxGetNamesBymytaskAction(){
        
        
        $empl_name = array();
        $toDo_model = new Application_Model_ToDo();
        $employee_model = new Application_Model_HRMModel();
        $new_status = $this->_getParam('new_status');
        $empl_id =  $this->_getParam('empl_id');
        
        $result = $toDo_model->getAllEmployeeByEmplId($new_status,$empl_id);
       
        //making perfect array if u see the diffrence from b4 and after
        for($k=0; $k<count($result); $k++){
       $result[$k] = $result[$k]['toDo_assigned_to_id'];
        }
       
            $result =  array_unique($result);
            //print_r($result);exit;
          
        $j = 1;
        for ($i = 0; $i < count($result); $i++) {
            
            $empl_name[$i] = $employee_model->getAllEmployee($result[$i])[0];
           
        }
   
        echo '<option value="">Select</option>';
        if (count($empl_name) > 0) {
            foreach ($empl_name as $key => $value) {
                echo "<option value='" . $value['empl_id'] . "'>" . $value['name'] . "</option>";
            }die;
        }
        
        
    }
    
    
    public function ajaxGetNamesBymeAction(){
         $empl_name = array();
        $toDo_model = new Application_Model_ToDo();
        $employee_model = new Application_Model_HRMModel();
        $new_status = $this->_getParam('new_status');
        $id =  $this->_getParam('id');
        $result = $toDo_model->getAllEmployeeById($new_status,$id);
      
        //making perfect array if u see the diffrence from b4 and after
        for($k=0; $k<count($result); $k++){
       $result[$k] = $result[$k]['toDo_assigned_to_id'];
        }
       
            $result =  array_unique($result);
          
        $j = 1;
        for ($i = 0; $i < count($result); $i++) {
            
            $empl_name[$i] = $employee_model->getAllEmployee($result[$i])[0];
           
        }
    
        echo '<option value="">Select</option>';
        if (count($empl_name) > 0) {
            foreach ($empl_name as $key => $value) {
                echo "<option value='" . $value['empl_id'] . "'>" . $value['name'] . "</option>";
            }die;
        }
        
    }
    
    
    public function ajaxGetListAction() {
        $toDo_model = new Application_Model_ToDo();
        $employee_model = new Application_Model_HRMModel();
        $data = array();
        //all  post variable 
        $from_date = strtr($this->_getParam('from_date'), "/", "-");
        $to_date = strtr($this->_getParam('to_date'), "/", "-");
        $empl_name = $this->_getParam('empl_name');
        $new_status = $this->_getParam('new_status');
        $check = $this->_getParam('check');
        $category = $this->_getParam('category');
        
        $data = array(
            'from_date' => date('Y-m-d', strtotime($from_date)),
            'to_date' => date('Y-m-d', strtotime($to_date)),
            'todo_status' => $new_status,
            'btn' => $check
        );
        //print_r($data['from_date']);exit;
        //all variable sent to gerRecordsByEverything
        $result = $toDo_model->gerRecordsByEverything($data, $empl_name, $category);
       // print_r($result);exit;
        //$print_r($check);exit;
        if($check == 'by_me')
            $this->assignedTo($result);
        $this->assignedBy($result);

    }
    
    
    
    public function ajaxGetTaskAction(){
         $toDo_model = new Application_Model_ToDo();
        $employee_model = new Application_Model_HRMModel();
        $task = $this->_getParam('mytask');
        $status = $this->_getParam('status');
        $empl_id = $this->_getParam('empl_id');
       $check = $this->_getParam('check');
       $category = $this->_getParam('category');
          $from_date = strtr($this->_getParam('from_date'), "/", "-");
        $to_date = strtr($this->_getParam('to_date'), "/", "-");
         $from_date1 = date('Y-m-d', strtotime($from_date));
          $to_date1 = date('Y-m-d', strtotime($to_date));
        $result = $toDo_model->getTask($task,$from_date1,$to_date1, $status,$empl_id, $category);

        if($check == 'by_me')
            $this->assignedTo($result);
        $this->assignedBy($result);
    }
    
    
    
    
    public function ajaxGetMytaskAction(){
         $toDo_model = new Application_Model_ToDo();
        $employee_model = new Application_Model_HRMModel();
        $task = $this->_getParam('mytask');
        $empl_id = $this->_getParam('empl_id');
        $category = $this->_getParam('category');
       $from_date = strtr($this->_getParam('from_date'), "/", "-");
        $to_date = strtr($this->_getParam('to_date'), "/", "-");
         $from_date1 = date('Y-m-d', strtotime($from_date));
          $to_date1 = date('Y-m-d', strtotime($to_date));
         $status = $this->_getParam('status');
            $check = $this->_getParam('check');
        
          
          
        $result = $toDo_model->getMyTask($task,$from_date1,$to_date1,$status, $empl_id, $category);
          
          //$result = $toDo_model->gerRecordsByEverything($data, $empl_name);
if($check == 'by_me')
            $this->assignedTo($result);
        $this->assignedBy($result);
        
       
    }
    
    
    
    
    public function assignedBy(array $result)
    { $toDo_model = new Application_Model_ToDo();
        $employee_model = new Application_Model_HRMModel();
     //  print_r($result);exit;
        
        $category = array('1' => 'Academic',
            '2' => 'Admission');
        $priority = array('1' => 'Critical',
            '2' => 'High',
            '3' => 'Low');
        $status = array('1' => 'Not Started',
            '2' => 'In Progress',
            '3' => 'Completed');


        $i = 0;
        foreach ($result as $key) {
            // print_r($category[$cater]);exit;
            $empl_name = $employee_model->getAllEmployee($key['toDo_assigned_to_id']);
            $result[$i]['toDo_assigned_to_id'] = $empl_name[0]['name'];
            $result[$i]['toDo_category'] = $category[$key['toDo_category']];
            $result[$i]['toDo_priority'] = $priority[$key['toDo_priority']];
            $result[$i]['toDo_status'] = $status[$key['toDo_status']];
            $file_arr = explode('/', $key['toDo_files']);
            $file_name = explode(".", $file_arr[count($file_arr) - 1]);
            $result[$i]['filename1'] = $file_name[0];
            $i++;
        }
        $table_body='';
        //print_r($result);exit;
        $table_body.= '<thead>
					<tr>
						<th style="text-align:center;">S. No.</th>	
						<th style="text-align:center;">Category</th>
						<th style="text-align:center;">Task/Activity</th>
						<th style="text-align:center;">Assigned by</th>
                                                <th style="text-align:center">Assigned Date</th>
                                                <th style="text-align:center">Due Date</th>
                                                <th style="text-align:center">Priority</th>
                                                 <th style="text-align:center">Status</th>
                                                 <th style="text-align:center">Download Files</th>
                                                 <th style="text-align:center">Cancel/Delete</th>
					</tr>
				</thead>
				<tbody id="main_table" style="text-align:center;">';
        if(count($result)==0){
        $table_body .='<tr><td colspan="10" class="text-center">No Record Found</td></tr>';
        }
        
$j=0;

        foreach ($result as $results) {
           //print_r($results['toDo_category']);exit;
           $table_body.='<tr>';
              $table_body.='<td>'. ++$j.'</td>';				
              $table_body.='<td>'. $results['toDo_category'] .'</td>';
                $table_body.= '<td>'. $results['toDo_task_description'].'</td>';
               $table_body.= '<td>'.  $results['toDo_assigned_by_name'] .'</td>';
             $table_body.= '<td>'. $results['toDo_activity_date'] .'</td>';
             $table_body.= '<td>'. $results['toDo_due_date'].' </td>';
               $table_body.= '<td>'. $results['toDo_priority'].'</td>';
                $table_body.= '<td>'. $results['toDo_status'].'</td>';
              $table_body.= "<td><a href='".$this->mainconfig['host'].$results['toDo_files']."' class='link' download>". $results['filename1']."</a></td>";
               $table_body.= '<td><a href="'.$this->mainconfig['host'].'ToDo/index/type/edit/id/'.$results['toDo_id'].'" class="edit" title="Edit">View / Edit</a>&nbsp'; 

           $table_body.= '</tr>';            

        }
        $table_body.='</tbody>';
        echo $table_body; 
        die;
        
    }
    
    
    public function assignedTo(array $result){
       //print_r($result);exit;
         $toDo_model = new Application_Model_ToDo();
        $employee_model = new Application_Model_HRMModel();     
           
        $category = array('1' => 'Academic',
            '2' => 'Admission');
        $priority = array('1' => 'Critical',
            '2' => 'High',
            '3' => 'Low');
        $status = array('1' => 'Not Started',
            '2' => 'In Progress',
            '3' => 'Completed');

 
        $i = 0;
        foreach ($result as $key) {
            // print_r($category[$cater]);exit;
            $empl_name = $employee_model->getAllEmployee($key['toDo_assigned_to_id']);
            $result[$i]['toDo_assigned_to_id'] = $empl_name[0]['name'];
            $result[$i]['toDo_category'] = $category[$key['toDo_category']];
            $result[$i]['toDo_priority'] = $priority[$key['toDo_priority']];
            $result[$i]['toDo_status'] = $status[$key['toDo_status']];
            $file_arr = explode('/', $key['toDo_files']);
            $file_name = explode(".", $file_arr[count($file_arr) - 1]);
            $result[$i]['filename1'] = $file_name[0];
            $i++;
        }
       
        $table_body='';
       
          $table_body .= '<thead>
					<tr>
						<th style="text-align:center;">S. No.</th>	
						<th style="text-align:center;">Category</th>
						<th style="text-align:center;">Task/Activity</th>
						<th style="text-align:center;">Assigned to</th>
                                                <th style="text-align:center">Assigned Date</th>
                                                <th style="text-align:center">Due Date</th>
                                                <th style="text-align:center">Priority</th>
                                                 <th style="text-align:center">Status</th>
                                                 <th style="text-align:center">Download Files</th>
                                                 <th style="text-align:center">Cancel/Delete</th>
					</tr>
				</thead>
				<tbody id="main_table" style="text-align:center;">';
        if(count($result)==0){
        $table_body .='<tr><td colspan="10" class="text-center">No Record Found</td></tr>';
        }
        
$j=0;

        foreach ($result as $results) {
           //print_r($results['toDo_category']);exit;
           $table_body.='<tr>';
              $table_body.='<td>'. ++$j.'</td>';

            
          
              					
              $table_body.='<td>'. $results['toDo_category'] .'</td>';
                $table_body.= '<td>'. $results['toDo_task_description'].'</td>';
               $table_body.= '<td>'.  $results['toDo_assigned_to_id'] .'</td>';
             $table_body.= '<td>'. $results['toDo_activity_date'] .'</td>';
             $table_body.= '<td>'. $results['toDo_due_date'].' </td>';
               $table_body.= '<td>'. $results['toDo_priority'].'</td>';
                $table_body.= '<td>'. $results['toDo_status'].'</td>';
              $table_body.= "<td><a href='".$this->mainconfig['host'].$results['toDo_files']."' class='link' download>". $results['filename1']."</a></td>";
               $table_body.= '<td><a href="'.$this->mainconfig['host'].'ToDo/index/type/edit/id/'.$results['toDo_id'].'" class="edit" title="Edit">View / Edit</a>&nbsp'; 

           $table_body.= '</tr>';            

        }
        $table_body.='</tbody>';
        echo $table_body; 
        die;
        
        
    }
}
