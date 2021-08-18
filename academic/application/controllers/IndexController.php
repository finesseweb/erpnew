<?php

class IndexController extends Zend_Controller_Action {

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
    private $academic_id = NULL;
    private $term_id = NULL;
    private $start_date = NULL;
    private $end_date = NULL;
    private $_base_url = NULL;
    private $accessConfig =NULL;

    public function init() {
        
        $zendConfig = new Zend_Config_Ini(APPLICATION_PATH . '/configs/application.ini', APPLICATION_ENV);
        
        require_once APPLICATION_PATH . '/configs/access_level.inc';
                        
        $accessConfig = new accessLevel();

        $config = $zendConfig->mainconfig->toArray();
        $this->_base_url = $config['erp'];

        $this->view->mainconfig = $config;

        $config_role = $zendConfig->role_administrator->toArray();

        $this->view->administrator_role = $config_role;
        $storage = new Zend_Session_Namespace("admin_login");
        $data = $storage->admin_login;
        $this->view->login_storage = $data;
        // print_r($data);exit;

        if (isset($data)) {
            $this->view->role_id = $data->role_id;
            $this->view->login_empl_id = $data->empl_id;
        }


        $this->_action = $this->getRequest()->getActionName();

        
        if ($this->_action == "login" || $this->_action == "forgot-password") 
        {
        $this->_helper->layout->setLayout("layout");
        } 
        else 
        {
        $this->_helper->layout->setLayout("layout");
        }



        $adminaction_model = new Application_Model_Adminactions();

        $this->_act = new Application_Model_Adminactions();

        $this->_flashMessenger = $this->_helper->FlashMessenger;

        $this->authonticate();

        // $this->_db = Zend_Db_Table::getDefaultAdapter();
        if ($this->_action != 'forgot-password') {
            $this->view->authontication = $this->_authontication;
        }

        // echo date(DATE_FORMATE);
        //echo DATE_FORMATE;exit;
    }

    protected function authonticate() {

        $storage = new Zend_Session_Namespace("admin_login");

        $data = $storage->admin_login;

//echo '<pre>'; print_r($this->_action ); die;

if($data->role_id == 0  && $this->_action != 'logout' &&  $this->_action != 'login')
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
        $toDo_form = new Application_Form_Index();
        $this->view->type = $type;
        $this->view->form = $toDo_form;
        $zendConfig = new Zend_Config_Ini(APPLICATION_PATH . '/configs/application.ini', APPLICATION_ENV);
        $config = $zendConfig->mainconfig->toArray();
 
        $this->view->mainconfig = $config;
        // action body 
        //Purchase 
        $ErpIndex_model = new Application_Model_ErpIndex();
        /* $this->view->PurchaseProformaCount = $ErpIndex_model->PurchaseProformaCount();
          $this->view->PurchaseCommercialCount = $ErpIndex_model->PurchaseCommercialCount();
          $this->view->PurchasePackingCount = $ErpIndex_model->PurchasePackingCount();
          $this->view->PurchaseOrderCount = $ErpIndex_model->PurchaseOrderCount();
          $this->view->PurchaseInvoiceCount = $ErpIndex_model->PurchaseInvoiceCount();
          //Sales
          $this->view->SalesProformaCount = $ErpIndex_model->SalesProformaCount();
          $this->view->SalesCommercialCount = $ErpIndex_model->SalesCommercialCount();
          $this->view->SalesPackingCount = $ErpIndex_model->SalesPackingCount();
          $this->view->SalesEnquiryCount = $ErpIndex_model->SalesEnquiryCount();
          $this->view->SalesQuotationCount = $ErpIndex_model->SalesQuotationCount();
          $this->view->SalesOrderCount = $ErpIndex_model->SalesOrderCount();
          $this->view->SalesInvoiceCount = $ErpIndex_model->SalesInvoiceCount(); */
        // Grn	
        //$Sales_order_model = new Application_Model_ErpSalesTyreOrderForm();
        //plant
        //$this->view->PlantMaintenance = $ErpIndex_model->PlantMaintenance();
    }

    public function logoutAction() {

        $storage = new Zend_Session_Namespace("admin_login");

        $storage->unsetAll();
        setcookie("admin_login_status", "", time() - (3600 * 24), "/");
        /* $session = new Zend_Session_Namespace("admin_pemissions");

          $session->unsetAll(); */

        $this->_redirect('index/login');
    }

