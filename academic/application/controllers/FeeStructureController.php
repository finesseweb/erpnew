<?php

class FeeStructureController extends Zend_Controller_Action {

    private $_siteurl = null;
    private $_db = null;
    private $_flashMessenger = null;
    private $_authontication = null;
    private $_agentsdata = null;
    private $_usersdata = null;
    private $_act = null;
    private $_adminsettings = null;
    Private $_unit_id = null;
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
        if (isset($data)) {
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
    }

    protected function authonticate() {
        $storage = new Zend_Session_Namespace("admin_login");
        $data = $storage->admin_login;
          if($data->role_id == 0)
            $this->_redirect('student-portal/fee-status');
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
        $this->view->action_name = 'structure';
        $this->view->sub_title_name = 'Fee Structure';
        $this->accessConfig->setAccess("SA_ACAD_FEE_STRUCTURE");
        $FeeStructure_model = new Application_Model_FeeStructure();
        $FeeStructure_form = new Application_Form_FeeStructure();
        $FeeStructureItems_model = new Application_Model_FeeStructureItems();
        $FeeStructureTermItems_model = new Application_Model_FeeStructureTermItems();
        $structure_id = $this->_getParam("id");
        //print_r($feehead_id);die;
        $type = $this->_getParam("type");
        $this->view->type = $type;
        $this->view->form = $FeeStructure_form;

        switch ($type) {
            case "add":
                if ($this->getRequest()->isPost()) {
                    if ($FeeStructure_form->isValid($this->getRequest()->getPost())) {
                        $data = $FeeStructure_form->getValues();
                        $last_insert_id = $FeeStructure_model->insert($data);
                        $terms_fee = $this->getRequest()->getPost('terms_fee'); //Get AddMore Fields From View
                     //  echo '<pre>'; print_r($terms_fee);die; 
                        $terms_id = $this->getRequest()->getPost('term_id');
                      
                        foreach (array_filter($terms_fee['grand_result1']) as $key => $grand_result1) {
                            //$counts = $terms_fee['count'];
                            //$count_i = $terms_fee['count_i'];
                            $fee_data = array("structure_id" => $last_insert_id,
                        
                                "total_grand_value" => $terms_fee['grandtotal_total'][$key],
                            );
                            for($i=0;$i<count($terms_id); $i++)
                            {
                                $date = "t".($i+1)."_date";
                                $grand_terms =  "grand_term".($i+1)."_result";
                                $grand_results = "grand_result".($i+1);
                               $fee_data[$date] = $terms_fee[$date];
                               $fee_data [$grand_terms] = $terms_fee[$grand_results][$key];
                            }
                            
                            $FeeStructureItems_model->insert($fee_data);
                        }
                        $terms = $this->getRequest()->getPost('terms');
                        
                        //echo'<pre>';print_r($terms);die;
                        $FeeCategory_model = new Application_Model_FeeCategory();
                        $Category_data = $FeeCategory_model->getCategoryIds();

                        $FeeHeads_model = new Application_Model_FeeHeads();


                        for ($i = 0; $i < count($Category_data); $i++) {
                            $terms_data['structure_id'] = $last_insert_id;
                            $terms_data['category_id'] = $terms['category_id'][$i];
                            $fee_heads = $FeeHeads_model->getFeehead_ids($Category_data[$i]['category_id']);

                            for ($j = 0; $j < count($fee_heads); $j++) {

                                //echo'<pre>';print_r($fee_heads[$j]['feehead_id']);die;
                                //echo'<pre>';print_r($terms_data);die;
                                for ($k = 1; $k <= count($terms_id); $k++) {

                                    $terms_data['terms_id'] = $terms_id[$k-1];
                                    $terms_data['terms'] = $k;
                                    $terms_data['fee_heads_id'] = $fee_heads[$j]['feehead_id'];
                                    $terms_data['feeheads_total'] = $terms['feeheads_total_val' . $terms['category_id'][$i] . ''][$j];
                                    //echo'<pre>';print_r($fee_heads);die;
                                    $terms_data['fees'] = $_POST['term_' . $Category_data[$i]['category_id'] . '_' . $fee_heads[$j]['feehead_id'] . '_' . $k . ''];
                                    //echo'<pre>';print_r($terms_data);die;
                                    //	$terms_data['term2_val'] = $terms['term_'.$terms['category_id'][$i].'_'.$fee_heads[$j]['feehead_id'].'_'.'2'];
                                    //	$terms_data['term3_val'] = $terms['term_'.$terms['category_id'][$i].'_'.$fee_heads[$j]['feehead_id'].'_'.'3'];
                                    //	$terms_data['term4_val'] = $terms['term_'.$terms['category_id'][$i].'_'.$fee_heads[$j]['feehead_id'].'_'.'4'];
                                    //	$terms_data['term5_val'] = $terms['term_'.$terms['category_id'][$i].'_'.$fee_heads[$j]['feehead_id'].'_'.'5'];
                                    $terms_data['cat_row_total'] = $terms['cat_row_total' . $terms['category_id'][$i] . '_' . $i . ''];
                                    //echo'<pre>';print_r($terms_data);die;
                                    
                                    $FeeStructureTermItems_model->insert($terms_data);
                                    //echo $flag;
                                    //$flag++;
                                }
                            }
                        }

                        //echo'<pre>';print_r($terms);die;
                        // foreach(array_filter($terms['category_id']) as $key=>$category_id)
                        // {
                        // $counts = $terms['count'];
                        //echo $terms['feehead_id11']; die;
                        // $count_i = $terms['count_i'];
                        // $terms_data=array("structure_id"=>$last_insert_id,
                        // "category_id"=>$category_id,
                        // "fee_heads_id"=>$terms['feehead_id'.$category_id.''.$counts[$key].''][$key],
                        // "term1_val"=>$terms['term'.$counts[$key].'1'][$key],
                        // "term2_val"=>$terms['term'.$counts[$key].'2'][$key],
                        // "term3_val"=>$terms['term'.$counts[$key].'3'][$key],
                        // "term4_val"=>$terms['term'.$counts[$key].'4'][$key],
                        // "term5_val"=>$terms['term'.$counts[$key].'5'][$key],
                        // "feeheads_total"=>$terms['feeheads_total_val'.$counts[$key].''][$key],
                        // "term1_cat_total"=>$terms['catresult1'.$category_id.''][$key],
                        // "term1_cat_total"=>$terms['catresult1'.$category_id.''][$key],
                        // "term2_cat_total"=>$terms['catresult2'.$category_id.''][$key],
                        // "term3_cat_total"=>$terms['catresult3'.$category_id.''][$key],
                        // "term4_cat_total"=>$terms['catresult4'.$category_id.''][$key],
                        // "term5_cat_total"=>$terms['catresult5'.$category_id.''][$key],
                        // "cat_row_total"=>$terms['cat_row_total'.$category_id.'_'.$count_i[$key].''][$key],			 		 
                        // );
                        //print_r($terms_data);die;
                        // $FeeStructureTermItems_model->insert($terms_data);	
                        // }

                        $this->_flashMessenger->addMessage('Fee Structure Successfully added');

                        $this->_redirect('fee-structure/index');
                    }
                }


                break;
            case 'edit':
                $result = $FeeStructure_model->getRecord($structure_id);
                $item_result = $FeeStructureItems_model->getItemRecords($structure_id);
                //print_r($structure_id);die;
                $this->view->students_name = $structure_id;
                $this->view->item_result = $item_result;
              
               $academic_id = $result['academic_id'];
                $FeeStructure_form->populate($result);
                $this->view->result = $result;
              if ($this->getRequest()->isPost()) {
                    if ($FeeStructure_form->isValid($this->getRequest()->getPost())) {
                        
                        $data = $FeeStructure_form->getValues();
                          
                        $FeeStructure_model->delete(array('academic_id =?' =>  $academic_id));
                        $last_insert_id = $FeeStructure_model->insert($data);
                        $terms_fee = $this->getRequest()->getPost('terms_fee'); //Get AddMore Fields From View
                     //  echo '<pre>'; print_r($terms_fee);die; 
                        $terms_id = $this->getRequest()->getPost('term_id');
                      
                        foreach (array_filter($terms_fee['grand_result1']) as $key => $grand_result1) {
                            //$counts = $terms_fee['count'];
                            //$count_i = $terms_fee['count_i'];
                            $fee_data = array("structure_id" => $last_insert_id,
                        
                                "total_grand_value" => $terms_fee['grandtotal_total'][$key],
                            );
                            for($i=0;$i<count($terms_id); $i++)
                            {
                                $date = "t".($i+1)."_date";
                                $grand_terms =  "grand_term".($i+1)."_result";
                                $grand_results = "grand_result".($i+1);
                               $fee_data[$date] = $terms_fee[$date];
                               $fee_data [$grand_terms] = $terms_fee[$grand_results][$key];
                            }
                             $FeeStructureItems_model->delete(array('structure_id =?' =>  $structure_id));
                            $FeeStructureItems_model->insert($fee_data);
                        }
                        $terms = $this->getRequest()->getPost('terms');
                        
                        //echo'<pre>';print_r($terms);die;
                        $FeeCategory_model = new Application_Model_FeeCategory();
                        $Category_data = $FeeCategory_model->getCategoryIds();

                        $FeeHeads_model = new Application_Model_FeeHeads();


                        for ($i = 0; $i < count($Category_data); $i++) {
                            $terms_data['structure_id'] = $last_insert_id;
                            $terms_data['category_id'] = $terms['category_id'][$i];
                            $fee_heads = $FeeHeads_model->getFeehead_ids($Category_data[$i]['category_id']);

                            for ($j = 0; $j < count($fee_heads); $j++) {

                                //echo'<pre>';print_r($fee_heads[$j]['feehead_id']);die;
                                //echo'<pre>';print_r($terms_data);die;
                                for ($k = 1; $k <= count($terms_id); $k++) {

                                    $terms_data['terms_id'] = $terms_id[$k-1];
                                    $terms_data['terms'] = $k;
                                    $terms_data['fee_heads_id'] = $fee_heads[$j]['feehead_id'];
                                    $terms_data['feeheads_total'] = $terms['feeheads_total_val' . $terms['category_id'][$i] . ''][$j];
                                    //echo'<pre>';print_r($fee_heads);die;
                                    $terms_data['fees'] = $_POST['term_' . $Category_data[$i]['category_id'] . '_' . $fee_heads[$j]['feehead_id'] . '_' . $k . ''];
                                    //echo'<pre>';print_r($terms_data);die;
                                    //	$terms_data['term2_val'] = $terms['term_'.$terms['category_id'][$i].'_'.$fee_heads[$j]['feehead_id'].'_'.'2'];
                                    //	$terms_data['term3_val'] = $terms['term_'.$terms['category_id'][$i].'_'.$fee_heads[$j]['feehead_id'].'_'.'3'];
                                    //	$terms_data['term4_val'] = $terms['term_'.$terms['category_id'][$i].'_'.$fee_heads[$j]['feehead_id'].'_'.'4'];
                                    //	$terms_data['term5_val'] = $terms['term_'.$terms['category_id'][$i].'_'.$fee_heads[$j]['feehead_id'].'_'.'5'];
                                    $terms_data['cat_row_total'] = $terms['cat_row_total' . $terms['category_id'][$i] . '_' . $i . ''];
                                     $FeeStructureTermItems_model->delete(array('structure_id =?' =>  $structure_id));
                                    $FeeStructureTermItems_model->insert($terms_data);
                                    //echo $flag;
                                    //$flag++;
                                }
                            }
                        }
                        $this->_flashMessenger->addMessage('Fee Structure Successfully Updated');

                        $this->_redirect('fee-structure/index');
                    }
                }
                break;
            case 'delete':
                $data['status'] = 2;
                if ($structure_id) {
                    $FeeStructure_model->update($data, array('structure_id=?' => $structure_id));
                    $FeeStructureItems_model->update($data, array('structure_id=?' => $structure_id));
                    $this->_flashMessenger->addMessage('Details Deleted Successfully');
                    $this->_redirect('fee-structure/index');
                }
                break;
            default:
                $messages = $this->_flashMessenger->getMessages();
                $this->view->messages = $messages;
                $result = $FeeStructure_model->getRecords();
                $page = $this->_getParam('page', 1);
                $paginator_data = array(
                    'page' => $page,
                    'result' => $result
                );
                $this->view->paginator = $this->_act->pagination($paginator_data);
                break;
        }
    }

