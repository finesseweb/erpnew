<?php
class ReportsController extends Zend_Controller_Action {

    private $_siteurl = null;
    private $_db = null;
    private $_flashMessenger = null;
    private $_authontication = null;
    private $_agentsdata = null;
    private $_usersdata = null;
    private $_act = null;
    private $_adminsettings = null;
    private $accessConfig =NULL;

    public function init() {
        $zendConfig = new Zend_Config_Ini(
                APPLICATION_PATH . '/configs/application.ini', APPLICATION_ENV);
                require_once APPLICATION_PATH . '/configs/access_level.inc';
                        
        $this->accessConfig = new accessLevel();
        $config = $zendConfig->mainconfig->toArray();

        $this->view->mainconfig = $config;
        $this->_action = $this->getRequest()->getActionName();
        if ($this->_action == "login" || $this->_action == "forgot-password") {
            $this->_helper->layout->setLayout("adminlogin");
        } else {

            $this->_helper->layout->setLayout("layout");
        }
        $this->_act = new Application_Model_Adminactions();
        $this->_db = Zend_Db_Table::getDefaultAdapter();
        $this->_flashMessenger = $this->_helper->FlashMessenger;
        $this->authonticate();
        $this->view->authontication = $this->_authontication;
    }

    protected function authonticate() {
        $storage = new Zend_Session_Namespace("admin_login");
        $data = $storage->admin_login;


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
	public function htmlpdfAction() {
		$pdfheader = $this->view->render('index/pdfheader.phtml');
		$pdffooter = $this->view->render('index/pdffooter.phtml');
		$htmlcontent = $this->view->render('index/htmlpdf.phtml');
		//echo $pdfheader.$htmlcontent.$pdffooter; die;
		$this->_act->generatePdf($pdfheader, $pdffooter, $htmlcontent, "HTMLPDF");
	}
    public function tyreOrderReportAction() {
        $this->view->action_name = 'reports';
        $this->view->sub_title_name = 'reports';
        $ErpSalesTyreOrderForm_model = new Application_Model_SalesReports();
		$ReportsForm = new Application_Form_ReportsForm();
		$this->view->ReportsForm = $ReportsForm;
        $type = $this->_getParam("type");
		$this->view->type = $type;
        switch ($type) {

            case "search":               
                if ($this->getRequest()->isPost()) {
					if ($ReportsForm->isValid($this->getRequest()->getPost())) {					
						$data = $this->getRequest()->getPost();
						$this->view->start_date = $startdate = $data['start_date'];
						$this->view->enddate = $enddate = $data['end_date'];
						$result = $ErpSalesTyreOrderForm_model->searchbyRecords($startdate, $enddate);
						$export = $this->getRequest()->getPost("export");
						if (isset($export)) {
							if( !empty($startdate) ){
								$export_result = $ErpSalesTyreOrderForm_model->searchbyRecords($startdate, $enddate);
							}else{
								$export_result = $ErpSalesTyreOrderForm_model->getRecords();
							}
							//$heading = "Sales Invoice"; //$this->view->render('index/pdfheader.phtml');							
							//$htmlcontent = $this->view->render('reports/tyre-order-form.phtml');
							//$this->_act->exportExcel($heading, $result, 'Order Form Statement');
							$this->filename = "/excel-" . date( "m-d-Y" ) . "-".mt_rand(10000,20000).".xls";

							$realPath = realpath($this->filename);
							if (false === $realPath )
							{
								touch($this->filename);
								chmod($this->filename, 0777); 
							}

							$this->filename = realpath( $this->filename );
							$this->handle = fopen( $this->filename, "w" );

							//$projectsModul = new Model_DbTable_Projects();
							$projects = $export_result; //$projectsModul->fetchProjects();
							$this->finalData[] = array( 'S.No', 'Order.No.', 'Name of the dealer ( Party )', 'Size', 'Make', 'Tyre Sr. No.', 'O.F.No', 'S/M', 'Remarks');
							$this->finalData[] = array( '', '', '', '', '', '', '', '', '');
							$i=0;
							foreach ($projects as $row)
							{
								$i++;
								if (strlen($row['tyre_order_form_tyres_id']) == 1) {
									$sales_tyre_order_form_id = @(SALE_TYRE_ID) . "000" . $row['tyre_order_form_tyres_id'];
								} else if (strlen($row['tyre_order_form_tyres_id']) == 2) {
									$sales_tyre_order_form_id = @(SALE_TYRE_ID) . "00" . $row['tyre_order_form_tyres_id'];
								} else if (strlen($row['tyre_order_form_tyres_id']) == 3) {
									$sales_tyre_order_form_id = @(SALE_TYRE_ID) . "0" . $row['tyre_order_form_tyres_id'];
								} else {
									$sales_tyre_order_form_id = @(SALE_TYRE_ID) . "" . $row['tyre_order_form_tyres_id'];
								}
													
								$this->finalData[] = array(
									$i,
									$sales_tyre_order_form_id,
									$row['dealer_name'],
									$row['tyre_size'],
									$row['tyre_make_name'],
									$row['sales_order_tyre_no'],
									$row['sales_order_form_number'],
									$row['employee_name']
									
								);
							}
							
							foreach ( $this->finalData AS $finalRow )
							{
								fputcsv( $this->handle, $finalRow, "\t" );
							}	
							
							fclose( $this->handle );

							$this->_helper->layout->disableLayout();
							$this->_helper->viewRenderer->setNoRender();

							$this->getResponse()->setRawHeader( "Content-Type: application/vnd.ms-excel; charset=UTF-8")
								->setRawHeader("Content-Disposition: attachment; filename=excel.xls")
								->setRawHeader("Content-Transfer-Encoding: binary")
								->setRawHeader("Expires: 0")
								->setRawHeader("Cache-Control: must-revalidate, post-check=0, pre-check=0")
								->setRawHeader("Pragma: public")
								->setRawHeader("Content-Length: " . filesize($this->filename))
								->sendResponse();

							readfile($this->filename); 
							exit();
						}
						
						$page = $this->_getParam('page', 1);
						$paginator_data = array(
							'page' => $page,
							'result' => $result
						);
						$this->view->paginator = $this->_act->pagination($paginator_data); 
				    }
                }else{
					$this->_redirect('reports/tyre-order-report');
				}
                break;           
            default:
                $messages = $this->_flashMessenger->getMessages();
                $this->view->messages = $messages;
				$this->view->start_date = $startdate = date( "Y-m-d" );
				$this->view->enddate = $enddate = date( "Y-m-d" );
				// $result = $ErpSalesTyreOrderForm_model->searchbyRecords($startdate, $enddate);
                $result = $ErpSalesTyreOrderForm_model->getRecords();
				//echo '<pre>'; print_r($result); die;
                $page = $this->_getParam('page', 1);
                $paginator_data = array(
                    'page' => $page,
                    'result' => $result
                );
                $this->view->paginator = $this->_act->pagination($paginator_data);
                break;
        }
    }
	
	public function tyreEntryReportAction() {
        $this->view->action_name = 'Tyre Entry Register';
        $this->view->sub_title_name = 'Tyre Entry Register';
        $ErpSalesTyreOrderForm_model = new Application_Model_SalesReports();
		$ReportsForm = new Application_Form_ReportsForm();
		$this->view->ReportsForm = $ReportsForm;
        $type = $this->_getParam("type");
		$this->view->type = $type;
        switch ($type) {

            case "search":               
                if ($this->getRequest()->isPost()) {
					if ($ReportsForm->isValid($this->getRequest()->getPost())) {					
						$data = $this->getRequest()->getPost();
						$this->view->start_date = $startdate = ''; //$data['start_date'];
						$this->view->enddate = $enddate = ''; //$data['end_date'];
						$result = $ErpSalesTyreOrderForm_model->searchbyTyreEntryRecords($startdate, $enddate);
						$export = $this->getRequest()->getPost("export");
						if (isset($export)) {
							if( !empty($startdate) ){
								$export_result = $ErpSalesTyreOrderForm_model->searchbyTyreEntryRecords($startdate, $enddate);
							}else{
								$export_result = $ErpSalesTyreOrderForm_model->getTyreEntryRecords();
							}							
							$this->filename = $_SERVER['DOCUMENT_ROOT']."/hcrpl/excel-export/excel-" . date( "m-d-Y" ) . "-".mt_rand(10000,20000).".xls";

							$realPath = realpath($this->filename);
							if (false === $realPath )
							{
								touch($this->filename);
								chmod($this->filename, 0777); 
							}

							$this->filename = realpath( $this->filename );
							$this->handle = fopen( $this->filename, "w" );

							//$projectsModul = new Model_DbTable_Projects();
							$projects = $export_result; //$projectsModul->fetchProjects();
							$this->finalData[] = array( 'S.No', 'Type ID', 'Date', 'Name of the dealer ( Party )', 'Size', 'Make', 'Tyre Sr. No.', 'O.F.No', 'S/M', 'Pattern', 'Patches', 'Cure No', 'Date', 'Bill/Ch. No.', 'Date', 'Amount', 'Qty', 'Remarks');
							$this->finalData[] = array( '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '');
							$i=0;
							foreach ($projects as $row)
							{
								$i++;
								if (strlen($row['sales_tyre_order_form_id']) == 1) {
									$sales_tyre_order_form_id = @(SALE_ORDER_FORM_ID) . "000" . $row['sales_tyre_order_form_id'];
								} else if (strlen($row['sales_tyre_order_form_id']) == 2) {
									$sales_tyre_order_form_id = @(SALE_ORDER_FORM_ID) . "00" . $row['sales_tyre_order_form_id'];
								} else if (strlen($row['sales_tyre_order_form_id']) == 3) {
									$sales_tyre_order_form_id = @(SALE_ORDER_FORM_ID) . "0" . $row['sales_tyre_order_form_id'];
								} else {
									$sales_tyre_order_form_id = @(SALE_ORDER_FORM_ID) . "" . $row['sales_tyre_order_form_id'];
								}
								
								if (strlen($row['tyre_order_form_tyres_id']) == 1) {
									$tyre_order_form_tyres_id = @(SALE_TYRE_ID) . "000" . $row['tyre_order_form_tyres_id'];
								} else if (strlen($row['tyre_order_form_tyres_id']) == 2) {
									$tyre_order_form_tyres_id = @(SALE_TYRE_ID) . "00" . $row['tyre_order_form_tyres_id'];
								} else if (strlen($row['tyre_order_form_tyres_id']) == 3) {
									$tyre_order_form_tyres_id = @(SALE_TYRE_ID) . "0" . $row['tyre_order_form_tyres_id'];
								} else {
									$tyre_order_form_tyres_id = @(SALE_TYRE_ID) . "" . $row['tyre_order_form_tyres_id'];
								}
								
								$entry_date = date(DATE_PREFIX, strtotime($row['sales_order_tyre_order_date']));
								$final_inspection_added_date = date(DATE_PREFIX, strtotime($row['final_inspection_added_date']));
								$tyre_dispatch_added_date = date(DATE_PREFIX, strtotime($row['tyre_dispatch_added_date']));
								//Invoice Data 
								$repairing_cost = 0;
								$tyre_repair_cost = json_decode($row['tyre_repair_cost']);
								if (isset($tyre_repair_cost->$row['tyre_order_form_tyres_id'])) {
									$repairing_cost = $tyre_repair_cost->$row['tyre_order_form_tyres_id'];
								}
								$repair_remarks_object = $row['tyre_order_form_tyres_id'] . "remarks";
								$repairing_remarks = '';
								if (isset($tyre_repair_cost->$repair_remarks_object)) {
									$repairing_remarks = $tyre_repair_cost->$repair_remarks_object;
								}
								$tyre_retread_cost_object = $row['tyre_order_form_tyres_id'] . "tyre_retread_cost";
								if (isset($tyre_repair_cost->$tyre_retread_cost_object)) {
									$row['sales_order_tyre_price'] = $tyre_repair_cost->$tyre_retread_cost_object;
								}
								$qty = 1;
								
								$this->finalData[] = array(
									$i,
									$tyre_order_form_tyres_id,
									$entry_date,
									$row['dealer_name'],
									$row['tyre_size'],
									$row['tyre_make_name'],
									$row['sales_order_tyre_no'],
									$row['sales_order_form_number'],
									$row['employee_name'],
									$row['tyre_tread_name'],
									$row['item_name'],
									$row['curing_no'],
									$final_inspection_added_date,
									$sales_tyre_order_form_id,
									$tyre_dispatch_added_date,
									($row['sales_order_tyre_price'] + $repairing_cost),
									$qty,
									$repairing_remarks
								);
							}
							
							foreach ( $this->finalData AS $finalRow )
							{
								fputcsv( $this->handle, $finalRow, "\t" );
							}	
							
							fclose( $this->handle );

							$this->_helper->layout->disableLayout();
							$this->_helper->viewRenderer->setNoRender();

							$this->getResponse()->setRawHeader( "Content-Type: application/vnd.ms-excel; charset=UTF-8")
								->setRawHeader("Content-Disposition: attachment; filename=excel.xls")
								->setRawHeader("Content-Transfer-Encoding: binary")
								->setRawHeader("Expires: 0")
								->setRawHeader("Cache-Control: must-revalidate, post-check=0, pre-check=0")
								->setRawHeader("Pragma: public")
								->setRawHeader("Content-Length: " . filesize($this->filename))
								->sendResponse();

							readfile($this->filename); 
							exit();
						}
						
						$page = $this->_getParam('page', 1);
						$paginator_data = array(
							'page' => $page,
							'result' => $result
						);
						$this->view->paginator = $this->_act->pagination($paginator_data); 
				    }
                }else{
					$this->_redirect('reports/tyre-entry-report');
				}
                break;           
            default:
                $messages = $this->_flashMessenger->getMessages();
                $this->view->messages = $messages;
				$this->view->start_date = $startdate = date( "Y-m-d" );
				$this->view->enddate = $enddate = date( "Y-m-d" );
				// $result = $ErpSalesTyreOrderForm_model->searchbyTyreEntryRecords($startdate, $enddate);
                $result = $ErpSalesTyreOrderForm_model->getTyreEntryRecords();
				//echo '<pre>'; print_r($result); die;
                $page = $this->_getParam('page', 1);
                $paginator_data = array(
                    'page' => $page,
                    'result' => $result
                );
                $this->view->paginator = $this->_act->pagination($paginator_data);
                break;
        }
    }
	//Production reports
	public function tyreProductionReportAction() {
        $this->view->action_name = 'Production Statement';
        $this->view->sub_title_name = 'Production Statement';
        $ErpSalesTyreOrderForm_model = new Application_Model_SalesReports();
		$this->view->curenumber = $curenumber = $ErpSalesTyreOrderForm_model->getCureNumber();
		$ReportsForm = new Application_Form_ReportsForm();		
		$this->view->ReportsForm = $ReportsForm;
        $type = $this->_getParam("type");
		$this->view->type = $type;
        switch ($type) {

            case "search":               
                if ($this->getRequest()->isPost()) {
					if ($ReportsForm->isValid($this->getRequest()->getPost())) {					
						$data = $this->getRequest()->getPost();
						$this->view->start_date = $startdate = $data['start_date'];
						$this->view->enddate = $enddate = $data['end_date'];
						$this->view->cure_number = $cure_no = $data['cure_number'];
						if( !empty($cure_no) ){
							$result = $ErpSalesTyreOrderForm_model->searchbyTyreProductionRecordsByCure($cure_no, $startdate, $enddate);
						}else{
							$result = $ErpSalesTyreOrderForm_model->searchbyTyreProductionRecords($startdate, $enddate);
						}
						$export = $this->getRequest()->getPost("export");
						if (isset($export)) {							
							if( !empty($startdate) ){
								if( !empty($cure_no) ){
									$export_result = $ErpSalesTyreOrderForm_model->searchbyTyreProductionRecordsByCure($cure_no, $startdate, $enddate);
								}else{
									$export_result = $ErpSalesTyreOrderForm_model->searchbyTyreProductionRecords($startdate, $enddate);
								}
							}else{
								if( !empty($cure_no) ){
									$export_result = $ErpSalesTyreOrderForm_model->getTyreProductionRecordsByCure($cure_no);
								}else{
									$export_result = $ErpSalesTyreOrderForm_model->getTyreProductionRecords();
								}
							}							
							$this->filename = $_SERVER['DOCUMENT_ROOT']."/hcrpl/excel-export/excel-" . date( "m-d-Y" ) . "-".mt_rand(10000,20000).".xls";

							$realPath = realpath($this->filename);
							if (false === $realPath )
							{
								touch($this->filename);
								chmod($this->filename, 0777); 
							}

							$this->filename = realpath( $this->filename );
							$this->handle = fopen( $this->filename, "w" );

							//$projectsModul = new Model_DbTable_Projects();
							$projects = $export_result; //$projectsModul->fetchProjects();
							$this->finalData[] = array( 'S.No', 'Date', 'Order.No.', 'Name of the dealer ( Party )', 'Pattern', 'Cure No', 'Length', 'Patches', 'Remarks');
							$this->finalData[] = array( '', '', '', '', '', '', '', '', '');
							$i=0;
							foreach ($projects as $row)
							{
								$i++;
								if (strlen($row['sales_tyre_order_form_id']) == 1) {
									$sales_tyre_order_form_id = @(SALE_ORDER_FORM_ID) . "000" . $row['sales_tyre_order_form_id'];
								} else if (strlen($row['sales_tyre_order_form_id']) == 2) {
									$sales_tyre_order_form_id = @(SALE_ORDER_FORM_ID) . "00" . $row['sales_tyre_order_form_id'];
								} else if (strlen($row['sales_tyre_order_form_id']) == 3) {
									$sales_tyre_order_form_id = @(SALE_ORDER_FORM_ID) . "0" . $row['sales_tyre_order_form_id'];
								} else {
									$sales_tyre_order_form_id = @(SALE_ORDER_FORM_ID) . "" . $row['sales_tyre_order_form_id'];
								}
								
								$final_inspection_date = date(DATE_PREFIX, strtotime($row['final_inspection_added_date']));
								$production_added_date = date(DATE_PREFIX, strtotime($row['production_added_date']));
								
								$this->finalData[] = array(
									$i,
									$final_inspection_date,
									$sales_tyre_order_form_id,									
									$row['dealer_name'],
									$row['tyre_tread_name'],
									$row['curing_no'],
									'',
									$row['item_name'],
									$row['final_inspection_remarks']
								);
							}
							
							foreach ( $this->finalData AS $finalRow )
							{
								fputcsv( $this->handle, $finalRow, "\t" );
							}	
							
							fclose( $this->handle );

							$this->_helper->layout->disableLayout();
							$this->_helper->viewRenderer->setNoRender();

							$this->getResponse()->setRawHeader( "Content-Type: application/vnd.ms-excel; charset=UTF-8")
								->setRawHeader("Content-Disposition: attachment; filename=excel.xls")
								->setRawHeader("Content-Transfer-Encoding: binary")
								->setRawHeader("Expires: 0")
								->setRawHeader("Cache-Control: must-revalidate, post-check=0, pre-check=0")
								->setRawHeader("Pragma: public")
								->setRawHeader("Content-Length: " . filesize($this->filename))
								->sendResponse();

							readfile($this->filename); 
							exit();
						}
						
						$page = $this->_getParam('page', 1);
						$paginator_data = array(
							'page' => $page,
							'result' => $result
						);
						$this->view->paginator = $this->_act->pagination($paginator_data); 
				    }
                }else{
					$this->_redirect('reports/tyre-production-report');
				}
                break;           
            default:
                $messages = $this->_flashMessenger->getMessages();
                $this->view->messages = $messages;
				$this->view->start_date = $startdate = date( "Y-m-d" );
				$this->view->enddate = $enddate = date( "Y-m-d" );
				// $result = $ErpSalesTyreOrderForm_model->searchbyTyreEntryRecords($startdate, $enddate);
                $result = $ErpSalesTyreOrderForm_model->getTyreProductionRecords();
				//echo '<pre>'; print_r($result); die;
                $page = $this->_getParam('page', 1);
                $paginator_data = array(
                    'page' => $page,
                    'result' => $result
                );
                $this->view->paginator = $this->_act->pagination($paginator_data);
                break;
        }
    }
	
	//Ajax product report list
	public function ajaxProductionReportAction() {

        $this->_helper->layout->disableLayout();
        $cure_no = $this->_getParam("cure_no");
		$ErpSalesTyreOrderForm_model = new Application_Model_SalesReports();
		if( !empty($cure_no) ){
			$this->view->start_date = $startdate = $this->_getParam('start_date');
			$this->view->enddate = $enddate = $this->_getParam('end_date');
			$this->view->curenumber = $curenumber = $ErpSalesTyreOrderForm_model->getCureNumber();
			if( !empty($startdate) ){
				$result = $ErpSalesTyreOrderForm_model->searchbyTyreProductionRecordsByCure($cure_no, $startdate, $enddate);
			}else{
				$result = $ErpSalesTyreOrderForm_model->getTyreProductionRecordsByCure($cure_no);
			}	
		}else{
			$result = $ErpSalesTyreOrderForm_model->getTyreProductionRecords();
			$this->view->cure_number = '';
		}
		
		$page = $this->_getParam('page', 1);
		$paginator_data = array(
			'page' => $page,
			'result' => $result
		);
		$this->view->paginator = $this->_act->pagination($paginator_data);
    }
	
	public function tyreDispatchReportAction() {
        $this->view->action_name = 'Sales Statement';
        $this->view->sub_title_name = 'Sales Statement';
        $ErpSalesTyreOrderForm_model = new Application_Model_SalesReports();
		$ReportsForm = new Application_Form_ReportsForm();
		$this->view->ReportsForm = $ReportsForm;
        $type = $this->_getParam("type");
		$this->view->type = $type;
        switch ($type) {

            case "search":               
                if ($this->getRequest()->isPost()) {
					if ($ReportsForm->isValid($this->getRequest()->getPost())) {					
						$data = $this->getRequest()->getPost();
						$this->view->start_date = $startdate = $data['start_date'];
						$this->view->enddate = $enddate = $data['end_date'];
						$result = $ErpSalesTyreOrderForm_model->searchbyDispatchtyres($startdate, $enddate);
						$export = $this->getRequest()->getPost("export");
						if (isset($export)) {
							if( !empty($startdate) ){
								$export_result = $ErpSalesTyreOrderForm_model->searchbyDispatchtyres($startdate, $enddate);
							}else{
								$export_result = $ErpSalesTyreOrderForm_model->getDispatchtyres();
							}
							//$heading = "Sales Invoice"; //$this->view->render('index/pdfheader.phtml');							
							//$htmlcontent = $this->view->render('reports/tyre-order-form.phtml');
							//$this->_act->exportExcel($heading, $result, 'Order Form Statement');
							$this->filename = $_SERVER['DOCUMENT_ROOT']."/hcrpl/excel-export/excel-" . date( "m-d-Y" ) . "-".mt_rand(10000,20000).".xls";

							$realPath = realpath($this->filename);
							if (false === $realPath )
							{
								touch($this->filename);
								chmod($this->filename, 0777); 
							}

							$this->filename = realpath( $this->filename );
							$this->handle = fopen( $this->filename, "w" );

							//$projectsModul = new Model_DbTable_Projects();
							$projects = $export_result; //$projectsModul->fetchProjects();
							$this->finalData[] = array( 'S.No', 'Invoice No.', 'Bill/Ch. No', 'Date', 'Amount', 'Qty', 'Remarks');
							$this->finalData[] = array( '', '', '', '', '', '', '');
							$i=0;
							foreach ($projects as $row)
							{
								$i++;
								if (strlen($row['sales_tyre_order_form_id']) == 1) {
									$sales_tyre_order_form_id = @(SALE_ORDER_FORM_ID) . "000" . $row['sales_tyre_order_form_id'];
								} else if (strlen($row['sales_tyre_order_form_id']) == 2) {
									$sales_tyre_order_form_id = @(SALE_ORDER_FORM_ID) . "00" . $row['sales_tyre_order_form_id'];
								} else if (strlen($row['sales_tyre_order_form_id']) == 3) {
									$sales_tyre_order_form_id = @(SALE_ORDER_FORM_ID) . "0" . $row['sales_tyre_order_form_id'];
								} else {
									$sales_tyre_order_form_id = @(SALE_ORDER_FORM_ID) . "" . $row['sales_tyre_order_form_id'];
								}
								
								$date = date(@DATE_PREFIX, strtotime($row['tyre_dispatch_added_date']) );
													
								$this->finalData[] = array(
									$i,
									$row['tyre_invoice_id'],
									$sales_tyre_order_form_id,
									$date,
									$row['total_amt'],
									$row['qty']
								);
							}
							
							foreach ( $this->finalData AS $finalRow )
							{
								fputcsv( $this->handle, $finalRow, "\t" );
							}	
							
							fclose( $this->handle );

							$this->_helper->layout->disableLayout();
							$this->_helper->viewRenderer->setNoRender();

							$this->getResponse()->setRawHeader( "Content-Type: application/vnd.ms-excel; charset=UTF-8")
								->setRawHeader("Content-Disposition: attachment; filename=excel.xls")
								->setRawHeader("Content-Transfer-Encoding: binary")
								->setRawHeader("Expires: 0")
								->setRawHeader("Cache-Control: must-revalidate, post-check=0, pre-check=0")
								->setRawHeader("Pragma: public")
								->setRawHeader("Content-Length: " . filesize($this->filename))
								->sendResponse();

							readfile($this->filename); 
							exit();
						}
						
						$page = $this->_getParam('page', 1);
						$paginator_data = array(
							'page' => $page,
							'result' => $result
						);
						$this->view->paginator = $this->_act->pagination($paginator_data); 
				    }
                }else{
					$this->_redirect('reports/tyre-dispatch-report');
				}
                break;           
            default:
                $messages = $this->_flashMessenger->getMessages();
                $this->view->messages = $messages;
				$this->view->start_date = $startdate = date( "Y-m-d" );
				$this->view->enddate = $enddate = date( "Y-m-d" );
				//$result = $ErpSalesTyreOrderForm_model->searchbyDispatchtyres($startdate, $enddate);
                $result = $ErpSalesTyreOrderForm_model->getDispatchtyres();
				//echo '<pre>'; print_r($result); die;
                $page = $this->_getParam('page', 1);
                $paginator_data = array(
                    'page' => $page,
                    'result' => $result
                );
                $this->view->paginator = $this->_act->pagination($paginator_data);
                break;
        }
    }
	
	public function dailyStockReportAction() {
        $this->view->action_name = 'inventory';
        $this->view->sub_title_name = 'Daily Stock Statement';
        $ErpSalesTyreOrderForm_model = new Application_Model_SalesReports();
		$ReportsForm = new Application_Form_ReportsForm();
		$this->view->ReportsForm = $ReportsForm;
        $type = $this->_getParam("type");
		$this->view->type = $type;
		$page_id = $this->_getParam("page");
		$this->view->pageurl ='page/'.$page_id;
        switch ($type) {

            case "search":               
                if ($this->getRequest()->isPost() || $this->getRequest()->isGet()) {
					//if ($ReportsForm->isValid($this->getRequest()->getPost())) {					
						$data = $this->getRequest()->getPost();
						if( $this->getRequest()->getPost() ){						
							$data = $this->getRequest()->getPost();
						}else{
							$data['start_date'] = $this->_getParam("from");
							$data['end_date'] = $this->_getParam("to");
						}
						$this->view->start_date = $startdate = $data['start_date'];
						$this->view->enddate = $enddate = $data['end_date'];
						$from = $data['start_date'];
						$to = $data['end_date'];
						$ReportsForm->populate($data);
						$result = $ErpSalesTyreOrderForm_model->searchbyDailyStockRecords($startdate, $enddate);
						$export = $this->getRequest()->getPost("export");
						if (isset($export)) {
							if( !empty($startdate) ){
								$export_result = $ErpSalesTyreOrderForm_model->searchbyDailyStockRecords($startdate, $enddate);
							}else{
								$export_result = $ErpSalesTyreOrderForm_model->getDailyStockRecords();
							}
							$page = $this->_getParam('page', 1);
							$paginator_data = array(
								'page' => $page,
								'result' => $export_result
							);
							$this->view->paginator = $export_result; //$this->_act->pagination($paginator_data);
							$pdfheader = ''; //$this->view->render('index/header.phtml');
							$pdffooter = '';//$this->view->render('index/pdffooter.phtml');
							$htmlcontent = $this->view->render('reports/daily-stock-report-pdf.phtml');
							$this->_act->generatePdf($pdfheader, $pdffooter, $htmlcontent, "Daily Stock Statement");
						}
						
					
							//$heading = "Sales Invoice"; //$this->view->render('index/pdfheader.phtml');							
							//$htmlcontent = $this->view->render('reports/tyre-order-form.phtml');
							//$this->_act->exportExcel($heading, $result, 'Order Form Statement');
							/* $this->filename = $_SERVER['DOCUMENT_ROOT']."/hcrpl/excel-export/excel-" . date( "m-d-Y" ) . "-".mt_rand(10000,20000).".xls";

							$realPath = realpath($this->filename);
							if (false === $realPath )
							{
								touch($this->filename);
								chmod($this->filename, 0777); 
							}

							$this->filename = realpath( $this->filename );
							$this->handle = fopen( $this->filename, "w" );

							//$projectsModul = new Model_DbTable_Projects();
							$projects = $export_result; //$projectsModul->fetchProjects();
							$this->finalData[] = array( 'S.No', 'Item', 'Quantity BF', 'Quantity Recd', 'Quantity Issue', 'Quantity Balance');
							$this->finalData[] = array( '', '', '', '', '', '');
							$i=0;
							$total_balance = 0;
							$total_item_qty = 0;
							$total_issue_qty = 0;
							$total_receipt_qty = 0;
							foreach ($projects as $row)
							{
								$total = ($row['pis_receipt_qty'] + $row['item_quantity'] + $row['grn_item_qty']);
								$balance = ($total - $row['pis_issue_qty']);
								$total_balance += $balance;
								$total_item_qty += $row['item_quantity'];
								$total_issue_qty += $row['pis_issue_qty'];
								$total_receipt_qty += ($row['pis_receipt_qty'] + $row['grn_item_qty']);
								$pis_issue_qty = 0;
								if( isset( $row['pis_issue_qty'] ) ){
									$pis_issue_qty = $row['pis_issue_qty'];
								}
								$i++;																					
								$this->finalData[] = array(
									$i,									
									$row['item_name'],
									$row['item_quantity'],
									$row['pis_receipt_qty'] + $row['grn_item_qty'],
									$pis_issue_qty,
									$balance
									
								);
							}
							
							$this->finalData[] = array( 'Total', '', $total_item_qty, $total_receipt_qty, $total_issue_qty, $total_balance);
							//echo '<pre>';
							//print_r($this->finalData); die;
							foreach ( $this->finalData AS $finalRow )
							{
								fputcsv( $this->handle, $finalRow, "\t" );
							}	
							
							fclose( $this->handle );

							$this->_helper->layout->disableLayout();
							$this->_helper->viewRenderer->setNoRender();

							$this->getResponse()->setRawHeader( "Content-Type: application/vnd.ms-excel; charset=UTF-8")
								->setRawHeader("Content-Disposition: attachment; filename=excel.xls")
								->setRawHeader("Content-Transfer-Encoding: binary")
								->setRawHeader("Expires: 0")
								->setRawHeader("Cache-Control: must-revalidate, post-check=0, pre-check=0")
								->setRawHeader("Pragma: public")
								->setRawHeader("Content-Length: " . filesize($this->filename))
								->sendResponse();

							readfile($this->filename); 
							exit();
						} */
						
						$page = $this->_getParam('page', 1);
						$paginator_data = array(
							'page' => $page,
							'result' => $result
						);
						$this->view->paginator = $this->_act->pagination($paginator_data); 
				    //}
                }else{
					$this->_redirect('reports/daily-stock-report');
				}
                break;           
            default:
                $messages = $this->_flashMessenger->getMessages();
                $this->view->messages = $messages;
			//	$this->view->start_date = $startdate = date( "Y-m-d" );
				//$this->view->enddate = $enddate = date( "Y-m-d" );
				//$data = array('start_date' => $startdate, 'end_date' => $enddate);
				//$ReportsForm->populate($data);
				//$result = $ErpSalesTyreOrderForm_model->searchbyDailyStockRecords($startdate, $enddate);
                $result = $ErpSalesTyreOrderForm_model->getDailyStockRecords();
				//echo '<pre>'; print_r($result); die;
                $page = $this->_getParam('page', 1);
                $paginator_data = array(
                    'page' => $page,
                    'result' => $result
                );
                $this->view->paginator = $this->_act->pagination($paginator_data);
                break;
        }
    }
	
	//Tata motors Challan Summary
	public function challanSummeryAction() {
        $this->view->action_name = 'Challan Summary';
        $this->view->sub_title_name = 'Challan Summary';
        $ErpSalesTyreOrderForm_model = new Application_Model_SalesReports();
		$ErpItemsMaster_model = new Application_Model_ErpItemsMaster();
		$this->view->patches = $patches = $ErpItemsMaster_model->getPatchItems();
		$ReportsForm = new Application_Form_ReportsForm();
		$this->view->ReportsForm = $ReportsForm;
        $type = $this->_getParam("type");
		$this->view->type = $type;        
		switch ($type) {

            case "search":               
                if ($this->getRequest()->isPost()) {
					if ($ReportsForm->isValid($this->getRequest()->getPost())) {					
						$data = $this->getRequest()->getPost();
						$this->view->start_date = $startdate = $data['start_date'];
						$this->view->enddate = $enddate = $data['end_date'];
						$result = $ErpSalesTyreOrderForm_model->searchbyChallanSummery($startdate, $enddate);
						$export = $this->getRequest()->getPost("export");
						if (isset($export)) {
							if( !empty($startdate) ){
								$export_result = $ErpSalesTyreOrderForm_model->searchbyChallanSummery($startdate, $enddate);
							}else{
								$export_result = $ErpSalesTyreOrderForm_model->getChallanSummery();
							}
							//$heading = "Sales Invoice"; //$this->view->render('index/pdfheader.phtml');							
							//$htmlcontent = $this->view->render('reports/tyre-order-form.phtml');
							//$this->_act->exportExcel($heading, $result, 'Order Form Statement');
							$this->filename = $_SERVER['DOCUMENT_ROOT']."/hcrpl/excel-export/excel-" . date( "m-d-Y" ) . "-".mt_rand(10000,20000).".xls";

							$realPath = realpath($this->filename);
							if (false === $realPath )
							{
								touch($this->filename);
								chmod($this->filename, 0777); 
							}

							$this->filename = realpath( $this->filename );
							$this->handle = fopen( $this->filename, "w" );

							//$projectsModul = new Model_DbTable_Projects();
							$patches_th = array();							
							foreach($patches as $k=>$val){
								//$patches_th[]= "'".$val."'";
								$patches_th[]= $val;
							}
							
							$imp_patch = $patches_th;//implode(", ", $patches_th);
							//echo $imp_patch; die;	
							
							$projects = $export_result; //$projectsModul->fetchProjects();
							$this->finalData[] = array( '', '', '', '', '', '', '', '', '', '', '', 'Patches', '', '');
							//$this->finalData[] = array( 'S.No', 'Challan No.', 'Date', 'Depot', 'Total', 'Retread', 'Only Cut Repair', 'Old Belt', 'FOC Repair', $imp_patch);	
							$array_fields =  array( 'S.No', 'Challan No.', 'Date', 'Depot', 'Total', 'Retread', 'Only Cut Repair', 'Old Belt', 'FOC Repair');
							$this->finalData[] = array_merge($array_fields, $patches_th);
							$this->finalData[] = array( '', '', '', '', '', '', '', '', '');
							$i=0;
							foreach ($projects as $row)
							{
								$i++;															
								$date = date(@DATE_PREFIX, strtotime($row['tyre_invoice_added_date']) );
								// Start ****** 
								$process_itemid = array();
								if( !empty($row['item_id']) ){
									$process_itemid = explode(',', $row['item_id']);
								}
								
								$process_items_usage = array();
								if( !empty($row['items_usage']) ){
									$process_items_usage = explode(',', $row['items_usage']);
								}
								$patch_td_count = array();
								foreach($patches as $k=>$val){
									//$patches_th[]= "'".$val."'";
									$patches_th[]= $val;								
									$j = 0;
									$patch_count = 0;
									foreach($process_itemid as $sk=>$pval){
										if( ($pval == $k) && !empty($process_items_usage[$j]) ){
											$patch_count += $process_items_usage[$j];
										}
									$j++;
									}
									
									$patch_td_count[] = $patch_count;
								}								
								// End								
								$row_of_array = array(
									$i,
									$row['tyre_invoice_id'],
									$date,
									$row['dealer_name'],
									$row['qty'],
									$row['retread'],			
									$row['cut_repair'],			
									$row['old_belt'],
									$row['claim_repair']
								);
								
								$this->finalData[] = array_merge($row_of_array, $patch_td_count);
							}
							
							foreach ( $this->finalData AS $finalRow )
							{
								fputcsv( $this->handle, $finalRow, "\t" );
							}	
							
							fclose( $this->handle );

							$this->_helper->layout->disableLayout();
							$this->_helper->viewRenderer->setNoRender();

							$this->getResponse()->setRawHeader( "Content-Type: application/vnd.ms-excel; charset=UTF-8")
								->setRawHeader("Content-Disposition: attachment; filename=excel.xls")
								->setRawHeader("Content-Transfer-Encoding: binary")
								->setRawHeader("Expires: 0")
								->setRawHeader("Cache-Control: must-revalidate, post-check=0, pre-check=0")
								->setRawHeader("Pragma: public")
								->setRawHeader("Content-Length: " . filesize($this->filename))
								->sendResponse();

							readfile($this->filename); 
							exit();
						}
						
						$page = $this->_getParam('page', 1);
						$paginator_data = array(
							'page' => $page,
							'result' => $result
						);
						$this->view->paginator = $this->_act->pagination($paginator_data); 
				    }
                }else{
					$this->_redirect('reports/challan-summery');
				}
                break;           
            default:
                $messages = $this->_flashMessenger->getMessages();
                $this->view->messages = $messages;
				$this->view->start_date = $startdate = date( "Y-m-d" );
				$this->view->enddate = $enddate = date( "Y-m-d" );
				//$result = $ErpSalesTyreOrderForm_model->searchbyDispatchtyres($startdate, $enddate);
                $result = $ErpSalesTyreOrderForm_model->getChallanSummery();
				//echo '<pre>'; print_r($result); die;
                $page = $this->_getParam('page', 1);
                $paginator_data = array(
                    'page' => $page,
                    'result' => $result
                );
                $this->view->paginator = $this->_act->pagination($paginator_data);
                break;
        }
    }

}