    public function loginAction() {
       
     $this->setErpUser();
        $this->_helper->layout->setLayout("loginlayout");
      //  $erp_val = json_decode($this->_getParam('erp_session_val'),true);
       
        if ($this->_authontication) {
            $this->_redirect('index/'); //Redirected on Report page, Date - 15-Dec-2017
        }

        $users = new Application_Model_ErpAdmin();

        //$form = new Application_Form_Login();
        //$this->view->form = $form;

        if ($this->getRequest()->isPost()) {
            //if ($form->isValid($_POST)) {


            $auth = Zend_Auth::getInstance();
            $data = $this->getRequest()->getPost();
            
           // unset($data[])
            if(count($data)==1)
              $this->forgotPassword($data);  
                
            $HRMModel_model = new Application_Model_HRMModel();
            $user_detail = $HRMModel_model->getUserDetail($data['admin_user_name']);
            
            if (is_array($user_detail) && !empty($user_detail)) {
                $stored_password = $user_detail['password'];
          //    print_r(md5($data['password']));exit;
                if (md5($data['password']) === $stored_password) {
                  
                    $auth->clearIdentity();
                    $storage = new Zend_Session_Namespace("admin_login");
                    $user_detail['role_set'] = explode(';',$user_detail['areas']);
                    unset($user_detail['sections']);
                    unset($user_detail['areas']);
                    $storage->admin_login = (object) $user_detail;
                    $storage->unique_id = uniqid() . rand() . time(date("Y-m-d H:i:s"));
                    setcookie('admin_login_status', '1', time() + (3600 * 24), '/');
                    // $adminroles = new Application_Model_Adminroles();
                    //$stored = $storage->admin_login;
                    $this->_redirect('index/'); //Redirected on Report page, Date - 15-Dec-2017
                } else {
                    $auth_attempt = FALSE;
                    //$this->view->errorMessage = "Invalid username or password. Please try again.";
                }
            } else {
                $auth_attempt = FALSE;
                //$this->view->errorMessage = "Invalid username or password. Please try again.";
            }
            // die;
           if (!$auth_attempt) {


                $auth = Zend_Auth::getInstance();
                $authAdapter = new Zend_Auth_Adapter_DbTable($users->getAdapter(), 'participants_login');

                $authAdapter->setIdentityColumn('participant_username')->setCredentialColumn('participant_pword');
                $authAdapter->setIdentity($data['admin_user_name'])->setCredential($data['password']);
                $authAdapter->getDbSelect()->where('participant_Active=0');


                $result = $auth->authenticate($authAdapter);
            //   echo "<pre>"; print_r($result);echo "</pre>";exit;
                if ($result->isValid()) {
//echo "asdfa";die;

                    $auth->clearIdentity();
                    $storage = new Zend_Session_Namespace("admin_login");
                    $storage->admin_login = $authAdapter->getResultRowObject();
                    $storage->unique_id = uniqid() . rand() . time(date("Y-m-d H:i:s"));
                    setcookie('admin_login_status', '1', time() + (3600 * 24), '/');
                    // $adminroles = new Application_Model_Adminroles();

                    $stored = $storage->admin_login;

                    $this->_redirect('student-portal/student-dashboard'); //Redirected on Report page, Date - 15-Dec-2017
                } else {
                    $_SESSION['flash_message_error']= "Invalid username or password. Please try again.";
                }
            } else {
                $_SESSION['flash_message_error'] = "Invalid username or password. Please try again.";
            }
           

             }
        
    }

    public function forgotPassword($data){
         $HRMModel_model = new Application_Model_HRMModel();
            $user_detail = $HRMModel_model->getUserDetail1($data['f_username']);
           // print_r($user_detail);die;
            if (is_array($user_detail) && !empty($user_detail)){
                 $_SESSION['flash_message']='Password is sent to your email id';
               $this->sendMail($user_detail);
            } else {
                $auth_attempt = FALSE;
                //$this->view->errorMessage = "Invalid username or password. Please try again.";
            }
               if (!$auth_attempt) {
                   $participant = new Application_Model_ParticipantsLogin();
            $user_detail = $participant->getInfo($data['f_username']);
            
                if (is_array($user_detail) && !empty($user_detail)){
                    $_SESSION['flash_message']='Password is sent to your email id';
               $this->sendMail($user_detail);
            } else {
                $_SESSION['flash_message'] = 'Invalid Email Id';
                $this->_redirect('index/login');
                //$this->view->errorMessage = "Invalid username or password. Please try again.";
            }
            }
            
            
            
    }
    
    
    public function sendMail($user_detail){
       if ($user_detail['password']!=''){
           $pword = $user_detail['password'];
            $name = $user_detail['real_name'];
             $id = $user_detail['user_id'];   
       }
       else {
       $pword = $user_detail['participant_pword'];
       $name = $user_detail['participant_username'];
       $id = $user_detail['user_id'];  
       }
            $to = "ash0a130@gmail.com";
$subject = "Forgot password";

$message = "
<html>
<head>
<title>Forgot PAssword</title>
<style>
table>thead>th,table>thead>td{
padding:5px;
}
</style>
</head>
<body>
<p style='color:red; text-shadow: 1px 1px 3px #000;'>This email contains your forgotten password!</p>
<table border=1>
<tr>
<th>User Id</th>
<th>Name</th>
<th>Password</th>
</tr>
<tr>
<td>".$id."</td>
<td>".$name."</td>
<td>".$pword."</td>  
</tr>
</table>
</body>
</html>
";

// Always set content-type when sending HTML email
$headers = "MIME-Version: 1.0" . "\r\n";
$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";

// More headers
$headers .= 'From: <no-reply@dmi.ac.in>' . "\r\n";
mail($to,$subject,$message,$headers);
$this->_redirect('index/login');
    }