    public function ajaxGetFeeStructureAction() {
        $this->_helper->layout->disableLayout();
        if ($this->_request->isPost() && $this->getRequest()->isXmlHttpRequest()) {
            $academic_year_id = $this->_getParam("academic_year_id");
            $structure_id = $this->_getParam("structure_id");
          
                
            $FeeCategory_model = new Application_Model_FeeCategory();
            $FeeHeads_model = new Application_Model_FeeHeads();
            $FeeStructure_model = new Application_Model_FeeStructure();
            $StructureItems_model = new Application_Model_FeeStructureItems();
            $term_model = new Application_Model_TermMaster();
            $TermItems_model = new Application_Model_FeeStructureTermItems();
            
            if (!empty($structure_id)) {
                 $result = $TermItems_model->getItemRecords($structure_id);
            $this->view->result = $result;
            $result1 = $StructureItems_model->getStructureRecords($structure_id);
            $this->view->result1 = $result1;
            
            
            $academic_year_id  = $TermItems_model->getAcademicId($structure_id);
                
              $terms_data = $term_model->getRecordByAcademicId($academic_year_id['academic_id']);
                    $this->view->term_data = $terms_data; 
            $this->view->structure_id = $structure_id;
            $Category_data = $FeeCategory_model->getCategory();
            $this->view->Category_data = $Category_data;
            $Feeheads_data = $FeeHeads_model->getFeeheads();
            // print_r($Feeheads_data);die;
            $this->view->Feeheads_data = $Feeheads_data;
            } else {
                if (!empty($academic_year_id)) {
                    // $student_model = new Application_Model_StudentPortal();
                    $Category_data = $FeeCategory_model->getCategory();
                    $this->view->Category_data = $Category_data;
                    $Feeheads_data = $FeeHeads_model->getFeeheads();
                       
                    $terms_data = $term_model->getRecordByAcademicId($academic_year_id);
                    $this->view->term_data = $terms_data; 
                    $this->view->structure_id = 0;
                    $this->view->Feeheads_data = $Feeheads_data;
                    // $Electivecourse_model = new Application_Model_ElectiveCourseLearning();
                    //  $electives = $Electivecourse_model->getDropDownList();
                    //print_r($electives);die;
                    //$this->view->electives = $electives;
                }
            }
        }
    }