    public function maintainanceDashboardAction() {

        $zendConfig = new Zend_Config_Ini(APPLICATION_PATH . '/configs/application.ini', APPLICATION_ENV);
        $config = $zendConfig->mainconfig->toArray();
        $this->view->mainconfig = $config;
        // action body 
        //Purchase 
        $ErpIndex_model = new Application_Model_ErpIndex();
        /* $this->view->PurchaseProformaCount = $ErpIndex_model->PurchaseProformaCount();
          $this->view->PurchaseCommercialCount = $ErpIndex_model->PurchaseCommercialCount();
          $this->view->PurchasePackingCount = $ErpIndex_model->PurchasePackingCount();
          $this->view->PurchaseOrderCount = $ErpIndex_model->PurchaseOrderCount();
          $this->view->PurchaseInvoiceCount = $ErpIndex_model->PurchaseInvoiceCount();
          //Sales
          $this->view->SalesProformaCount = $ErpIndex_model->SalesProformaCount();
          $this->view->SalesCommercialCount = $ErpIndex_model->SalesCommercialCount();
          $this->view->SalesPackingCount = $ErpIndex_model->SalesPackingCount();
          $this->view->SalesEnquiryCount = $ErpIndex_model->SalesEnquiryCount();
          $this->view->SalesQuotationCount = $ErpIndex_model->SalesQuotationCount();
          $this->view->SalesOrderCount = $ErpIndex_model->SalesOrderCount();
          $this->view->SalesInvoiceCount = $ErpIndex_model->SalesInvoiceCount(); */
        // Grn	
        //$Sales_order_model = new Application_Model_ErpSalesTyreOrderForm();
        //plant
        $this->view->PlantMaintenance = $ErpIndex_model->PlantMaintenance();
    }

    public function electivesDashboardAction() {
        $zendConfig = new Zend_Config_Ini(APPLICATION_PATH . '/configs/application.ini', APPLICATION_ENV);
        $config = $zendConfig->mainconfig->toArray();
        $this->view->mainconfig = $config;
        //$ErpIndex_model = new Application_Model_ErpIndex();
        $ElectiveSelection_model = new Application_Model_ElectiveSelection();
        $electives = $ElectiveSelection_model->getElectivesDashboard();
        //print_r(count($electives));die;
        $this->view->electives = $electives;
        //$this->view->purchase_quotation = $ErpIndex_model->PurchaseQuotationCount();
        //$this->view->PurchaseOrderCount = $ErpIndex_model->PurchaseOrderCount();
        //$this->view->PurchaseInvoiceCount = $ErpIndex_model->PurchaseInvoiceCount();
    }

    public function financeDashboardAction() {
        $zendConfig = new Zend_Config_Ini(APPLICATION_PATH . '/configs/application.ini', APPLICATION_ENV);
        $config = $zendConfig->mainconfig->toArray();
        $this->view->mainconfig = $config;

        $ErpIndex_model = new Application_Model_ErpIndex();
        $this->view->payments = $ErpIndex_model->paymentAmount();
        $this->view->deposits = $ErpIndex_model->depositAmount();
    }

    public function inventoryDashboardAction() {
        $zendConfig = new Zend_Config_Ini(APPLICATION_PATH . '/configs/application.ini', APPLICATION_ENV);
        $config = $zendConfig->mainconfig->toArray();
        $this->view->mainconfig = $config;

        $erp_items_master = new Application_Model_ErpItemsMaster();

        $erp_items_category_master = new Application_Model_ErpItemsCategoryMaster();

        $category = $erp_items_category_master->getCateId();

        $result = $erp_items_master->getRecords();

        $page = $this->_getParam('page', 1);

        $paginator_data = array(
            'page' => $page,
            'result' => $result
        );

        $categ_data = array(
            'page' => $page,
            'result' => $result
        );

        $this->view->paginator = $this->_act->pagination($paginator_data);

        $this->view->catdata = $this->_act->pagination($categ_data);
    }

    public function hrmDashboardAction() {
        $zendConfig = new Zend_Config_Ini(APPLICATION_PATH . '/configs/application.ini', APPLICATION_ENV);
        $config = $zendConfig->mainconfig->toArray();
        $this->view->mainconfig = $config;
        $ErpIndex_model = new Application_Model_ErpIndex();
        $this->view->professors_count = $ErpIndex_model->Professorcount();
        $this->view->assisprofessors_count = $ErpIndex_model->AssistantProfessorcount();
        $this->view->drivercount = $ErpIndex_model->Drivercount();
        $this->view->officestaffcount = $ErpIndex_model->OfficeStaffcount();
        $this->view->labourcount = $ErpIndex_model->Labourcount();
    }
    
   public function getViewUrlAction(){
       $this->_helper->layout->disableLayout();
        $start_date = $this->_getParam('start_date');
        $batch = $this->_getParam('batch_id');
        $course_details = new Application_Model_Attendance();
        $max_version = $course_details->getMaxVersionOnDate($start_date, $batch);
        $id = $course_details->getId($start_date,$max_version);
        if(!empty($id['batch_schedule_id']) &&  !empty($max_version)){
      echo $this->_base_url."academic/batch-schedule/index/type/edit/id/".$id['batch_schedule_id']."/version/$max_version";die;
        }else{
      echo '#';die;
        }
   }
    
   public function setErpUser(){
     
   if($_POST['user']){
        
            $_SESSION['admin_login']['admin_login']->id = $this->_getParam('user');
           $_SESSION['admin_login']['admin_login']->user_id = $this->_getParam('username');
             $_SESSION['admin_login']['admin_login']->real_name = !empty($this->_getParam('empl_name'))?$this->_getParam('empl_name'):$this->_getParam('name');
            $_SESSION['admin_login']['admin_login']->role_id = $this->_getParam('access');
            $_SESSION['admin_login']['admin_login']->email = $this->_getParam('email');
           $_SESSION['admin_login']['admin_login']->empl_id = $this->_getParam('empl_id');
         $_SESSION['admin_login']['admin_login']->last_visit_date = date('Y-m-d h:i:s A', $this->_getParam('last_act'));
       $_SESSION['admin_login']['admin_login']->role_set = $this->_getParam('roles');
         $this->_authontication = $_SESSION['admin_login']['admin_login'];
       }
   }
  /* public function forgotPasswordAction() {
        $this->_helper->layout->setLayout("loginlayout");
        $users = new Application_Model_ErpAdmin();
        $form = new Application_Form_ForgotPassword();
        $this->view->form = $form;
        if ($this->getRequest()->isPost()) {
            if ($form->isValid($_POST)) {
                $data = $form->getValues();
                if ($result->isValid()) {
                    //echo ''; die;
                    $this->_redirect('index');
                } else {
                    $this->view->errorMessage = "Invalid email. Please try again.";
                }
            }
        }
    }
   */
   
   
    
    public function getCourseAction($start_date = ''){
            $start_date = $this->_getParam('start_date');
            $term_id = $this->_getParam('term_id');
             if($term_id==''){
                 echo "<table class='table table-striped table-bordered mb30 jambo_table bulk_action' id='dataTable' style='width:100%;'>";

        echo "<thead>";
        //====[HEADING]======//
        echo "<tr>";
        echo "<th>Class I</th>";
        echo "<th> Class II</th>";
        echo "<th>Class III</th>";
        echo "<th>Class IV</th>";
        echo "<th>Class V</th>";
        echo "</tr>";
        echo "</thead>";
        //=====[TABLE BODY]======///       
        echo "<tbody>";
          //========[TABLE DATE]===//
          echo "<tr>";
          echo "<th class='text-center' colspan='6'>No Record Found</th>";
          echo "</tr>";
          echo "</tbody>";
          echo "</table>";die;
            }
            
            
            
             $course_details = new Application_Model_Attendance();
             $terms = new Application_Model_BatchSchedule();
             $section_master = new Application_Model_Section();
             $sections = $section_master->getSectionId($term_id);
           //  echo $start_date.' '.$term_id; exit;
             $allBatch = $terms->getAllBatch($start_date,$term_id);
             foreach($allBatch as $batch){
                foreach($sections as $key => $value){
                $max_version[$batch['batch']][] = $course_details->getMaxVersionOnDate($start_date,$batch['batch'],$value['id']);
             }
             }
            
            foreach($max_version as $key => $value){
                $result[$key] = $course_details->getCourseDetails($term_id, $key);
            }
            foreach($result as $Items => $val){
              foreach($val as $Item => $value){
                        $course_id[$Items][$Item] = $value['course_id'];
                    }
                }
            $all_result = $this->getDateAction($course_id, $start_date, $max_version, $term_id,$sections);
    }
    
    

    
    