    public function ajaxGetCheckFeeDataAction() {
        $this->_helper->layout->disableLayout();
        if ($this->_request->isPost() && $this->getRequest()->isXmlHttpRequest()) {
            $academic_year_id = $this->_getParam("academic_year_id");
            //$year_id = $this->_getParam("year_id");
            //echo $academic_year_id;die;
            $FeeStructure_model = new Application_Model_FeeStructure();
            $grade_result = $FeeStructure_model->getValidFeeRecord($academic_year_id);
            $counts = count($grade_result['structure_id']);
            //print_r($counts);die;
            echo json_encode($counts);
            die;
            $this->view->grade_result = $grade_result;
        }
    }

    public function ajaxGetFeeDetailsViewAction() {

        $FeeCategory_model = new Application_Model_FeeCategory();
        $FeeHeads_model = new Application_Model_FeeHeads();
        $FeeStructure_model = new Application_Model_FeeStructure();
        $StructureItems_model = new Application_Model_FeeStructureItems();
         $term_model = new Application_Model_TermMaster();
        $TermItems_model = new Application_Model_FeeStructureTermItems();
        $structure_id = $this->_getParam("id");
        $this->view->structure_id = $structure_id;
       
        $type = $this->_getParam("type");
        $this->view->type = $type;
        //$this->view->form = $GradeAllocationReport_form;
        $this->_helper->layout->disableLayout();
        if ($this->_request->isPost() && $this->getRequest()->isXmlHttpRequest()) {
            $result = $TermItems_model->getItemRecords($structure_id);
            $this->view->result = $result;
            $result1 = $StructureItems_model->getStructureRecords($structure_id);
            $this->view->result1 = $result1;
            
            
            $academic_year_id  = $TermItems_model->getAcademicId($structure_id);
                
              $terms_data = $term_model->getRecordByAcademicId($academic_year_id['academic_id']);
                    $this->view->term_data = $terms_data; 
            
            $Category_data = $FeeCategory_model->getCategory();
            $this->view->Category_data = $Category_data;
            $Feeheads_data = $FeeHeads_model->getFeeheads();
            // print_r($Feeheads_data);die;
            $this->view->Feeheads_data = $Feeheads_data;
        }
    }