    public function getDateAction($courses, $start_date, $max_version,$term_id,$sections) {
        
       // print_r($max_version);exit;
         $date_details = new Application_Model_Attendance();
         $term = new Application_Model_TermMaster();
         $classMaster = new Application_Model_ClassMaster();
         $section = new Application_Model_Section();
         
         $terms_academic_names = array();
       // $version_id = $term->getMaxVersion($start_date);
         
        $no_of_classes = 0;
      
            foreach($courses as $batch_id => $course_val){
                $no_of_classes = $classMaster->getRecordByTermIdAndBatch($term_id, $batch_id);
                $new_arr = array_filter($max_version[$batch_id], function($value){ return $value != ''; });
                $result[$batch_id] = $date_details->getAllDateDetails($term_id, $batch_id, $course_val, $start_date, $new_arr, $no_of_classes,$sections);  
            }
     
       
        foreach($max_version as $batch_id =>$max_value){ 
          $terms_academic_names[$batch_id] = $term->getTermOnDat1($term_id,$batch_id);
        }
        $arr = array();
      //  echo "<pre>";print_r($result);exit;
        foreach ($result as $key => $value) { 
            foreach ($value as $key1 => $value1) {
                    $arr[$key] = $value1;
            }
        }
        
        

        $class_records = $classMaster->getRecords();
       $class_records = $this->mergData($class_records, array('class_name'),$count($class_records));
        

        echo "<table cellpadding='20' class='table table-striped table-bordered mb30 jambo_table bulk_action' id='dataTable' style='width:100%;'>";

        echo "<thead>";
        //====[HEADING]======//
        echo "<tr>";
        echo "<th>Section</th>";
        for($i = 1; $i<=$no_of_classes; $i++){
        echo "<th>".$class_records[$no_of_classes - $i]."</th>";
        }
        echo "<th>View More</th>";
        echo "</tr>";
        echo "</thead>";
        //=====[TABLE BODY]======///       
        echo "<tbody>";
          //========[TABLE DATE]===//
       // echo "<pre>";print_r($arr);exit;
       if(count($arr)>0){
        $j=0;
        foreach($arr as $Items => $val){
         
        foreach($val as $key => $Item_arr ){
            $res = 0;
             echo "<tr>";
         
            foreach($Item_arr as $Item_key => $Item){
              if($res == 0){  
                echo '<th>'. $section->getRecordById($Item['section']).'</th>'; 
                $res++;
              }
          $mycourse = explode('-',$Item['class']);
         
          if(in_array($mycourse[0],$courses[$Items])){
         echo "<th ><a href='#' id='class".$i."' style='position:absolute; transform: rotate(-40deg)'>".$mycourse[1]."</a><span style='position:absolute; margin-left:12%; transform: rotate(-40deg)'>". $Item['time']." </span></th>";  }
        else {
                    echo "<th ><a href='#' style = 'text-align:center;' id='class".$i."' >--</a></th>";
                }
              
            }
             echo "<td><a href='".$this->_base_url."academic/batch-schedule/index/type/edit/id/".$Item_arr[1]['batch_schedule_id']."/version/".$Item_arr[1]['version']."/section/".$Item_arr[1]['section']."'>Click Here</a></td>";
      echo"</tr>";
    }
   
        }
    $j++;
        }
        else
        {
            echo "<tr><th class='text-center' colspan='6'>No Record Found</th></tr>";
        }
        echo "</tbody>";
        echo "</table>";
        die;
    }
    
    
    
    
    function numberToRomanRepresentation($number) {
    $map = array('M' => 1000, 'CM' => 900, 'D' => 500, 'CD' => 400, 'C' => 100, 'XC' => 90, 'L' => 50, 'XL' => 40, 'X' => 10, 'IX' => 9, 'V' => 5, 'IV' => 4, 'I' => 1);
    $returnValue = '';
    while ($number > 0) {
        foreach ($map as $roman => $int) {
            if($number >= $int) {
                $number -= $int;
                $returnValue .= $roman;
                break;
            }
        }
    }
    return $returnValue;
}
    
}