    public function structurePdfAction() {

        $FeeCategory_model = new Application_Model_FeeCategory();
        $FeeHeads_model = new Application_Model_FeeHeads();
        $FeeStructure_model = new Application_Model_FeeStructure();
        $StructureItems_model = new Application_Model_FeeStructureItems();
        $TermItems_model = new Application_Model_FeeStructureTermItems();
        $structure_id = $this->_getParam("id");
        $this->view->structure_id = $structure_id;
        //print_r($structure_id); die;
        $result = $TermItems_model->getItemRecords($structure_id);
        //print_r($result);die;
        $this->view->result = $result;
        $result1 = $StructureItems_model->getStructureRecords($structure_id);
        //print_r($result1);die;
        $this->view->result1 = $result1;

        $Category_data = $FeeCategory_model->getCategory();
        $this->view->Category_data = $Category_data;
        $Feeheads_data = $FeeHeads_model->getFeeheads();
        // print_r($Feeheads_data);die;
        $this->view->Feeheads_data = $Feeheads_data;

        $pdfheader = $this->view->render('fee-structure/pdfheader.phtml');
        $pdffooter = $this->view->render('fee-structure/pdffooter.phtml');
        $htmlcontent = $this->view->render('fee-structure/structure-pdf.phtml');
        $this->_act->generatePdf($pdfheader, $pdffooter, $htmlcontent, "Fee Structure Report Details");
    }

}
