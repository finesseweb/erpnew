<?php

class Application_Model_Adminactions extends Zend_Db_Table {

    private $_adminsettings = '';
	private $_auth_id = '';
	private $_role_id = '';
	private $_createdby_id = '';

    public function init() {
      
        //$this->_adminsettings = $this->getAdminSettings();
		$storage = new Zend_Session_Namespace("admin_login");
        $data    = $storage->admin_login;
		$auth_id = '';
		$role_id = '';
		$createdby_id = '';
		if( isset($data) ){
               
			if($data->role_id == 1){
				$auth_id = $data->admin_id;
				$role_id = $data->role_id;
			}
			
			if($data->role_id == 3){
				$auth_id = $data->branch_id;
				$role_id = $data->role_id;
			}
			
			if($data->role_id == 4){
				$auth_id = $data->employee_id;
				$role_id = $data->role_id;
				$createdby_id = $data->createdby_id;
			}
			
			if($data->role_id == 5){
				$auth_id = $data->admin_id;
				$role_id = $data->role_id;
			}
		}
		
		$this->_auth_id = $auth_id;
		$this->_role_id = $role_id;
		$this->_createdby_id = $createdby_id;
$this->_employee_name = $auth_id;
		
    }
	
	public function auth_id() {
        return $this->_auth_id;
    }
	
	public function role_id() {
        return $this->_role_id;
    }
public function employee_name() {
	    //$employee_name = $data->employee_id;
	    return $this->_employee_name;
	}
	public function unserialize_php($string){
		$data = unserialize($string);
		return $data;
	}
	//Employee 
	public function createdby_id() {
        return $this->_createdby_id;
    }
	
    public function pagination($data) {
        $paginator = Zend_Paginator::factory($data['result']);
        $paginator->setItemCountPerPage();
        $paginator->setCurrentPageNumber($data['page']);
        $paginator->setDefaultPageRange(5);
        return $paginator;
    }

    public function generatePdf($pdfheader, $pdffooter, $htmlcontent, $filename, $pagetype = 'P') {
        require_once ('../application/public/mpdf/mpdf.php');
        ini_set("max_execution_time", 1800);
        ini_set('memory_limit', '-1');
        ob_clean();
        //$mpdf = new mPDF($pagetype, 'A4', '', '', 10, 15, 5, 5, 10, 10);
		$mpdf = new mPDF($pagetype, 'A4', '', '', 10, 15, 3, 5, 5, 10);
       // $mpdf->SetHTMLHeader($pdfheader);
        $mpdf->useOnlyCoreFonts = true;
        $mpdf->SetProtection(array(
            'print'
        ));
        $mpdf->SetTitle("DMI Gradesheet");
        $mpdf->SetAuthor("Acme Trading Co.");
        //$mpdf->SetWatermarkText("HCR(P)L");
        $mpdf->showWatermarkText = true;
        $mpdf->watermark_font = 'DMI';
        $mpdf->watermarkTextAlpha = 0.1;
        $mpdf->SetHTMLFooter($pdffooter);
		$page_htmlcontent = $pdfheader.$htmlcontent;
        $mpdf->WriteHTML($page_htmlcontent);
        $download = 0;
        if ($download) {

        } else {
            $filePath = "$filename.pdf";
            $mpdf->Output($filePath, 'D');
        }
        exit();
    }
    
    
        public function generatePdf1($pdfheader, $pdffooter, $htmlcontent, $filename, $pagetype = 'P') {
        require_once ('../application/public/mpdf/mpdf.php');
        ini_set("max_execution_time", 1800);
        ini_set('memory_limit', '-1');
        ob_clean();
        //$mpdf = new mPDF($pagetype, 'A4', '', '', 10, 15, 5, 5, 10, 10);
		$mpdf = new mPDF($pagetype, 'A4', '', '', 10, 15, 3, 5, 5, 10);
       // $mpdf->SetHTMLHeader($pdfheader);
        $mpdf->useOnlyCoreFonts = true;
        $mpdf->SetProtection(array(
            'print'
        ));
        $mpdf->SetTitle("DMI Gradesheet");
        $mpdf->SetAuthor("Acme Trading Co.");
        //$mpdf->SetWatermarkText("HCR(P)L");
        $mpdf->showWatermarkText = true;
        $mpdf->watermark_font = 'DMI';
        $mpdf->watermarkTextAlpha = 0.1;
        $mpdf->SetHTMLFooter($pdffooter);
		$page_htmlcontent = $pdfheader.$htmlcontent;
        $mpdf->WriteHTML($page_htmlcontent);
        $download = 0;
        if ($download) {

        } else {
            $filePath = "$filename.pdf";
            $mpdf->Output($filePath, 'D');
        }
        exit();
    }

    public function uploadfile($file, $uploadPath) {
        $upload = new Zend_File_Transfer_Adapter_Http();
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $newName = str_replace(' ', '-', basename($file['name'])) .
                substr(md5(uniqid(rand(), true)), 0, 7) . '.' . $extension;
        if (!is_dir($uploadPath)) {
            mkdir($uploadPath, 0777, true);
        }
        $upload->addValidator('Count', false, array(
                    'min' => 0,
                    'max' => 1
                ))
                ->addValidator('IsImage', false, 'jpeg,pjpeg,jpg,png,gif,bmp')
                ->addValidator('Size', false, array(
                    'max' => '10MB'
                ))
                ->setDestination($uploadPath);
        $filterFileRename = new Zend_Filter_File_Rename(
                array(
            'target' => $uploadPath . $newName,
            'overwrite' => true
        ));
        $filterFileRename->filter($file['tmp_name']);
        try {
            $upload->receive();
            return $newName;
        } catch (Zend_File_Transfer_Exception $e) {
            die('ERROR' . $e->getMessage());
        }
    }

    public function setCache($id, $input = NULL, $replace = TRUE, $lifetime = 7200) {
        $frontendOptions = array(
            'lifetime' => $lifetime, /*             * cache lifetime of 2 hours   */
            'automatic_serialization' => true);
        $backendOptions = array('cache_dir' => APPLICATION_PATH . '/public/cache/');
        /** getting a Zend_Cache_Core object */
        $cache = Zend_Cache::factory('Core', 'File', $frontendOptions, $backendOptions);
        $result = $cache->load($id);
        if ($result && $replace == FALSE) {
            return $result;
            $cache->end();
        } elseif ($replace == TRUE) {
            $cache->save($input, $id);
            $input = $cache->load($id);
            return $input;
        }
    }

    public function getCache($id) {
        $frontendOptions = array('lifetime' => 7200,
            'automatic_serialization' => true);
        $backendOptions = array(
            'cache_dir' => APPLICATION_PATH . '/public/cache/');
        /** getting a Zend_Cache_Core object */
        $cache = Zend_Cache::factory('Core', 'File', $frontendOptions, $backendOptions);
        $result = $cache->load($id);
        if ($result) {
            return $result;
            $cache->end();
        } elseif (!$result) {
            return array();
            $cache->end();
        }
    }

    public function cleanCache($id) {
        $frontendOptions = array('lifetime' => 7200, 'automatic_serialization' => true);
        $backendOptions = array('cache_dir' => APPLICATION_PATH . '/public/cache/');
        /** getting a Zend_Cache_Core object */
        $cache = Zend_Cache::factory('Core', 'File', $frontendOptions, $backendOptions);
        $cache->remove($id);
    }

    /*
     * employee cache
     * @param string,stirng,stirng,string
     * return 1 or 0
     */

    public static function setEmpcache($foldername, $filename, $data, $type) {

        $path = APPLICATION_PATH . "/employee" . '/' . $foldername;
        $fullpath = $path . '/' . $filename;
        if (!file_exists($path)) {
            mkdir($path, 0777, true);
        }
        $frontendOptions = array(
            'lifetime' => NULL, // cache lifetime of 2 hours
            'automatic_serialization' => true
        );
        $backendOptions = array(
            'cache_dir' => $path// Directory where to put the cache files
        );
        $cache = Zend_Cache::factory('Core', 'File', $frontendOptions, $backendOptions);
        return $cache->save($data, $filename);
    }

    /*
     * employee cache
     * @param string,stirng
     * return data arrya
     */

    public static function getEmpcache($foldername, $filename) {
        $path = APPLICATION_PATH . "/employee" . '/' . $foldername;
        $fullpath = $path . '/' . $filename;
        $frontendOptions = array(
            'lifetime' => NULL, // cache lifetime of 2 hours
            'automatic_serialization' => true
        );
        $backendOptions = array(
            'cache_dir' => $path// Directory where to put the cache files
        );
        $cache = Zend_Cache::factory('Core', 'File', $frontendOptions, $backendOptions);
        if (!($data = $cache->load($filename))) {
            // cache miss
            return '';
        } else {
            return $result = $cache->load($filename);
        }
    }

  /*  public function createExcel($heading, $data, $filename = "") {
		
        ini_set("memory_limit", "512M");
        ini_set("max_execution_time", 1800);
        error_reporting(0);
        chdir(APPLICATION_PATH . '/../library/PHPExcel');
        require_once( 'Classes/PHPExcel.php' );
        chdir('../../application');
        $objPHPExcel = new PHPExcel();
        header('Content-Type: application/vnd.ms-excel');
        $filename = $filename . date("dmYHis");
        header("Content-Disposition: attachment;filename=$filename.xls");
        header('Cache-Control: max-age=0');
        $col = 0;
        $row = 1;
        foreach ($heading as $head) {
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $head);
            $col++;
        }
        foreach (range(0, count($heading)) as $col) {
            $objPHPExcel->getActiveSheet()->getColumnDimensionByColumn($col)->setAutoSize(true);
        }
        $row++;

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
		
        $objWriter->save('php://output');
        exit;
    } */

	public function createExcel($heading, $exceldata, $filename = "") {
		
		ini_set("memory_limit", "512M");
        ini_set("max_execution_time", 1800);
        error_reporting(0);
        chdir(APPLICATION_PATH . '/../library/PHPExcel');
        require_once( 'Classes/PHPExcel.php' );
        chdir('../../application');
        $objPHPExcel = new PHPExcel();
        header('Content-Type: application/vnd.ms-excel');
        $filename = $filename . date("dmYHis");
        header("Content-Disposition: attachment;filename=$filename.xls");
        header('Cache-Control: max-age=0');
        $col = 0;
        $row = 1;
		
		foreach ($heading as $head) {
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $head);
            $col++;
        } 
		$row = 2;
		
		foreach ($exceldata as $value) { 
		
			$col = 0;
			if( !empty($value['admission_date']) ){
					$value['admission_date'] = date( DATE_PREFIX, strtotime($value['admission_date']));
			}

			if( !empty($value['followup_date']) ){
					$value['followup_date'] = date( DATE_PREFIX, strtotime($value['followup_date']));
			}
			
			foreach ($value as $values) { // print_r($value);die;
			//echo '<pre>';print_r($values);die;
			
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $values);
            $col++;
			}$row++;
        }
		foreach (range(0, count($heading)) as $col) {
            $objPHPExcel->getActiveSheet()->getColumnDimensionByColumn($col)->setAutoSize(true);
        }
        //$row++;
		
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
		
        $objWriter->save('php://output');
		
		
	/*	header("Content-Type: text/plain");

			$flag = false;
			foreach( $data as $value ) {
				if( !$flag ) {
				 //display field/column names as first row
				echo implode( "\t", array_keys( $value ) ) . "\r\n";
				$flag = true;
			}
			echo implode( "\t", array_values( $value ) ) . "\r\n";
		} */
		exit;
    }
    function upload($filename, $head) {

        ini_set("memory_limit", "512M");
        ini_set("max_execution_time", 1800);

        chdir(APPLICATION_PATH . '/../library/PHPExcel');
        require_once( 'Classes/PHPExcel.php' );
        chdir('../../application');

        $inputFileType = PHPExcel_IOFactory::identify($filename);
        $objReader = PHPExcel_IOFactory::createReader($inputFileType);

        $reader = $objReader->load($filename);
        $data = $reader->getSheet(0);

        $k = PHPExcel_Cell::columnIndexFromString($data->getHighestColumn());

//        for ($i = 1; $i <= $k; $i++) {
//            $head[] = str_replace(array('.', ','), '', trim($this->getCell($data, 0, $i)));
//        }
//        $head = array_filter($head);

        $records = $this->import($reader, $head);

//        echo '<pre>';
//        print_r($records);
//        exit;
        chdir('../../..');

        return $records;
    }

    public function import(&$reader, $head) {
        $data = $reader->getSheet(0);
        $users = array();
        $i = 0;
        $getHighestRow = $data->getHighestRow();
        $records = array();
        for ($i = 1; $i < $getHighestRow; $i++) {
            $loan_data = array();
            $cell = 1;
            foreach ($head as $k => $v) {
                $v = str_replace(array('.', ','), '', $v);
                if ($v) {
                    $loan_data[$v] = trim($this->getCell($data, $i, $cell));
                }
                $cell++;
            }
            if (count(array_filter($loan_data)) > 0) {
                $records[$i] = $loan_data;
            }
        }
        return $records;
    }

    function getCell(&$worksheet, $row, $col, $default_val = '') {
        $col -= 1; // we use 1-based, PHPExcel uses 0-based column index
        $row += 1; // we use 0-based, PHPExcel used 1-based row index
        //echo $col.'<br/>'.$row;
        $cell = $worksheet->getCellByColumnAndRow($col, $row);

        $return_cell_value = ($worksheet->cellExistsByColumnAndRow($col, $row)) ? $worksheet->getCellByColumnAndRow($col, $row)->getCalculatedValue() : $default_val;
        if (PHPExcel_Shared_Date::isDateTime($cell)) {
            $return_cell_value = date('Y-m-d', PHPExcel_Shared_Date::ExcelToPHP($return_cell_value));
        }
        return $return_cell_value;
    }



function create_zip($files = array(),$destination = '',$overwrite = false) {
	//if the zip file already exists and overwrite is false, return false
	
	//if(file_exists($destination) && !$overwrite) { return false; }
	//vars
	//print_r($files); die;
	$valid_files = array();
	//if files were passed in...
	if(is_array($files)) {
		//cycle through each file
		foreach($files as $file) {
		
			//make sure the file exists
			//if(file_exists($file)) {
				$valid_files[] = $file;
				//print_r($valid_files); die;
			//}
		}
	}

	//if we have good files...
	if(count($valid_files)) {
	
		//create the archive
		$zip = new ZipArchive();
		if($zip->open($destination,$overwrite ? ZIPARCHIVE::OVERWRITE : ZIPARCHIVE::CREATE) !== true) {
			return false;
		}
		//add the files
		foreach($valid_files as $file) {
			$zip->addFile($file,$file);
		}
		//debug
		//echo 'The zip archive contains ',$zip->numFiles,' files with a status of ',$zip->status;
		//print_r($zip);die;
		//close the zip -- done!
		return $zip;
		$zip->close();
		
		//check to make sure the file exists
		
		//return file_exists($destination);
	}
	else
	{
	
		return false;
	}
}
	//Enquiry Mail and Follow up
    function EnquirySendMail($data="", $type="") {

		//Get branch details
		//echo 'asdf';
		//print_r($data);die;
		$select_branch = $this->_db->select()
						->from('master_branch')
						->where("status !=?", 2)
						->where("branch_id =?", $data['branch_id']);
		$branch_data =	$this->getAdapter()
						->fetchRow($select_branch);	
		$address = $branch_data['branch_name'].'<br />'.$branch_data['branch_address'].'<br /> T:'.$branch_data['branch_phone_no1'].'<br /> M:'.$branch_data['branch_mobile_no'];
						
		//Get head office details
		$select_main = $this->_db->select()
						->from('master_main_branch')
						->where("status !=?", 2);
		$mainbranch_data =$this->getAdapter()
						->fetchRow($select_main);	
		//print_r($mainbranch_data); die;
		
        if($type==1)
		{		
			$select=$this->_db->select()
						->from('master_email')
						->where("status !=?", 2)
						->where("type =?", $type);
			$result=$this->getAdapter()
                      ->fetchRow($select);	
			$message = str_replace("XXXX", $data['parent_name'], $result['description']);			
			$message = str_replace("DATE", $data['followup_date'], $message);
			$message = str_replace("ADDRESS", $data['address'], $message);
			$message = str_replace("ENID", $data['enquiry_id'], $message);
if(!empty($data['father_email'])){
				///$email = explode(',',$data['father_email']);
$email = rtrim($data['father_email'],","); 
$father_email = explode(',',$email);
//print_r($father_email);die;
//$father = array($father_email);
}
if(!empty($data['mother_mail'])){
$mother= rtrim($data['mother_mail'],","); 
$mother_email  = explode(',',$mother);
//$mother = explode(',',$data['mother_mail']);
}
if(!empty($data['guardian_email'])){
			

			///$guardian = explode(',',$data['guardian_email']);

$guardian = rtrim($data['guardian_email'],","); 
$guardian_email = explode(',',$guardian);
}
$emails= array();
if(is_array($father_email))
    $emails= array_merge($emails, $father_email);
if(is_array($mother_email))
    $emails= array_merge($emails, $mother_email);
if(is_array($guardian_email))
    $emails= array_merge($emails, $guardian_email);


			//print_r($emails); die;	
			//$emails= array_merge($father_email,$mother_email,$guardian_email);

		}
		//Enquiry Follow up
		//print_r($data);die;
		if($type==2)
		{		
			$select=$this->_db->select()
						->from('master_email')
						->where("status !=?", 2)
						->where("type =?", $type);
			$result=$this->getAdapter()
                      ->fetchRow($select);	
			$message = str_replace("XXXX", $data['parent_name'], $result['description']);				
			$message = str_replace("DATE", $data['followup_date'], $message);
			$message = str_replace("ADDRESS", $data['address'], $message);
			//print_r($message);die;
			//$to = $data['email'];
//print_r($data['email']);die;
if(!empty($data['email'])){
				///$email = explode(',',$data['father_email']);
$email = rtrim($data['father_email'],","); 
//print_r($email);die;
$father_email = explode(',',$father_email);
//print_r($father_email);die;
//$father = array($father_email);
}
if(!empty($data['mother_mail'])){
$mother= rtrim($data['mother_mail'],","); 
$mother_email  = explode(',',$mother);
//$mother = explode(',',$data['mother_mail']);
}
if(!empty($data['guardian_email'])){
			

			///$guardian = explode(',',$data['guardian_email']);

$guardian = rtrim($data['guardian_email'],","); 
$guardian_email = explode(',',$guardian);
}
$emails= array();
if(is_array($father_email))
    $emails= array_merge($emails, $father_email);
if(is_array($mother_email))
    $emails= array_merge($emails, $mother_email);
if(is_array($guardian_email))
    $emails= array_merge($emails, $guardian_email);

			//print_r($emails); die;	
			//$emails= array_merge($father_email,$mother_email,$guardian_email);
		}
		//print_r($result);die;
		//$message = 'you are registered successfully';
		$img_path = "http://eddeerp.com/public/";		
		/* $html_content ='<!doctype html>
					<html>
					<head>
					<meta charset="utf-8">
					<title>Esperanza</title>
					<style>
					a{color:#fff;}
					</style>
					</head>
					<body style="margin:0px; padding:0px; font-family:Arial, Helvetica, sans-serif;">
					<div style="width:900px; margin:0px auto; display:block; overflow:hidden;  height:auto; border:1px solid #dcc; box-sizing:border-box;">
					<header style="display:block; overflow:hidden;  height:auto; ">
					<img src="'.$img_path.'mail_img/header-logo.jpg" style="float:right; clear:both; margin-right:50px;">
					</header>
					<div class="left-logo" style="width:200px; height:auto; float:left; border-right:1px solid #ccc; text-align:center;"> 
					<img src="'.$img_path.'mail_img/left-logo.jpg">
					<p style=" font-size:12px; padding:5px; text-align:left; font-weight:300;">
					<h3 style="  font-size:12px; padding:0px 5px; text-align:left; font-weight:300;color:#008981;"><strong>'.$branch_data['branch_name'].'</strong></h3>
					<p style=" font-size:12px; padding:0px 5px;text-align:left; font-weight:300;">'.nl2br( $branch_data['branch_address'] ).'<br/>T: '.$branch_data['branch_phone_no1'].' | '.$branch_data['branch_phone_no2'].'<br/>
					M: '.$branch_data['branch_mobile_no'].' | '.$branch_data['branch_contact_person_phone'].'<br/>
					<span style="font-size:9px; color:#f00;">franchisesupport@esperanzacorporate.com</span></p>
					<h3 style="font-size:12px; padding:0px 5px;text-align:left; font-weight:300;color:#008981;"><strong>'.$mainbranch_data['main_branch_name'].'</strong></h3>
					<p style=" font-size:12px; padding:0px 5px; text-align:left; font-weight:300;">'.nl2br( $mainbranch_data['address'] ).'<br/>T: '.$mainbranch_data['phone_no1'].' | '.$mainbranch_data['phone_no2'].'<br/>
					<span style="font-size:9px; color:#f00;">franchisesupport@esperanzacorporate.com</span></p>
					</p>
					</div>
  
					<div class="content" style=" display:block; overflow:hidden;  height:auto; padding:15px; width:667px; box-sizing:border-box;">
					<div style="height:886px; width:610px; float:left;">'.$message.'</div>
					<br> 					
					<div style="width:47px; float:right; margin-top:350px;  height:222px; display:block; overflow:hidden;"><a href="'.$mainbranch_data['website'].'"><img src="'.$img_path.'mail_img/website-lnk.jpg" width="47"></a></div>
					</div>
					<div style="display:block; overflow:hidden;  height:auto; box-sizing:border-box;top:0px;  position:absolute; margin-left:230px;">
					<img src="'.$img_path.'mail_img/footer.jpg">
					</div>
					</div>
					</body>
					</html>'; */
			//new code start
		//print_r($to);die;
		//$to = $data['email'];
$brn_nm = strtolower($branch_data['branch_name']);

$zipped_file = $brn_nm.'enquiry.zip';

foreach($emails as $k => $to){

		$url = 'https://api.sendgrid.com/';
		$user = 'chandravasireddi';
		$pass = 'tissot123';
		
		$json_string = array(
		
		  'to' => array(
		   $to
		  ),
		  'category' => 'test_category'
		);
		
		//$filePath = $_SERVER['DOCUMENT_ROOT'].'/'.'public';


		//$fileName ="doc-att.zip";


		//$filePath = dirname(__FILE__);
		$params = array(
		    'api_user'  => $user,
		    'api_key'   => $pass,
		    'x-smtpapi' => json_encode($json_string),
		    'to'        => $to,
		    'subject'   => $result['subject'],
		    'html'      => $message,
		    'text'      => 'testing body',
		    'from'      => $branch_data['primary_email_address'],
		   //'files['.$fileName.']' => '@'.$filePath.'/'.$fileName
		  );
		//print_r($params);die;
		
		$request =  $url.'api/mail.send.json';
//print_r($request);

		// Generate curl request
		$session = curl_init($request);
		//print_r($session);
		// Tell curl to use HTTP POST
		curl_setopt ($session, CURLOPT_POST, true);
		
		// Tell curl that this is the body of the POST
		curl_setopt ($session, CURLOPT_POSTFIELDS, $params);

		
		// Tell curl not to return headers, but do return the response
		curl_setopt($session, CURLOPT_HEADER, false);
		// Tell PHP not to use SSLv3 (instead opting for TLS)
		curl_setopt($session, CURLOPT_SSLVERSION, CURL_SSLVERSION_TLSv1_2);
		curl_setopt($session, CURLOPT_RETURNTRANSFER, true);
		
		// obtain response
		$response = curl_exec($session);
//print_r($response );die;
//print_r($session);

		curl_close($session);
}
    }
	
	
	function sendMail($data="", $type="") {
	//print_r($data);die;
        if($type == 3)// successfully enrol mail
		{	
			$login_link = "http://eddeerp.com/student/login";
			$dar = "http://eddeerp.com/student/dar/uniqueid/".$data['unique_id']."/id/".$data['admission_id'];
			$live = "http://eddeerp.com/student/live-update/uniqueid/".$data['unique_id']."/id/".$data['admission_id'];
			$select=$this->_db->select()
						->from('master_email')
						->where("status !=?", 2)
						->where("type =?", $type);
			$result=$this->getAdapter()
                      ->fetchRow($select);	
			$message = str_replace("XXXX", $data['parent_name'], $result['description']); 
			$message = str_replace("ADDRESS", $data['address'], $message); 
			$message = str_replace("BRANCH NAME", $data['branch_name'], $message);
			$message = str_replace("CHILDNAME", $data['child_name'], $message);
			$message = str_replace("NUMBER", $data['admission_number'], $message);
			$message = str_replace("LOGINLINK", $login_link, $message);
			$message = str_replace("PASSWORD", $data['password'], $message);
			$message = str_replace("EMAILID", $data['username'], $message);
			$message = str_replace("DARLINK", $dar, $message);
			$message = str_replace("LIVEUPDATELINK", $live, $message);
			//$email = explode(',',$data['father_email']);
			//$mother = explode(',',$data['mother_email']);
			//$guardian = explode(',',$data['guardian_email']);
				//$to = array_merge($email,$mother,$guardian);
if(!empty($data['father_email'])){
				///$email = explode(',',$data['father_email']);
$email = rtrim($data['father_email'],","); 
$father_email = explode(',',$email);
}if(!empty($data['mother_email'])){
$mother= rtrim($data['mother_email'],","); 
$mother_email = explode(',',$mother);
}
if(!empty($data['guardian_email'])){
			

			///$guardian = explode(',',$data['guardian_email']);

$guardian = rtrim($data['guardian_email'],","); 
$guardian_email = explode(',',$guardian);
}

				
			$emails= array();
if(is_array($father_email))
    $emails= array_merge($emails, $father_email);
if(is_array($mother_email))
    $emails= array_merge($emails, $mother_email);
if(is_array($guardian_email))
 $emails= array_merge($emails, $guardian_email);
		}
		
	/*	if($type == 4)
		{		
			$select=$this->_db->select()
						->from('master_email')
						->where("status !=?", 2)
						->where("type =?", $type);
			$result=$this->getAdapter()
                      ->fetchRow($select);	
			$message = str_replace("XXXX", $data['parent_name'], $result['description']); 
			$message = str_replace("ADDRESS", $data['address'], $result['description']); 
		}
		*/
$brn_nm = strtolower($data['branch_name']);
		$zipped_file = $brn_nm.'admission.zip';
//print_r($emails);die;
foreach($emails as $k=>$to){
		$url = 'https://api.sendgrid.com/';
		$user = 'chandravasireddi';
		$pass = 'tissot123';
		
		$json_string = array(
		
		  'to' => array(
		    $to
		  ),
		  'category' => 'test_category'
		);
		
		//$filePath = $_SERVER['DOCUMENT_ROOT'].'/'.'public';
		//$fileName = $zipped_file;
		//$filePath = dirname(__FILE__);
		$params = array(
		    'api_user'  => $user,
		    'api_key'   => $pass,
		    'x-smtpapi' => json_encode($json_string),
		    'to'        => $to,
		    'subject'   => $result['subject'],
		    'html'      => $message,
		    'text'      => 'testing body',
		    'from'      => $data['branch_email'],
		    //'files['.$fileName.']' => '@'.$filePath.'/'.$fileName
		  );
		//print_r($params);
		
		$request =  $url.'api/mail.send.json';

		// Generate curl request
		$session = curl_init($request);
		
		// Tell curl to use HTTP POST
		curl_setopt ($session, CURLOPT_POST, true);
		
		// Tell curl that this is the body of the POST
		curl_setopt ($session, CURLOPT_POSTFIELDS, $params);
		
		// Tell curl not to return headers, but do return the response
		curl_setopt($session, CURLOPT_HEADER, false);
		// Tell PHP not to use SSLv3 (instead opting for TLS)
		curl_setopt($session, CURLOPT_SSLVERSION, CURL_SSLVERSION_TLSv1_2);
		curl_setopt($session, CURLOPT_RETURNTRANSFER, true);
		
		// obtain response
		$response = curl_exec($session);
		curl_close($session);
}
		
	
    }
	
	function sendSMS($data="", $type="") {
		//print_r($data);die;
        if($type == 1)
		{		
			$select=$this->_db->select()
						->from('master_sms')
						->where("sms_type =?", $type);
						//->where("type =?", $type);
			$result=$this->getAdapter()
                      ->fetchRow($select);
			$message = str_replace("XXXX", $data['parent_name'], $result['description']);
			$message = str_replace("CAPTURE ID", $data['enquiry_id'], $message);
		}
		
		if($type == 2)
		{	
			$date = $newDate = date("d-m-Y", strtotime($data['followup_date']));
			$select=$this->_db->select()
						->from('master_sms')
						->where("sms_type =?", $type);
						//->where("type =?", $type);
			$result=$this->getAdapter()
                      ->fetchRow($select);
			$message = str_replace("XXXX", $data['parent_name'], $result['description']);
			$message = str_replace("DD-MM-YY", $date, $message);
		} 
		//echo $message; die;
		if($type == 3)
		{		
			$select=$this->_db->select()
						->from('master_sms')
						->where("sms_type =?", $type);
						//->where("type =?", $type);
			$result=$this->getAdapter()
                      ->fetchRow($select);
			$message = str_replace("XXXX", $data['parent_name'], $result['description']);
			$message = str_replace("BRANCHNAME", $data['branch_name'], $message);
			$message = str_replace("CHILDNAME", $data['child_name'], $message);
			//$message = str_replace("NUMBER", $data['admission_number'], $message);
		}
		$father_mobile = '';
		$mother_mobile = '';
		$guardian_mobile = '';
			if(!empty($data['father_mobile'])){
				$father_mobile = rtrim($data['father_mobile'],","); 
			}
			if(!empty($data['mother_mobile'])){
				$mother_mobile = rtrim($data['mother_mobile'],","); 
			}
			if(!empty($data['guardian_mobile'])){
			//echo 'dsd' ; die;
				$guardian_mobile = rtrim($data['guardian_mobile'],","); 
				//echo $guardian_mobile; die;
			} 	
			$mobile = array($father_mobile,$mother_mobile,$guardian_mobile);
			$mb = implode(',',$mobile);
			$m = rtrim($mb,",");

		$url = 'http://174.143.34.193/SendSMS/BulkSMS.aspx?';
		$data = "usr=chandra@esperanzacorporate.com&pass=9966000011&msisdn=".$m."&msg=".$message."&sid=ESPRNZ&mt=0";

		$ch = curl_init();
		
		curl_setopt($ch, CURLOPT_URL, $url);
		//curl_setopt($ch, CURLOPT_POST, count($kv));
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		
		curl_setopt($ch, CURLOPT_HEADER, FALSE);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, FALSE);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
		
		$result = curl_exec($ch);
		
		curl_close($ch);
		
		
		
	/*	$url = 'http://174.143.34.193/SendSMS/BulkSMS.aspx?';
		$data = "usr=chandra@esperanzacorporate.com&pass=9966000011&msisdn=".$data['father_mobile'].','.$data['mother_mobile'].','.$data['branch_mobile']."&msg=".$message."&sid=ESPRNZ&mt=0";
		
		$ch = curl_init();
		
		curl_setopt($ch, CURLOPT_URL, $url);
		//curl_setopt($ch, CURLOPT_POST, count($kv));
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		
		curl_setopt($ch, CURLOPT_HEADER, FALSE);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, FALSE);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
		
		$result = curl_exec($ch);
		
		curl_close($ch); */
		//print_r($data);die;
    }
	//Upcoming Birthdays Inform to Branch  
	function sendBirthdayMail($data="", $type="",$value="") {
	
        $select_branch = $this->_db->select()
						->from('master_branch')
						->where("status !=?", 2)
						->where("branch_id =?", $data['branch_id']);
		$branch_data =	$this->getAdapter()
						->fetchRow($select_branch);	
		$address = $branch_data['branch_name'].'<br />'.$branch_data['branch_address'].'<br /> T:'.$branch_data['branch_phone_no1'].'<br /> M:'.$branch_data['branch_mobile_no'];
		//print_r($address);die;
		if($type == 6)
		{	//echo '6';die;
			$select=$this->_db->select()
						->from('master_email')
						->where("status !=?", 2)
						->where("type =?", $type);
			$result=$this->getAdapter()
                      ->fetchRow($select);	
			$message = str_replace("XXXX", $data['fatherName'], $result['description']); 
			$message = str_replace("CHILDNAME", $data['child_first_name'], $message);
			$message = str_replace("Respective Branch Address", $address, $message);
			$message = ($message."Are you received the gifts");
			$message = ($message.$data['link']);
			//$message = ($message."<a href=\"index/type/yes\">Yes</a>/ <a href=\"index/type/no\">No</a>");
		//echo $message; die;
			//$message = ("Are you received the gifts". $message);
			$to = $data['father_email'];
	
		}
		//echo $message; die;
		if($type == 7)
		{	
			$select=$this->_db->select()
						->from('master_email')
						->where("status !=?", 2)
						->where("type =?", $type);
			$result=$this->getAdapter()
                      ->fetchRow($select);	
			$message = str_replace("XXXX", $data['vendor_email'], $result['description']); 
			//$message = str_replace("ADDRESS", $data['local_address'], $message); 
			$message = str_replace("BRANCH NAME", $data['branch_name'], $message);
			$message = str_replace("Respective Branch Address & Contact Details", $address, $message);
			$message = str_replace("Child name-Class-Home address-Mobile Number", $value, $message);
			//echo $message; die;	
			$to = $data['vendor_email'];
		}
		//echo $to; die;
		//echo $message; die;
		//$message = 'you are registered successfully';
		//$to = $data['branch_email'];
		if($type == 7)
		{	
			$select=$this->_db->select()
						->from('master_email')
						->where("status !=?", 2)
						->where("type =?", $type);
			$result=$this->getAdapter()
                      ->fetchRow($select);	
			$message = str_replace("XXXX", $data['branch_contact_person'], $result['description']); 
			//$message = str_replace("ADDRESS", $data['local_address'], $message); 
			$message = str_replace("BRANCH NAME", $data['branch_name'], $message);
			$message = str_replace("Respective Branch Address & Contact Details", $address, $message);
			$message = str_replace("Child name-Class-Home address-Mobile Number", $value, $message);			
			$to = $data['branch_email'];
		}
		//$to = 'tisrajkumar@gmail.com';
		$url = 'https://api.sendgrid.com/';
		$user = 'chandravasireddi';
		$pass = 'tissot123';
		
		$json_string = array(
		
		  'to' => array(
		    $to//$data['email']
		  ),
		  'category' => 'test_category'
		);
		
		//$filePath = $_SERVER['DOCUMENT_ROOT'].'/'.'public';
		//$fileName = "doc-att.zip";
		//$filePath = dirname(__FILE__);
		$params = array(
		    'api_user'  => $user,
		    'api_key'   => $pass,
		    'x-smtpapi' => json_encode($json_string),
		    'to'        => $to,//'tisrajkumar@gmail.com',
		    'subject'   => $result['subject'],
		    'html'      => $message,
		    'text'      => 'testing body',
		    'from'      => $data['branch_email']
		    //'files['.$fileName.']' => '@'.$filePath.'/'.$fileName
		  );
		//print_r($params);
		
		$request =  $url.'api/mail.send.json';

		// Generate curl request
		$session = curl_init($request);
		
		// Tell curl to use HTTP POST
		curl_setopt ($session, CURLOPT_POST, true);
		
		// Tell curl that this is the body of the POST
		curl_setopt ($session, CURLOPT_POSTFIELDS, $params);
		
		// Tell curl not to return headers, but do return the response
		curl_setopt($session, CURLOPT_HEADER, false);
		// Tell PHP not to use SSLv3 (instead opting for TLS)
		curl_setopt($session, CURLOPT_SSLVERSION, CURL_SSLVERSION_TLSv1_2);
		curl_setopt($session, CURLOPT_RETURNTRANSFER, true);
		
		// obtain response
		$response = curl_exec($session);
		curl_close($session);
		//die;
	/*	$config = array('auth' => 'login',
                'username' => 'chandravasireddi',
                'password' => 'tissot123'); 
		$transport = new Zend_Mail_Transport_Smtp('smtp.sendgrid.net', $config);
        $mail = new Zend_Mail();		
        $mail->setBodyHtml($message);
        $mail->setFrom('espiranza@gmail.com', 'Espiranza');
        $mail->addTo($to);
		//$mail->addCc($data['branch_email']);
		//$mail->addBcc($to);
        $mail->setSubject('Hello');	
        $mailSend_status = $mail->send($transport); */
    }
	
	//birthday sms
	function sendBirthdaySMS($data="", $type="") {
		
		//echo $message; die;
		if($type == 6)
		{		
			$select=$this->_db->select()
						->from('master_sms')
						->where("sms_type =?", $type);
						//->where("type =?", $type);
			$result=$this->getAdapter()
                      ->fetchRow($select);
			$message = str_replace("CHILDNAME", $data['child_first_name'], $result['description']);
			
		
			//$message = str_replace("BRANCHNAME", $data['branch_name'], $message);
			//$message = str_replace("CHILDNAME", $data['child_name'], $message);
			//$message = str_replace("NUMBER", $data['admission_number'], $message);
		}
		//echo $message; die;
		//$message = str_replace("XXXX", $data['parent_name'], $result['description']);
		$url = 'http://174.143.34.193/SendSMS/BulkSMS.aspx?';
		$data = "usr=chandra@esperanzacorporate.com&pass=9966000011&msisdn=".$data['father_mobile_no']."&msg=".$message."&sid=ESPRNZ&mt=0";

		$ch = curl_init();
		
		curl_setopt($ch, CURLOPT_URL, $url);
		//curl_setopt($ch, CURLOPT_POST, count($kv));
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		
		curl_setopt($ch, CURLOPT_HEADER, FALSE);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, FALSE);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
		
		$result = curl_exec($ch);
		
		curl_close($ch);
		
    }
	
	function sendQuotationMail($data="",$feesType="", $type="") { //echo '<pre>';print_r($feesType);die;

        if($type==1)
		{		
			$select=$this->_db->select()
						->from('master_email')
						->where("status !=?", 2)
						->where("type =?", $type);
			$result=$this->getAdapter()
                      ->fetchRow($select);			
		}
		
		$message = str_replace("XXXX", $data['parent_name'],$feesType['type_name'],$feesType['monthly_fees'],$feesType['term_fees'],$feesType['yearly_fees'], $result['description']); 		
		//$message = 'you are registered successfully';
		$to = $data['email_id'].','.$data['alternate_email'];
        $mail = new Zend_Mail();
        $mail->setBodyHtml($message);
        $mail->setFrom('mywebsite@gmail.com', 'Admin');
        $mail->addTo($to);
        $mail->setSubject('Hello');	
        $mailSend_status = $mail->send();
    }
	
	
	function perentSendMail($data="", $type="") {
	//print_r($data);die;
      
		if($type==0) //send mail to referred  parent 
		{		
			/* $select=$this->_db->select()
						->from('master_email')
						->where("status !=?", 2)
						->where("type =?", $type);
			$result=$this->getAdapter()
                      ->fetchRow($select); */	
			//$message = str_replace("XXXX", $data['parent_name'], $result['description']);				
			//$message = str_replace(" First Name & Surname", $data['admission_parent_name'], $message);
			$message .= "Dear Parent,<br />";
$message .= "You will get Rs. 2000 Discount.";
			//$message = ("Hi,  check this out <a href=\"http://www.website.com\">click here</a>");
			//$email = explode(',',$data['father_email']);
			//$mother = explode(',',$data['mother_email']);
			//$guardian = explode(',',$data['guardian_email']);
				//$to = array_merge($email,$mother,$guardian);
  if(!empty($data['father_email'])){
				///$email = explode(',',$data['father_email']);
$email = rtrim($data['father_email'],","); 
}if(!empty($data['father_email'])){
				///$email = explode(',',$data['father_email']);
$email = rtrim($data['father_email'],","); 
}
if(!empty($data['mother_email'])){
$mother= rtrim($data['mother_email'],","); 
//$mother = explode(',',$data['mother_email']);
}
if(!empty($data['guardian_email'])){
			

			///$guardian = explode(',',$data['guardian_email']);

$guardian = rtrim($data['guardian_email'],","); 
}

				
			$emails= array($email ,$mother,$guardian);


		}
		  if($type==5)
		{		
			$select=$this->_db->select()
						->from('master_email')
						->where("status !=?", 2)
						->where("type =?", $type);
			$result=$this->getAdapter()
                      ->fetchRow($select);
			//$message = "Hi,  check this <a href=\"http://eddeerp.com\">Yes</a>/ <a href=\"http://eddeerp.com\">No</a>" ;			
			$message = str_replace("XXXX", $data['parent_name'], $result['description'], $message);				
			$message = str_replace("First Name", $data['admission_parent_name'], $message);
			$message = str_replace("Surname", $data['surname'], $message);
			$message = str_replace("BRANCH NAME", $data['branch_name'], $message);
			
			//$message = ($data['enquiry_id']. $message);
			//$to = $data['parentEmail'];
if(!empty($data['parentEmail'])){
		$parent_email = implode(',',$data['parentEmail']);
$email = rtrim($parent_email,","); 
}

$emails = $email;
		}
		if($type==-1) //send mail to refer  parent 
		{		
			/* $select=$this->_db->select()
						->from('master_email')
						->where("status !=?", 2)
						->where("type =?", $type);
			$result=$this->getAdapter()
                      ->fetchRow($select); */
			if(!empty($data['father_last_name'])){
				$refereeParentName = $data['father_last_name']; 
			}
			if(!empty($data['mother_last_name'])){
				$refereeParentName = $data['mother_last_name']; 
			}
			if(!empty($data['guardian_last_name'])){
				$refereeParentName = $data['guardian_last_name']; 
			}
			if(!empty($data['admission_father_name'])){
				$admissionParentName = $data['admission_father_name']; 
			}
			if(!empty($data['admission_mother_name'])){
				$admissionParentName = $data['admission_mother_name']; 
			}
			if(!empty($data['admission_guardian_name'])){
				$admissionParentName = $data['admission_guardian_name']; 
			}

			if(!empty($data['father_email'])){
				$referyEmail = $data['father_email']; 
			}elseif(!empty($data['mother_email'])){
				$referyEmail = $data['mother_email']; 
			}elseif(!empty($data['guardian_email'])){
				$referyEmail = $data['guardian_email']; 
			}	
			/*	$select=$this->_db->select()
						->from('master_email')
						->where("status !=?", 2)
						->where("type =?", $type);
			$result=$this->getAdapter()
                      ->fetchRow($select); */
			
			$message = str_replace("XXXX", $refereeParentName, 'Dear Mr/Ms XXXX ,
			Warm Greetings! Thank you for successfully referring Mr/Ms Surname to Esperanza BRANCH NAME ');
			$message = str_replace("Surname", $admissionParentName, $message);
			$message = str_replace("BRANCH NAME", $data['branch'], $message);			
			
			$to = $referyEmail;
		}
		//$message = 'you are registered successfully';
		$subject = '';
		if($result['subject'] != null){
			$subject = $result['subject'];
		}else{
			$subject = 'Esperanza Update';
		}
foreach($emails as $k=>$to){
		$url = 'https://api.sendgrid.com/';
		$user = 'chandravasireddi';
		$pass = 'tissot123';
		
		$json_string = array(
		
		  'to' => array(
		    $to//$data['email']
		  ),
		  'category' => 'test_category'
		);
		
		//$filePath = $_SERVER['DOCUMENT_ROOT'].'/'.'public';
		//$fileName = "doc-att.zip";
		//$filePath = dirname(__FILE__);
		$params = array(
		    'api_user'  => $user,
		    'api_key'   => $pass,
		    'x-smtpapi' => json_encode($json_string),
		    'to'        => $to,//'tisrajkumar@gmail.com',
		    'subject'   => $subject,
		    'html'      => $message,
		    'text'      => 'testing body',
		    'from'      => $data['branch_email']
		    //'files['.$fileName.']' => '@'.$filePath.'/'.$fileName
		  );
		//print_r($params);
		
		$request =  $url.'api/mail.send.json';

		// Generate curl request
		$session = curl_init($request);
		
		// Tell curl to use HTTP POST
		curl_setopt ($session, CURLOPT_POST, true);
		
		// Tell curl that this is the body of the POST
		curl_setopt ($session, CURLOPT_POSTFIELDS, $params);
		
		// Tell curl not to return headers, but do return the response
		curl_setopt($session, CURLOPT_HEADER, false);
		// Tell PHP not to use SSLv3 (instead opting for TLS)
		curl_setopt($session, CURLOPT_SSLVERSION, CURL_SSLVERSION_TLSv1_2);
		curl_setopt($session, CURLOPT_RETURNTRANSFER, true);
		
		// obtain response
		$response = curl_exec($session);
		curl_close($session);
		//die;
}
    }
	
	function perentDiscountSendMail($data="", $type="") {
	//print_r($data);die;
      
		if($type==0) //send mail to referred  parent 
		{		
			/* $select=$this->_db->select()
						->from('master_email')
						->where("status !=?", 2)
						->where("type =?", $type);
			$result=$this->getAdapter()
                      ->fetchRow($select); */	
			//$message = str_replace("XXXX", $data['parent_name'], $result['description']);				
			//$message = str_replace(" First Name & Surname", $data['admission_parent_name'], $message);
			$message = ("Dear Parent You Will Get 2000 Rs Discount");
			//$message = ("Hi,  check this out <a href=\"http://www.website.com\">click here</a>");
			$to = $data['email'];
		}
		 
		//$message = 'you are registered successfully';
		$subject = '';
		if($result['subject'] != null){
			$subject = $result['subject'];
		}else{
			$subject = 'Esperanza Update';
		}
		$url = 'https://api.sendgrid.com/';
		$user = 'chandravasireddi';
		$pass = 'tissot123';
		
		$json_string = array(
		
		  'to' => array(
		    $to//$data['email']
		  ),
		  'category' => 'test_category'
		);
		
		//$filePath = $_SERVER['DOCUMENT_ROOT'].'/'.'public';
		//$fileName = "doc-att.zip";
		//$filePath = dirname(__FILE__);
		$params = array(
		    'api_user'  => $user,
		    'api_key'   => $pass,
		    'x-smtpapi' => json_encode($json_string),
		    'to'        => $to,//'tisrajkumar@gmail.com',
		    'subject'   => $subject,
		    'html'      => $message,
		    'text'      => 'testing body',
		    'from'      => $data['branch_email']
		    //'files['.$fileName.']' => '@'.$filePath.'/'.$fileName
		  );
		//print_r($params);
		
		$request =  $url.'api/mail.send.json';

		// Generate curl request
		$session = curl_init($request);
		
		// Tell curl to use HTTP POST
		curl_setopt ($session, CURLOPT_POST, true);
		
		// Tell curl that this is the body of the POST
		curl_setopt ($session, CURLOPT_POSTFIELDS, $params);
		
		// Tell curl not to return headers, but do return the response
		curl_setopt($session, CURLOPT_HEADER, false);
		// Tell PHP not to use SSLv3 (instead opting for TLS)
		curl_setopt($session, CURLOPT_SSLVERSION, CURL_SSLVERSION_TLSv1_2);
		curl_setopt($session, CURLOPT_RETURNTRANSFER, true);
		
		// obtain response
		$response = curl_exec($session);
		curl_close($session);
		//die;
    }
	
	//fee payment
	function feePaymentMail($data="", $type="") {
	
		$select_branch = $this->_db->select()
						->from('master_branch')
						->where("status !=?", 2)
						->where("branch_id =?", $data['branch_id']);
		$branch_data =	$this->getAdapter()
						->fetchRow($select_branch);	
		$address = $branch_data['branch_name'].'<br />'.$branch_data['branch_address'].'<br /> T:'.$branch_data['branch_phone_no1'].'<br /> M:'.$branch_data['branch_mobile_no'];
		
        if($type==18)
		{		
			$select=$this->_db->select()
						->from('master_email')
						->where("status !=?", 2)
						->where("type =?", $type);
			$result=$this->getAdapter()
                      ->fetchRow($select);			
			$message = str_replace("XXXX", $data['parent_name'], $result['description']);				
			$message = str_replace("CAPTURE AMOUNT", $data['paid_amount'], $message);
                        $message = str_replace("PAYMENT METHOD", $data['mode_of_payment'], $message);
			$message = str_replace("DD/MM/YY", $data['todayDate'], $message);
			$message = str_replace("HH:MM:SS", $data['currentTime'], $message);
			$message = str_replace("CHILD NAME", $data['child_name'], $message);
			$message = str_replace("CAPTURE ANY DUE", $data['balance_due'], $message);
			$message = str_replace("Respective Branch Address",$address, $message);

			//$to = $data['email'];
if(!empty($data['father_email'])){
				///$email = explode(',',$data['father_email']);
$father = rtrim($data['father_email'],","); 
$father_email = explode(',',$father);
//$father = array($father_email);
}
if(!empty($data['mother_mail'])){
$mother= rtrim($data['mother_mail'],","); 
$mother_email  = explode(',',$mother);
//$mother = explode(',',$data['mother_mail']);
}
if(!empty($data['guardian_email'])){
			

			///$guardian = explode(',',$data['guardian_email']);

$guardian = rtrim($data['guardian_email'],","); 
$guardian_email = explode(',',$guardian);
}
$emails= array();
if(is_array($father_email))
    $emails= array_merge($emails, $father_email);
if(is_array($mother_email))
    $emails= array_merge($emails, $mother_email);
if(is_array($guardian_email))
    $emails= array_merge($emails, $guardian_email);

		}
		
		//$message = 'you are registered successfully';
		foreach($emails as $k=>$to){
		$url = 'https://api.sendgrid.com/';
		$user = 'chandravasireddi';
		$pass = 'tissot123';
		
		$json_string = array(
		
		  'to' => array(
		    $to//$data['email']
		  ),
		  'category' => 'test_category'
		);
		
		//$filePath = $_SERVER['DOCUMENT_ROOT'].'/'.'public';
		//$fileName = "doc-att.zip";
		//$filePath = dirname(__FILE__);
		$params = array(
		    'api_user'  => $user,
		    'api_key'   => $pass,
		    'x-smtpapi' => json_encode($json_string),
		    'to'        => $to,
		    'subject'   => 'Esperanza Update',
		    'html'      => $message,
		    'text'      => 'testing body',
		    'from'      => $data['branch_email']
		    //'files['.$fileName.']' => '@'.$filePath.'/'.$fileName
		  );
		//print_r($params);
		
		$request =  $url.'api/mail.send.json';

		// Generate curl request
		$session = curl_init($request);
		
		// Tell curl to use HTTP POST
		curl_setopt ($session, CURLOPT_POST, true);
		
		// Tell curl that this is the body of the POST
		curl_setopt ($session, CURLOPT_POSTFIELDS, $params);
		
		// Tell curl not to return headers, but do return the response
		curl_setopt($session, CURLOPT_HEADER, false);
		// Tell PHP not to use SSLv3 (instead opting for TLS)
		curl_setopt($session, CURLOPT_SSLVERSION, CURL_SSLVERSION_TLSv1_2);
		curl_setopt($session, CURLOPT_RETURNTRANSFER, true);
		
		// obtain response
		$response = curl_exec($session);
		curl_close($session);
		//die;
}
    }
	
	function perentsendSMS($data="", $type="") {
		
		//echo $message; die;
		if($type == -1)
		{		
			if(!empty($data['father_last_name'])){
				$refereeParentName = $data['father_last_name']; 
			}
			if(!empty($data['mother_last_name'])){
				$refereeParentName = $data['mother_last_name']; 
			}
			if(!empty($data['guardian_last_name'])){
				$refereeParentName = $data['guardian_last_name']; 
			}
			if(!empty($data['admission_father_name'])){
				$admissionParentName = $data['admission_father_name']; 
			}
			if(!empty($data['admission_mother_name'])){
				$admissionParentName = $data['admission_mother_name']; 
			}
			if(!empty($data['admission_guardian_name'])){
				$admissionParentName = $data['admission_guardian_name']; 
			}
			if(!empty($data['father_mobile_no'])){
				$refereeMobileNo = $data['father_mobile_no']; 
			}
			if(!empty($data['mother_mobile_no'])){
				$refereeMobileNo = $data['mother_mobile_no']; 
			}
			if(!empty($data['guardian_mobile_no'])){
				$refereeMobileNo = $data['guardian_mobile_no']; 
			}
			
			$message = str_replace("XXXX", $refereeParentName, 'Dear Mr/Ms XXXX ,
			Warm Greetings! Thank you for successfully referring Mr/Ms Surname to Esperanza BRANCH NAME ');
			$message = str_replace("Surname", $admissionParentName, $message);
			$message = str_replace("BRANCH NAME", $data['branch'], $message);
		}
		
		if($type == 5)
		{		
			$select=$this->_db->select()
						->from('master_sms')
						->where("sms_type =?", $type);
						//->where("type =?", $type);
			$result=$this->getAdapter()
                      ->fetchRow($select);
			$message = str_replace("XXXX", $data['parent_name'], $result['description']);
			$message = str_replace("First Name", $data['admission_parent_first_name'], $message);
			$message = str_replace("Surname", $data['admission_parent_name'], $message);
			//$message = str_replace("NUMBER", $data['admission_number'], $message);
			
			//$refereeMobileNo = $data['parentMobile'];
if(!empty($data['parentMobile'])){
				$refereeMobileNo = rtrim($data['parentMobile'],","); 
			}
$mobile = array($refereeMobileNo);
			$mb = implode(',',$mobile);
			$m = rtrim($mb,",");

		}
		//echo $message; die;
		//$message = str_replace("XXXX", $data['parent_name'], $result['description']);
		$url = 'http://174.143.34.193/SendSMS/BulkSMS.aspx?';
		$data = "usr=chandra@esperanzacorporate.com&pass=9966000011&msisdn=".$m."&msg=".$message."&sid=ESPRNZ&mt=0";

		$ch = curl_init();
		
		curl_setopt($ch, CURLOPT_URL, $url);
		//curl_setopt($ch, CURLOPT_POST, count($kv));
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		
		curl_setopt($ch, CURLOPT_HEADER, FALSE);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, FALSE);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
		
		$result = curl_exec($ch);
		
		curl_close($ch);
		//echo $data.'---'.$result;die;
    }
	
	//send mail to admission  parent 
	 function admissionPerentSendMail($data="", $type="") {

        if($type==5)
		{		
			$select=$this->_db->select()
						->from('master_email')
						->where("status !=?", 2)
						->where("type =?", $type);
			$result=$this->getAdapter()
                      ->fetchRow($select);	
			$message = str_replace("XXXX", $data['parent_name'], $result['description']);				
			//$message = str_replace("ADDRESS", $data['address'], $message);
			//$message = str_replace("ENID", $data['enquiry_id'], $message);
		}
		//$message = 'you are registered successfully';
		$to = $data['email'];
		$url = 'https://api.sendgrid.com/';
		$user = 'chandravasireddi';
		$pass = 'tissot123';
		
		$json_string = array(
		
		  'to' => array(
		    $to//$data['email']
		  ),
		  'category' => 'test_category'
		);
		
		//$filePath = $_SERVER['DOCUMENT_ROOT'].'/'.'public';
		//$fileName = "doc-att.zip";
		//$filePath = dirname(__FILE__);
		$params = array(
		    'api_user'  => $user,
		    'api_key'   => $pass,
		    'x-smtpapi' => json_encode($json_string),
		    'to'        => $to,
		    'subject'   => 'Esperanza Update',
		    'html'      => $message,
		    'text'      => 'testing body',
		    'from'      => 'tiskannan@gmail.com'
		    //'files['.$fileName.']' => '@'.$filePath.'/'.$fileName
		  );
		//print_r($params);
		
		$request =  $url.'api/mail.send.json';

		// Generate curl request
		$session = curl_init($request);
		
		// Tell curl to use HTTP POST
		curl_setopt ($session, CURLOPT_POST, true);
		
		// Tell curl that this is the body of the POST
		curl_setopt ($session, CURLOPT_POSTFIELDS, $params);
		
		// Tell curl not to return headers, but do return the response
		curl_setopt($session, CURLOPT_HEADER, false);
		// Tell PHP not to use SSLv3 (instead opting for TLS)
		curl_setopt($session, CURLOPT_SSLVERSION, CURL_SSLVERSION_TLSv1_2);
		curl_setopt($session, CURLOPT_RETURNTRANSFER, true);
		
		// obtain response
		$response = curl_exec($session);
		curl_close($session);
		//die;
    } 
	function adminSendMail($data="") {

        
			$message = ($data['parent_name']);	
			$message = ($data['parent_mobile'].$message)	;
			$message = ($data['parent_email'].$message)	;
			$message = ($data['child_admission_no'].$message);
			$message = ($data['child_name'].$message);
			$message = ($data['child_program_id'].$message);
		
		//$message = 'you are registered successfully';
		$to = $data['email'];
		//echo $to;die;
		$config = array('auth' => 'login',
                'username' => 'chandravasireddi',
                'password' => 'tissot123'); 
		$transport = new Zend_Mail_Transport_Smtp('smtp.sendgrid.net', $config);
        $mail = new Zend_Mail();
        $mail->setBodyHtml($message);
        $mail->setFrom('espiranza@gmail.com', 'Espiranza');
        $mail->addTo($to);
		$mail->addCc($data['email']);
		$mail->addBcc($to);
       // $mail->setSubject($result['subject']);	
		
        $mailSend_status = $mail->send($transport);	
		
    }
	
	
	function admissionPerentSendSMS($data="", $type="") {
		
		//echo $message; die;
		if($type == 17)
		{		
			$select=$this->_db->select()
						->from('master_sms')
						->where("sms_type =?", $type);
						//->where("type =?", $type);
			$result=$this->getAdapter()
                      ->fetchRow($select);
			$message = str_replace("XXXX", $data['parent_name'], $result['description']);
			$message = str_replace("CAPTURE AMOUNT", $data['paid_amount'], $message);
			$message = str_replace("DD/MM/YY", $data['todayDate'], $message);
			$message = str_replace("HH:MM:SS", $data['currentTime'], $message);
			$message = str_replace("PAYMENT METHOD", $data['mode_of_payment'], $message);
			$message = str_replace("CHILD NAME", $data['child_name'], $message);
			//$message = str_replace("CAPTURE ANY DUE", $data['child_name'], $message);
			//$message = str_replace("Respective Branch Address",$address, $message);
$father_mobile = '';
		$mother_mobile = '';
		$guardian_mobile = '';
			if(!empty($data['father_mobile'])){
				$father_mobile = rtrim($data['father_mobile'],","); 
			}
			if(!empty($data['mother_mobile'])){
				$mother_mobile = rtrim($data['mother_mobile'],","); 
			}
			if(!empty($data['guardian_mobile'])){
			//echo 'dsd' ; die;
				$guardian_mobile = rtrim($data['guardian_mobile'],","); 
				//echo $guardian_mobile; die;
			} 	
			$mobile = array($father_mobile,$mother_mobile,$guardian_mobile);
			$mb = implode(',',$mobile);
			$m = rtrim($mb,",");
		}
		//echo $message; die;
		//$message = str_replace("XXXX", $data['parent_name'], $result['description']);
		$url = 'http://174.143.34.193/SendSMS/BulkSMS.aspx?';
		$data = "usr=chandra@esperanzacorporate.com&pass=9966000011&msisdn=".$m."&msg=".$message."&sid=ESPRNZ&mt=0";		
		
		$ch = curl_init();
		
		curl_setopt($ch, CURLOPT_URL, $url);
		//curl_setopt($ch, CURLOPT_POST, count($kv));
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		
		curl_setopt($ch, CURLOPT_HEADER, FALSE);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, FALSE);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
		
		$result = curl_exec($ch);
		
		curl_close($ch);
		//echo $data.'----'.$result;die;
    }
	function customerCareSendMail($data="",$type="") {
//print_r($data); die;
  if($type==12)
		{		
			$select=$this->_db->select()
						->from('master_email')
						->where("status !=?", 2)
						->where("type =?", $type);
			$result=$this->getAdapter()
                      ->fetchRow($select);	
		   
			
			$message = str_replace("XXXX", $data['parent_name'], $result['description']);
			$message = str_replace("CAPTURE REQUEST",$data['description'],$message);
			$message = str_replace("DD-MM-YY",$data['execution_date'],$message);
			$message =  str_replace("HH:MM:SS",$data['execution_time'],$message);
			$message =  str_replace("CAPTURE",$data['report_id'],$message);
			$message = str_replace("ADDRESS", $data['address'], $message); 			
			
			if(!empty($data['father_email'])){
				///$email = explode(',',$data['father_email']);
$email = rtrim($data['father_email'],","); 
$father_email = explode(',',$email);
}if(!empty($data['mother_email'])){
$mother= rtrim($data['mother_email'],","); 
$mother_email = explode(',',$mother);
}
if(!empty($data['guardian_email'])){
			

			///$guardian = explode(',',$data['guardian_email']);

$guardian = rtrim($data['guardian_email'],","); 
$guardian_email = explode(',',$guardian);
}

				
			$emails= array();
if(is_array($father_email))
    $emails= array_merge($emails, $father_email);
if(is_array($mother_email))
    $emails= array_merge($emails, $mother_email);
if(is_array($guardian_email))
 $emails= array_merge($emails, $guardian_email);
		}
		//$message = 'you are registered successfully';
		if($type==14)
		{		
			$select=$this->_db->select()
						->from('master_email')
						->where("status !=?", 2)
						->where("type =?", $type);
			$result=$this->getAdapter()
                      ->fetchRow($select);	
		   
			//print_r($data); die;
			$message = str_replace("XXXX", $data['parent_name'], $result['description']);
			$message = str_replace("CAPTURE COMPLAINT",$data['description'],$message);
			$message =  str_replace("CAPTURE",$data['report_id'],$message);
			$message = str_replace("ADDRESS", $data['address'], $message); 
				//print_r($message); die;
			$to = $data['email'];
			
		}
		if($type==13)
		{		
			$select=$this->_db->select()
						->from('master_email')
						->where("status !=?", 2)
						->where("type =?", $type);
			$result=$this->getAdapter()
                      ->fetchRow($select);	
		   
			//print_r($data); die;
			$message = str_replace("XXXX", $data['parent_name'], $result['description']);
			$message = str_replace("CAPTURE SUGGESTION",$data['description'],$message);
			$message =  str_replace("CAPTURE",$data['report_id'],$message);
			$message = str_replace("ADDRESS", $data['address'], $message); 
			$to = $data['email'];
		}
		if($type==15)
		{		
			$select=$this->_db->select()
						->from('master_email')
						->where("status !=?", 2)
						->where("type =?", $type);
			$result=$this->getAdapter()
                      ->fetchRow($select);	
		   
			//print_r($data); die;
			$message = str_replace("XXXX", $data['parent_name'], $result['description']);
			$message = str_replace("ADDRESS", $data['address'], $message); 
			$to = $data['email'];
		}
		if($type==23)
		{		//echo $type; die;
			$select=$this->_db->select()
						->from('master_email')
						->where("status !=?", 2)
						->where("type =?", $type);
			$result=$this->getAdapter()
                      ->fetchRow($select);	
		   
			//print_r($data); die;
			$message = str_replace("XXXX", $data['parent_name'], $result['description']);
			$message = str_replace("CAPTURE REQUEST",$data['description'],$message);
			$message = str_replace("DD-MM-YY",$data['date'],$message);
			$message =  str_replace("HH:MM:SS",$data['time'],$message);
			$message =  str_replace("CAPTURE",$data['report_id'],$message);
			$message = str_replace("ADDRESS", $data['address'], $message); 			
			//print_r($message); die;
			if(!empty($data['father_email'])){
				///$email = explode(',',$data['father_email']);
$email = rtrim($data['father_email'],","); 
$father_email = explode(',',$email);
}if(!empty($data['mother_email'])){
$mother= rtrim($data['mother_email'],","); 
$mother_email = explode(',',$mother);
}
if(!empty($data['guardian_email'])){
			

			///$guardian = explode(',',$data['guardian_email']);

$guardian = rtrim($data['guardian_email'],","); 
$guardian_email = explode(',',$guardian);
}

				
			$emails= array();
if(is_array($father_email))
    $emails= array_merge($emails, $father_email);
if(is_array($mother_email))
    $emails= array_merge($emails, $mother_email);
if(is_array($guardian_email))
 $emails= array_merge($emails, $guardian_email);
			
		}if($type==24)
		{		
			$select=$this->_db->select()
						->from('master_email')
						->where("status !=?", 2)
						->where("type =?", $type);
			$result=$this->getAdapter()
                      ->fetchRow($select);	
		   
			//print_r($data); die;
			$message = str_replace("XXXX", $data['parent_name'], $result['description']);
			$message = str_replace("CAPTURECOMPLAINT",$data['description'],$message);
			$message = str_replace("CAPTURE",$data['report_id'],$message);
			$message = str_replace("ADDRESS", $data['address'], $message); 
				//print_r($message); die;
			$to = $data['email'];
			
		}
				
		//print_r($message); die;

foreach($emails as $k=>$to){
			$url = 'https://api.sendgrid.com/';
		$user = 'chandravasireddi';
		$pass = 'tissot123';
		
		$json_string = array(
		
		  'to' => array(
		    $to
		  ),
		  'category' => 'test_category'
		);
		
		//$filePath = $_SERVER['DOCUMENT_ROOT'].'/'.'public';
		//$fileName = "doc-att.zip";
		//$filePath = dirname(__FILE__);
		$params = array(
		    'api_user'  => $user,
		    'api_key'   => $pass,
		    'x-smtpapi' => json_encode($json_string),
		    'to'        => $to,
		    'subject'   => $result['subject'],
		    'html'      => $message,
		    'text'      => 'testing body',
		    'from'      => $data['branch_email'],
		    //'files['.$fileName.']' => '@'.$filePath.'/'.$fileName
		  );
		
		//print_r($params );die;
		$request =  $url.'api/mail.send.json';

		// Generate curl request
		$session = curl_init($request);
		
		// Tell curl to use HTTP POST
		curl_setopt ($session, CURLOPT_POST, true);
		
		// Tell curl that this is the body of the POST
		curl_setopt ($session, CURLOPT_POSTFIELDS, $params);
		
		// Tell curl not to return headers, but do return the response
		curl_setopt($session, CURLOPT_HEADER, false);
		// Tell PHP not to use SSLv3 (instead opting for TLS)
		curl_setopt($session, CURLOPT_SSLVERSION, CURL_SSLVERSION_TLSv1_2);
		curl_setopt($session, CURLOPT_RETURNTRANSFER, true);
		
		// obtain response
		$response = curl_exec($session);
		curl_close($session);
}
		//die;
	/*	$config = array('auth' => 'login',
                'username' => 'chandravasireddi',
                'password' => 'tissot123'); 
		$transport = new Zend_Mail_Transport_Smtp('smtp.sendgrid.net', $config);
        $mail = new Zend_Mail();
        $mail->setBodyHtml($message);
        $mail->setFrom('espiranza@gmail.com', 'Espiranza');
        $mail->addTo($to);
		$mail->addCc($data['branch_email']);
		//$mail->addCc($data['email']);
		$mail->addBcc($to);
        $mail->setSubject('Request');	
		
        $mailSend_status = $mail->send($transport);	*/
		
      
		
    }
	function customerCareResponseSendMail($data="",$type="") {
		if($type==23)
		{		//echo $type; die;
			$select=$this->_db->select()
						->from('master_email')
						->where("status !=?", 2)
						->where("type =?", $type);
			$result=$this->getAdapter()
                      ->fetchRow($select);	
		   
			//print_r($data); die;
			$message = str_replace("XXXX", $data['parent_name'], $result['description']);
			$message = str_replace("CAPTURE REQUEST",$data['description'],$message);
			$message = str_replace("DD-MM-YY",$data['date'],$message);
			$message =  str_replace("HH:MM:SS",$data['time'],$message);
			$message =  str_replace("CAPTURE",$data['report_id'],$message);
			$message = str_replace("ADDRESS", $data['address'], $message); 			
			//print_r($message); die;
			$to = $data['email'];
		}
		if($type==24)
		{		
			$select=$this->_db->select()
						->from('master_email')
						->where("status !=?", 2)
						->where("type =?", $type);
			$result=$this->getAdapter()
                      ->fetchRow($select);	
		   
			//print_r($data); die;
			$message = str_replace("XXXX", $data['parent_name'], $result['description']);
			$message = str_replace("CAPTURECOMPLAINT",$data['description'],$message);
			$message = str_replace("CAPTURE",$data['report_id'],$message);
			$message = str_replace("ADDRESS", $data['address'], $message); 
				//print_r($message); die;
			$to = $data['email'];
			//$to = 'tissailaja@gmail.com';
			
		}
		$url = 'https://api.sendgrid.com/';
		$user = 'chandravasireddi';
		$pass = 'tissot123';
		
		$json_string = array(
		
		  'to' => array(
		    $to
		  ),
		  'category' => 'test_category'
		);
		
		//$filePath = $_SERVER['DOCUMENT_ROOT'].'/'.'public';
		//$fileName = "doc-att.zip";
		//$filePath = dirname(__FILE__);
		$params = array(
		    'api_user'  => $user,
		    'api_key'   => $pass,
		    'x-smtpapi' => json_encode($json_string),
		    'to'        => $to,
		    'subject'   => $result['subject'],
		    'html'      => $message,
		    'text'      => 'testing body',
		    'from'      => $data['branch_email'],
		    //'files['.$fileName.']' => '@'.$filePath.'/'.$fileName
		  );
		//print_r($params);
		
		$request =  $url.'api/mail.send.json';

		// Generate curl request
		$session = curl_init($request);
		
		// Tell curl to use HTTP POST
		curl_setopt ($session, CURLOPT_POST, true);
		
		// Tell curl that this is the body of the POST
		curl_setopt ($session, CURLOPT_POSTFIELDS, $params);
		
		// Tell curl not to return headers, but do return the response
		curl_setopt($session, CURLOPT_HEADER, false);
		// Tell PHP not to use SSLv3 (instead opting for TLS)
		curl_setopt($session, CURLOPT_SSLVERSION, CURL_SSLVERSION_TLSv1_2);
		curl_setopt($session, CURLOPT_RETURNTRANSFER, true);
		
		// obtain response
		$response = curl_exec($session);
		curl_close($session);
	/*	$config = array('auth' => 'login',
                'username' => 'chandravasireddi',
                'password' => 'tissot123'); 
		$transport = new Zend_Mail_Transport_Smtp('smtp.sendgrid.net', $config);
        $mail = new Zend_Mail();		
        $mail->setBodyHtml($message);
        $mail->setFrom('espiranza@gmail.com', 'Espiranza');
        $mail->addTo($to);
		//$mail->addCc($data['branch_email']);
		//$mail->addBcc($to);
        $mail->setSubject('Hello');	
        $mailSend_status = $mail->send($transport); */
	}
	function  customerCareSendSMS($data="", $type="") {
		
		//echo $message; die;
		if($type ==18 )
		{	$select=$this->_db->select()
						->from('master_sms')
						->where("status !=?", 2)
						->where("sms_type =?", $type);
			$result=$this->getAdapter()
                      ->fetchRow($select);	
		   
			
			$message = str_replace("XXXX", $data['parent_name'], $result['description']);
			$message = str_replace("CAPTURE REQUEST",$data['description'],$message);
			//$message = str_replace("DD-MM-YY",$data['date'],$message);
			//$message =  str_replace("HH:MM:SS",$data['time'],$message);
			$message =  str_replace("CAPTURE",$data['report_id'],$message);
						
			//print_r($message); die;
			
		}
		//$message = 'you are registered successfully';
		if($type==19)
		{		
			$select=$this->_db->select()
						->from('master_sms')
						->where("status !=?", 2)
						->where("sms_type =?", $type);
			$result=$this->getAdapter()
                      ->fetchRow($select);	
		   
			$message = str_replace("XXXX", $data['parent_name'], $result['description']);
			$message = str_replace("CAPTURE REQUEST",$data['description'],$message);
			$message = str_replace("DD-MM-YY",$data['date'],$message);
			$message =  str_replace("HH:MM:SS",$data['time'],$message);
			$message =  str_replace("CAPTURE",$data['report_id'],$message);
			
		}
		if($type==12)
		{		
			$select=$this->_db->select()
						->from('master_sms')
						->where("status !=?", 2)
						->where("sms_type =?", $type);
			$result=$this->getAdapter()
                      ->fetchRow($select);	
		   
			//print_r($data); die;
			$message = str_replace("XXXX", $data['parent_name'], $result['description']);
			$message = str_replace("CAPTURE SUGGESTION",$data['description'],$message);
			$message =  str_replace("CAPTURE",$data['report_id'],$message);
			
		}
		if($type==14)
		{		
			$select=$this->_db->select()
						->from('master_sms')
						->where("status !=?", 2)
						->where("sms_type =?", $type);
			$result=$this->getAdapter()
                      ->fetchRow($select);	
		   
			//print_r($data); die;
			$message = str_replace("XXXX", $data['parent_name'], $result['description']); 
		
		}
		if($type ==21 )
		{	$select=$this->_db->select()
						->from('master_sms')
						->where("status !=?", 2)
						->where("sms_type =?", $type);
			$result=$this->getAdapter()
                      ->fetchRow($select);	
		   
			
			$message = str_replace("XXXX", $data['parent_name'], $result['description']);
			$message = str_replace("CAPTURE REQUEST",$data['description'],$message);
			$message = str_replace("DD-MM-YY",$data['date'],$message);
			$message =  str_replace("HH:MM:SS",$data['time'],$message);
			$message =  str_replace("CAPTURE",$data['report_id'],$message);
						
			//print_r($message); die;
			
		}
		if($type==22)
		{		
			$select=$this->_db->select()
						->from('master_sms')
						->where("status !=?", 2)
						->where("sms_type =?", $type);
			$result=$this->getAdapter()
                      ->fetchRow($select);	
		   
			//print_r($data); die;
			$message = str_replace("XXXX", $data['parent_name'], $result['description']);
			$message = str_replace("CAPTURE COMPLAINT",$data['description'],$message);
			$message =  str_replace("CAPTURE",$data['report_id'],$message);
				//print_r($message); die;
			
		}
		//echo $message; die;
		//$message = str_replace("XXXX", $data['parent_name'], $result['description']);
$father_mobile = '';
		$mother_mobile = '';
		$guardian_mobile = '';
			if(!empty($data['father_mobile'])){
				$father_mobile = rtrim($data['father_mobile'],","); 
			}
			if(!empty($data['mother_mobile'])){
				$mother_mobile = rtrim($data['mother_mobile'],","); 
			}
			if(!empty($data['guardian_mobile'])){
			//echo 'dsd' ; die;
				$guardian_mobile = rtrim($data['guardian_mobile'],","); 
				//echo $guardian_mobile; die;
			} 	
			$mobile = array($father_mobile,$mother_mobile,$guardian_mobile);
			$mb = implode(',',$mobile);
			$m = rtrim($mb,",");
		$url = 'http://174.143.34.193/SendSMS/BulkSMS.aspx?';
		$data = "usr=chandra@esperanzacorporate.com&pass=9966000011&msisdn=".$m."&msg=".$message."&sid=ESPRNZ&mt=0";

		$ch = curl_init();
		
		curl_setopt($ch, CURLOPT_URL, $url);
		//curl_setopt($ch, CURLOPT_POST, count($kv));
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		
		curl_setopt($ch, CURLOPT_HEADER, FALSE);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, FALSE);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
		
		$result = curl_exec($ch);
		
		curl_close($ch);
		
    }
	function customerCareResponseSendSMS($data="",$type="") {
		if($type ==21 )
		{	$select=$this->_db->select()
						->from('master_sms')
						->where("status !=?", 2)
						->where("sms_type =?", $type);
			$result=$this->getAdapter()
                      ->fetchRow($select);	
			$message = str_replace("XXXX", $data['parent_name'], $result['description']);
			$message = str_replace("CAPTURE REQUEST",$data['description'],$message);
			$message = str_replace("DD-MM-YY",$data['date'],$message);
			$message =  str_replace("HH:MM:SS",$data['time'],$message);
			$message =  str_replace("CAPTURE",$data['report_id'],$message);
						
			//print_r($message); die;
			
		}
		if($type==22)
		{		
			$select=$this->_db->select()
						->from('master_sms')
						->where("status !=?", 2)
						->where("sms_type =?", $type);
			$result=$this->getAdapter()
                      ->fetchRow($select);	
		   
			//print_r($data); die;
			$message = str_replace("XXXX", $data['parent_name'], $result['description']);
			$message = str_replace("CAPTURE COMPLAINT",$data['description'],$message);
			$message =  str_replace("CAPTURE",$data['report_id'],$message);
				//print_r($message); die;
			
		}
		$url = 'http://174.143.34.193/SendSMS/BulkSMS.aspx?';
		$data = "usr=chandra@esperanzacorporate.com&pass=9966000011&msisdn=".$data['father_mobile_no'].','.$data['mother_mobile_no'].','.$data['branch_mobile']."&msg=".$message."&sid=ESPRNZ&mt=0";

		$ch = curl_init();
		
		curl_setopt($ch, CURLOPT_URL, $url);
		//curl_setopt($ch, CURLOPT_POST, count($kv));
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		
		curl_setopt($ch, CURLOPT_HEADER, FALSE);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, FALSE);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
		
		$result = curl_exec($ch);
		
		curl_close($ch);
		

	}
		
	
	function employeeSendMail($data="",$type="") {
		
        if($type==25)
		{		
			$select=$this->_db->select()
						->from('master_email')
						->where("status !=?", 2)
						->where("type =?", $type);
			$result=$this->getAdapter()
                      ->fetchRow($select);	
		   
			//print_r($data); die;
			$message = str_replace("XXXX",$data['employee_name'], $result['description']);
			$message = str_replace("EMAILID",$data['email'],$message);
			//print_r($message); die;
			$message = str_replace(" PASSWORD",$data['password'],$message);
		}
		//print_r($message);die;
		$to = $data['email'];
		$config = array('auth' => 'login',
                'username' => 'chandravasireddi',
                'password' => 'tissot123'); 
		$transport = new Zend_Mail_Transport_Smtp('smtp.sendgrid.net', $config);
        $mail = new Zend_Mail();
        $mail->setBodyHtml($message);
        $mail->setFrom('espiranza@gmail.com', 'Espiranza');
        $mail->addTo($to);
		//$mail->addCc($data['branch_email']);
		$mail->addCc($data['email']);
		$mail->addBcc($to);
        $mail->setSubject('Request');	
		
        $mailSend_status = $mail->send($transport);	
		
    }
	function birthdayStatusSendMail($data="",$type="") {
		
        if($type==26)
		{		
			$select=$this->_db->select()
						->from('master_email')
						->where("status !=?", 2)
						->where("type =?", $type);
			$result=$this->getAdapter()
                      ->fetchRow($select);	
		   
			//print_r($data); die;
			$message = str_replace("CHILDNAME ",$data['child_first_name'], $result['description']);
			$message = str_replace("DATEOFBIRTH",$data['date_of_birth'],$message);
			
			
		}
		//print_r($message);die;
		$to = $data['email'];
		$config = array('auth' => 'login',
                'username' => 'chandravasireddi',
                'password' => 'tissot123'); 
		$transport = new Zend_Mail_Transport_Smtp('smtp.sendgrid.net', $config);
        $mail = new Zend_Mail();
        $mail->setBodyHtml($message);
        $mail->setFrom('espiranza@gmail.com', 'Espiranza');
        $mail->addTo($to);
		//$mail->addCc($data['branch_email']);
		$mail->addCc($data['email']);
		$mail->addBcc($to);
        $mail->setSubject('Request');	
		
        $mailSend_status = $mail->send($transport);	
		
    }
	function parentSendMail($data="") {
		
        
				
			$select=$this->_db->select()
						->from('send_email_and_sms')
						->where("status !=?", 2);
						//->where("branch =?", $branch);
			$result=$this->getAdapter()
                      ->fetchRow($select);	
		   
			//print_r($data); die;
			$message =  str_replace("XXXXX",$data['parent_name'],$data['email_content']);
			$message = str_replace("CHILDNAME",$data['child_name'],$message);
			$message = str_replace("ADMISSIONNUMBER",$data['admission_number'],$message);
			$message = str_replace("BRANCHNAME",$data['branch_name'],$message);
			$message = str_replace("ADDRESS",$data['address'],$message);
			
		//print_r($message);die;
		$to = $data['email'];
		//echo $to; die;
		$config = array('auth' => 'login',
                'username' => 'chandravasireddi',
                'password' => 'tissot123'); 
		$transport = new Zend_Mail_Transport_Smtp('smtp.sendgrid.net', $config);
        $mail = new Zend_Mail();
        $mail->setBodyHtml($message);
        $mail->setFrom('espiranza@gmail.com', 'Espiranza');
        $mail->addTo($to);
		$mail->addCc($data['branch_email']);
		//$mail->addCc($data['email']);
		//$mail->addBcc($to);
        $mail->setSubject($data['subject']);	
		
        $mailSend_status = $mail->send($transport);	
		
    }
	function parentSendSMS($data="") {
		
		//echo $message; die;
				
		if(!empty($data['father_mobile_no'])){
				$mobileNo[] = $data['father_mobile_no']; 
			}if(!empty($data['mother_mobile_no'])){
				$mobileNo[] = $data['mother_mobile_no']; 
			}if(!empty($data['guardian_mobile_no'])){
				$mobileNo[] = $data['guardian_mobile_no']; 
			}
				
			$select=$this->_db->select()
						->from('send_email_and_sms');
						//->where("branch =?", $branch);
						//->where("type =?", $type);
			$result=$this->getAdapter()
                      ->fetchRow($select);
				$message =  str_replace("XXXXX",$data['parent_name'],$data['sms_content']);
			$message = str_replace(" CHILDNAME",$data['child_name'],$message);
			$message = str_replace("ADMISSIONNUMBER",$data['admission_number'],$message);
			$message = str_replace("ADDRESS",$data['address'],$message);
			
		
		//echo $message; die;
		//$message = str_replace("XXXX", $data['parent_name'], $result['description']);
			for($i=0;$i<count($mobileNo);$i++)
		{
		$url = 'http://174.143.34.193/SendSMS/BulkSMS.aspx?';
		$data = "usr=chandra@esperanzacorporate.com&pass=9966000011&msisdn=".$mobileNo[$i]."&msg=".$message."&sid=ESPRNZ&mt=0";		
		
		$ch = curl_init();
		
		curl_setopt($ch, CURLOPT_URL, $url);
		//curl_setopt($ch, CURLOPT_POST, count($kv));
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		
		curl_setopt($ch, CURLOPT_HEADER, FALSE);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, FALSE);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
		
		$result = curl_exec($ch);
		
		curl_close($ch);
		}
		
    }
	
	//Upcoming Birthdays Inform to Branch  
	function sendEnquiryReminderMail($data="",$value="") {
        
		
		//echo $message; die;
		
		//echo $to; die;
		//echo $message; die;
		//$message = 'you are registered successfully';
		$to = $data['branch_email_id'];
		$config = array('auth' => 'login',
                'username' => 'chandravasireddi',
                'password' => 'tissot123'); 
		$transport = new Zend_Mail_Transport_Smtp('smtp.sendgrid.net', $config);
        $mail = new Zend_Mail();		
        $mail->setBodyHtml($value);
        $mail->setFrom('espiranza@gmail.com', 'Espiranza');
        $mail->addTo($to);
		//$mail->addCc($data['branch_email']);
		//$mail->addBcc($to);
        $mail->setSubject('Hello');	
        $mailSend_status = $mail->send($transport);
    }
	function SpecialCareSendMail($data="") {
	
		$message = str_replace("admission_number",$data['admission_number'],'Dear Branch Name,
					Please take care about my childName admission_number from from_date to to_date');
		$message = str_replace("from_date",$data['from_date'].'-'.$data['from_time'],$message);			
		$message = str_replace("to_date",$data['to_date'].'-'.$data['to_time'],$message);			
		//echo $message; die;
		$to = 'tisrajkumar@gmail.com';//$data['branch_email'];
		
		$url = 'https://api.sendgrid.com/';
		$user = 'chandravasireddi';
		$pass = 'tissot123';
		
		$json_string = array(
		
		  'to' => array(
		    $to
		  ),
		  'category' => 'test_category'
		);
		
		//$filePath = $_SERVER['DOCUMENT_ROOT'].'/'.'public';
		//$fileName = "doc-att.zip";
		//$filePath = dirname(__FILE__);
		$params = array(
		    'api_user'  => $user,
		    'api_key'   => $pass,
		    'x-smtpapi' => json_encode($json_string),
		    'to'        => $to,
		    'subject'   => 'Update',
		    'html'      => $message,
		    'text'      => 'testing body',
		    'from'      => $data['Email'],
		    //'files['.$fileName.']' => '@'.$filePath.'/'.$fileName
		  );
		//print_r($params);
		
		$request =  $url.'api/mail.send.json';

		// Generate curl request
		$session = curl_init($request);
		
		// Tell curl to use HTTP POST
		curl_setopt ($session, CURLOPT_POST, true);
		
		// Tell curl that this is the body of the POST
		curl_setopt ($session, CURLOPT_POSTFIELDS, $params);
		
		// Tell curl not to return headers, but do return the response
		curl_setopt($session, CURLOPT_HEADER, false);
		// Tell PHP not to use SSLv3 (instead opting for TLS)
		curl_setopt($session, CURLOPT_SSLVERSION, CURL_SSLVERSION_TLSv1_2);
		curl_setopt($session, CURLOPT_RETURNTRANSFER, true);
		
		// obtain response
		$response = curl_exec($session);
		curl_close($session);
		
		//echo $data['branch_email']; die;
	/*	$config = array('auth' => 'login',
                'username' => 'chandravasireddi',
                'password' => 'tissot123'); 
		$transport = new Zend_Mail_Transport_Smtp('smtp.sendgrid.net', $config);
        $mail = new Zend_Mail();		
        $mail->setBodyHtml($message);
        $mail->setFrom('espiranza@gmail.com', 'Espiranza');
        $mail->addTo($to);
		//$mail->addCc($data['branch_email']);
		//$mail->addBcc($to);
        $mail->setSubject('Hello');	
        $mailSend_status = $mail->send($transport); */
    }
	
	
	
  function uniqueID(){ 
	  $rand = substr(md5(microtime()),rand(0,26),4);
	  return $rand;
  }
  
  function RandomPass($name){
	  $rand = md5($name);
	  return $rand;
  }
  function BranchTransferSatisfying($data="",$type="") {
		
        //print_r($data); die;
		if($type==56){
			$select=$this->_db->select()
						->from('master_email')
						->where("status !=?", 2)
						->where("type =?", $type);
						//->where("branch =?", $branch);
			$result=$this->getAdapter()
                      ->fetchRow($select);	
		   //print_r($result);die;
			
			//print_r($data); die;
			$message =  str_replace("XXXX",$data['parent_name'],$result['description']);
			$message = str_replace("XYZ123",$data['transfer_id'],$message);
			$message = str_replace("DD-MM-YY",$data['date_of_transfer'],$message);
			$message = str_replace("CAPTURE AMOUNT.",$data['payable_amount'],$message);
			//print_r($message); die;
			
		}
			
		
		//$to = "tissailaja@gmail.com";
		//echo $to; die;
		$config = array('auth' => 'login',
                'username' => 'chandravasireddi',
                'password' => 'tissot123'); 
		$transport = new Zend_Mail_Transport_Smtp('smtp.sendgrid.net', $config);
        $mail = new Zend_Mail();
        $mail->setBodyHtml($message);
        $mail->setFrom('espiranza@gmail.com', 'Espiranza');
        $mail->addTo($to);
		//$mail->addCc($data['branch_email']);
		//$mail->addCc($data['email']);
		//$mail->addBcc($to);
        $mail->setSubject($data['subject']);	
		
        $mailSend_status = $mail->send($transport);	
		
    }
	 function BranchTransferDeclining($data="",$type="") {
		
        //print_r($data); die;
		if($type==57){
			$select=$this->_db->select()
						->from('master_email')
						->where("status !=?", 2)
						->where("type =?", $type);
						//->where("branch =?", $branch);
			$result=$this->getAdapter()
                      ->fetchRow($select);	
		   //print_r($result);die;
			
			//print_r($data); die;
			$message =  str_replace("XXXX",$data['parent_name'],$result['description']);
			$message = str_replace("XYZ123",$data['transfer_id'],$message);
			//print_r($message); die;
			
		}
			
		
		$to = "tissailaja@gmail.com";
		//echo $to; die;
		$config = array('auth' => 'login',
                'username' => 'chandravasireddi',
                'password' => 'tissot123'); 
		$transport = new Zend_Mail_Transport_Smtp('smtp.sendgrid.net', $config);
        $mail = new Zend_Mail();
        $mail->setBodyHtml($message);
        $mail->setFrom('espiranza@gmail.com', 'Espiranza');
        $mail->addTo($to);
		//$mail->addCc($data['branch_email']);
		//$mail->addCc($data['email']);
		//$mail->addBcc($to);
        $mail->setSubject($data['subject']);	
		
        $mailSend_status = $mail->send($transport);	
		
    }
	
 
function branchCredentials($data=""){
			$message .= "Dear<br  />";
			$message .= "Your branch is created.<br />";
			$message .= "Below are the login credentials Username-EMAILID,   Password-GENERALPASSWORD.";
			$message = str_replace("BRANCH", $data['branch_name'], $message);
			$message = str_replace("EMAILID",$data['branch_email_id'],$message);
			$message =  str_replace("GENERALPASSWORD",$data['password'],$message);
		
		//echo $message; die;
		
			$to = $data['branch_email_id'];
		//echo $to; die;
                        $url = 'https://api.sendgrid.com/';
		$user = 'chandravasireddi';
		$pass = 'tissot123';
		
		$json_string = array(
		
		  'to' => array(
		    $to
		  ),
		  'category' => 'test_category'
		);
		
		$filePath = $_SERVER['DOCUMENT_ROOT'].'/'.'public';
		$fileName = "doc-att.zip";
		//$filePath = dirname(__FILE__);
		$params = array(
		    'api_user'  => $user,
		    'api_key'   => $pass,
		    'x-smtpapi' => json_encode($json_string),
		    'to'        => $to,
		    'subject'   => "Welcome to Esperanza",
		    'html'      => $message,
		    'text'      => 'testing body',
		    'from'      => 'espiranza@gmail.com', 'Espiranza',
		    'files['.$fileName.']' => '@'.$filePath.'/'.$fileName
		  );
		//print_r($params);
		
		$request =  $url.'api/mail.send.json';

		// Generate curl request
		$session = curl_init($request);
		
		// Tell curl to use HTTP POST
		curl_setopt ($session, CURLOPT_POST, true);
		
		// Tell curl that this is the body of the POST
		curl_setopt ($session, CURLOPT_POSTFIELDS, $params);
		
		// Tell curl not to return headers, but do return the response
		curl_setopt($session, CURLOPT_HEADER, false);
		// Tell PHP not to use SSLv3 (instead opting for TLS)
		curl_setopt($session, CURLOPT_SSLVERSION, CURL_SSLVERSION_TLSv1_2);
		curl_setopt($session, CURLOPT_RETURNTRANSFER, true);
		
		// obtain response
		$response = curl_exec($session);

		curl_close($session);
		
		/* $config = array('auth' => 'login',
                'username' => 'chandravasireddi',
                'password' => 'tissot123'); 
		$transport = new Zend_Mail_Transport_Smtp('smtp.sendgrid.net', $config);
        $mail = new Zend_Mail();
        $mail->setBodyHtml($message);
        $mail->setFrom('espiranza@gmail.com', 'Espiranza');
        $mail->addTo($to);
		$mail->addCc();
		//$mail->addCc($data['email']);
		//$mail->addBcc($to);
        $mail->setSubject("Branch Login Details");
        $mailSend_status = $mail->send($transport); */
		}
function CampAdmissionSendMail($data="",$type="") {

		$select_branch = $this->_db->select()
						->from('master_branch')
						->where("status !=?", 2)
						->where("branch_id =?", $data['branch_id']);
		$branch_data =	$this->getAdapter()
						->fetchRow($select_branch);	
						$address = $branch_data['branch_name'].'<br />'.$branch_data['branch_address'].'<br /> T:'.$branch_data['branch_phone_no1'].'<br /> M:'.$branch_data['branch_mobile_no'];
						
		//Get head office details
		$select_main = $this->_db->select()
						->from('master_main_branch')
						->where("status !=?", 2);
		$mainbranch_data = $this->getAdapter()
						->fetchRow($select_main);
		
		if($type==51){
              
			$branch_mails['secondary_email_address'] = $data['allbranch_email'];
			//print_r($branch_mails['secondary_email_address']);die;
			foreach($branch_mails['secondary_email_address'] as $k=>$val1){ 
			$secondary_email['secondary_email_address']=$val1;
			//echo $secondary_email['secondary_email_address'];die;
			}
			$primary_branchemail['branch_email_id']=$branch_data['branch_email_id'];
			//print_r($primary_branchemail['branch_email_id']);die;
			$select=$this->_db->select()
						->from('master_email')
						->where("status !=?", 2)
						->where("type =?", $type);
			$result=$this->getAdapter()
                      ->fetchRow($select);

			$branch_mobile_nos = $branch_data['branch_phone_no1'].','.$branch_data['branch_mobile_no'].','.$branch_data['branch_secondary_mobile_no'];
			$select=$this->_db->select()
						->from('master_email')
						->where("status !=?", 2)
						->where("type =?", $type);
			$result=$this->getAdapter()
                      ->fetchRow($select);	
			//print_r($data); die;
			//print_r($data['admission_no']);die;
			$message =  str_replace("XXXX",$data['parent_name'],$result['description']);
			$message = str_replace("CAPTURE CAMP TYPE",$data['camp_type'],$message);
			$message = str_replace("CAPTURE CHILD NAME",$data['child_name'],$message);
			$message = str_replace("CAPTURE CURRENCY",$data['currency'],$message);
			$message = str_replace("CAPTURE AMOUNT",$data['total_paid'],$message);
			$message = str_replace("CAPTURE ADMISSION NUMBER",$data['admission_no'],$message);
		//print_r($message);die;
			$message = str_replace("CAPTURE CAMP YEAR",$data['campyear'],$message);
			$message = str_replace("CAPURE CAMP BATCH",$data['campbatch'],$message);
			$message = str_replace("CAPTURE CAMP PACKAGE",$data['camppackage'],$message);
			$message = str_replace("CAPTURE PAYABLE AMOUNT",$data['amount'],$message);
			$message = str_replace("CAPTURE DISCOUNT AMOUNT",$data['discount'],$message);
			$message = str_replace("CAPTURE TOTAL PAID AMOUNT",$data['total_paid'],$message);
			$message = str_replace("CAPTURE BALANCE DUE",$data['balance'],$message);
			$message = str_replace("CAPTURE PAYMENT MODE",$data['pay_mode'],$message);
			$message = str_replace("CAPTURE TRANSACTION NUMBER",$data['transaction'],$message);
			$message = str_replace("CAPTURE DATE&TIME OF TRANSACTION",$data['date&time'],$message);
			$message = str_replace("CAPTURE ALL BRANCH NUMBERS",$branch_mobile_nos,$message);
			$message = str_replace("CAPTURE ALL BRANCH EMAIL ID",$primary_branchemail['branch_email_id'].','.$secondary_email['secondary_email_address'],$message);
			$message = str_replace("CAPTURE BRANCH NAME",$branch_data['branch_name'],$message);
			$message = str_replace("CAPTURE BRANCH",$branch_data['branch_name'],$message);
			$message = str_replace("CAPTUREBRANCH ADDRESS",$branch_data['branch_address'],$message);
			//print_r($message);die;
			$subject=  str_replace("CAPTURE CAMPTYPE",$data['camp_type'],$result['subject']);
			//print_r($subject);die;
			$subject=  str_replace("CAPTURE CHILDNAME",$data['child_name'],$subject);
		  // print_r($message);die;
		}	
		//print_r($message);die;
		if(!empty($data['father_email'])){
		$mother= rtrim($data['father_email'],","); 
		$father_email  = explode(',',$mother);
		//$mother = explode(',',$data['father_email']);
		}		
		if(!empty($data['mother_email'])){
		$mother= rtrim($data['mother_email'],","); 
		$mother_email  = explode(',',$mother);
		//$mother = explode(',',$data['mother_email']);
		}
		if(!empty($data['guardian_email'])){
					
		///$guardian = explode(',',$data['guardian_email']);

		$guardian = rtrim($data['guardian_email'],","); 
		$guardian_email = explode(',',$guardian);
		}
		$emails= array();
		if(is_array($father_email))
			$emails= array_merge($emails, $father_email);
		if(is_array($mother_email))
			$emails= array_merge($emails, $mother_email);
		if(is_array($guardian_email))
			$emails= array_merge($emails, $guardian_email);
		// $Email = array_merge($father_mail,$mother_mail,$guardian_mail);
		// $to = $Email;
		//echo $to; die;
		foreach($emails as $k => $to){
		//print_r($to);die;
		$url = 'https://api.sendgrid.com/';
		$user = 'chandravasireddi';
		$pass = 'tissot123';
		
		$json_string = array(
		
		  'to' => array(
		    $to//$data['email']
		  ),
		  'category' => 'test_category'
		);
		
		$filePath = $_SERVER['DOCUMENT_ROOT'].'/'.'public';
		$fileName = "doc-att.zip";
		//$filePath = dirname(__FILE__);
		$params = array(
		    'api_user'  => $user,
		    'api_key'   => $pass,
		    'x-smtpapi' => json_encode($json_string),
		    'to'        => $to,
		    'subject'   => $result['subject'],
		    'html'      => $message,
		    'text'      => 'testing body',
		    'from'      => 'espiranza@gmail.com', 'Espiranza',
		    'files['.$fileName.']' => '@'.$filePath.'/'.$fileName
		  );
		//print_r($params);
		
		$request =  $url.'api/mail.send.json';

		// Generate curl request
		$session = curl_init($request);
		
		// Tell curl to use HTTP POST
		curl_setopt ($session, CURLOPT_POST, true);
		
		// Tell curl that this is the body of the POST
		curl_setopt ($session, CURLOPT_POSTFIELDS, $params);
		
		// Tell curl not to return headers, but do return the response
		curl_setopt($session, CURLOPT_HEADER, false);
		// Tell PHP not to use SSLv3 (instead opting for TLS)
		curl_setopt($session, CURLOPT_SSLVERSION, CURL_SSLVERSION_TLSv1_2);
		curl_setopt($session, CURLOPT_RETURNTRANSFER, true);
		
		// obtain response
		$response = curl_exec($session);
		curl_close($session);
		}
    }
   function campAdmissionSendSMS($data="", $type="") {
		//print_r($data);die;
		$select_branch = $this->_db->select()
						->from('master_branch')
						->where("status !=?", 2)
						->where("branch_id =?", $data['branch_id']);
		$branch_data =	$this->getAdapter()
						->fetchRow($select_branch);	
						$address = $branch_data['branch_name'].'<br />'.$branch_data['branch_address'].'<br /> T:'.$branch_data['branch_phone_no1'].'<br /> M:'.$branch_data['branch_mobile_no'];
		//print_r($data['father_mobile']);die;
        if($type == 51)
		{		
			$select=$this->_db->select()
						->from('master_sms')
						->where("sms_type =?", $type);
						//->where("type =?", $type);
			$result=$this->getAdapter()
                      ->fetchRow($select);
			
			$message = str_replace("XXXX", $data['parent_name'], $result['description']);
			$message = str_replace("CAPTURE CURRENCY", $data['currency'], $message);
			$message = str_replace("CAPTURE AMOUNT", $data['amount'], $message);
			$message = str_replace("CAPTURE CHILD NAME", $data['child_name'], $message);
			$message = str_replace("CAPTURE CAMP TYPE", $data['camp_type'], $message);
			$message = str_replace("CAPTURE CAMP YEAR", $data['campyear'], $message);
			$message = str_replace("CAPTURE BALANCE DUE", $data['balance'], $message);
			$message = str_replace("CAPTURE BRANCH", $branch_data['branch_name'], $message);
			//print_r($message);die;
		}
		
			$father_mobileNo = '';
			$mother_mobileNo = '';
			$guardian_mobileNo = '';
		    if(!empty($data['father_mobile'])){
				//$father_mobileNo = $data['father_mobile']; 
				$father_mobileNo = rtrim($data['father_mobile'],",");
				//print_r($father_mobileNo);die;
			}if(!empty($data['mother_mobile'])){
				//$mother_mobileNo = $data['mother_mobile']; 
				$mother_mobileNo = rtrim($data['mother_mobile'],",");
			}if(!empty($data['guardian_mobile'])){
				//$guardian_mobileNo = $data['guardian_mobile']; 
				$guardian_mobileNo = rtrim($data['guardian_mobile'],",");
			}
		$mobile = array($father_mobileNo,$mother_mobileNo,$guardian_mobileNo);
		
		$mb = implode(',',$mobile);
		//print_r($mb);die;
		$m = rtrim($mb,",");
		//print_r($m);die;
		/*if(!empty($data['father_mobile'])){
				$mobileNo = $data['father_mobile']; 
			}if(!empty($data['mother_mobile'])){
				$mobileNo = $data['mother_mobile']; 
			}if(!empty($data['guardian_mobile'])){
				$mobileNo = $data['guardian_mobile']; 
			}
		*/
		//print_r($data['guardian_mobile']);die;
		//$mobile_nos = implode(',',$Mobile);
		//for($i=0;$i<=count($mobileNo);$i++){
		//print_r($mobileNo[$i]);die;
		$url = 'http://174.143.34.193/SendSMS/BulkSMS.aspx?';
		$data = "usr=chandra@esperanzacorporate.com&pass=9966000011&msisdn=".$m."&msg=".$message."&sid=ESPRNZ&mt=0";
		//print_r($data);die;
		//echo $url.$data;die;
		$ch = curl_init();
		
		curl_setopt($ch, CURLOPT_URL, $url);
		//curl_setopt($ch, CURLOPT_POST, count($kv));
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		
		curl_setopt($ch, CURLOPT_HEADER, FALSE);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, FALSE);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
		
		$result = curl_exec($ch);
		
		curl_close($ch);
	  //}
   }
   
  function CampEnquirySendMail($data="",$type="") {
		
		$select_branch = $this->_db->select()
						->from('master_branch')
						->where("status !=?", 2)
						->where("branch_id =?", $data['branch_id']);
		$branch_data =	$this->getAdapter()
						->fetchRow($select_branch);	
						$address = $branch_data['branch_name'].'<br />'.$branch_data['branch_address'].'<br /> T:'.$branch_data['branch_phone_no1'].'<br /> M:'.$branch_data['branch_mobile_no'];
						
		//Get head office details
		$select_main = $this->_db->select()
						->from('master_main_branch')
						->where("status !=?", 2);
		$mainbranch_data = $this->getAdapter()
						->fetchRow($select_main);
						
		if($type==48){
	
			//print_r($primary_branchemail);die;
			$branch_mails['secondary_email_address'] = $data['allbranch_email'];
			//print_r($branch_mails['secondary_email_address']);die;
			foreach($branch_mails['secondary_email_address'] as $k=>$val1){ 
			$secondary_email['secondary_email_address']=$val1;
			//echo $secondary_email['secondary_email_address'];die;
			}
			$primary_branchemail['branch_email_id']=$branch_data['branch_email_id'];
			//print_r($primary_branchemail['branch_email_id']);die;
			$select=$this->_db->select()
						->from('master_email')
						->where("status !=?", 2)
						->where("type =?", $type);
			$result=$this->getAdapter()
                      ->fetchRow($select);	
			//print_r($data); die;
			$message =  str_replace("XXXX",$data['parent_name'],$result['description']);
			$message = str_replace("CAPTURE CAMP TYPE",$data['camp_type'],$message);
			$message = str_replace("CAPTURE BRANCH NAME",$branch_data['branch_name'],$message);
			$message = str_replace("CAPTURE CHILD NAME",$data['child_name'],$message);
			$message = str_replace("CAPTURE ENQUIRY ID",$data['camp_enquiry_id'],$message);
			$message = str_replace("CAPTURE ALL BRANCH EMAIL ID",$primary_branchemail['branch_email_id'].','.$secondary_email['secondary_email_address'],$message);
			//print_r($message);die;
			//$message = str_replace("CAPTURE ALL BRANCH EMAIL ID",$branch_emails,$message);
			$message = str_replace("CAPTURE ALL BRANCH PHONE NUMBER",$branch_data['branch_phone_no1'].','.$branch_data['branch_mobile_no'],$message);
			$message = str_replace("CAPTURE BRANCH TEAM",$branch_data['branch_name'],$message);
			$message = str_replace("CAPTURE BRANCH ADDRESS",$branch_data['branch_address'],$message);
			$subject=  str_replace("CAPTURE CAMPTYPE",$data['camp_type'],$result['subject']);
			//print_r($subject);die;
			$subject=  str_replace("CAPTURE BRANCH NAME",$branch_data['branch_name'],$subject);
//print_r($message);die;
if(!empty($data['father_email'])){
$mother= rtrim($data['father_email'],","); 
$father_email  = explode(',',$mother);
//$mother = explode(',',$data['father_email']);
}		
if(!empty($data['mother_email'])){
$mother= rtrim($data['mother_email'],","); 
$mother_email  = explode(',',$mother);
//$mother = explode(',',$data['mother_email']);
}
if(!empty($data['guardian_email'])){
			
///$guardian = explode(',',$data['guardian_email']);

$guardian = rtrim($data['guardian_email'],","); 
$guardian_email = explode(',',$guardian);
}
$emails= array();
if(is_array($father_email))
    $emails= array_merge($emails, $father_email);
if(is_array($mother_email))
    $emails= array_merge($emails, $mother_email);
if(is_array($guardian_email))
    $emails= array_merge($emails, $guardian_email);

		}	
		// print_r($message);die;
		// $father_mail=$data['father_email'];
		// $mother_mail=$data['mother_email'];
		// $guardian_mail=$data['guardian_email'];
		// $Email = array_merge($father_mail,$mother_mail,$guardian_mail);
		// $to = $Email;
		// echo $to; die;
	foreach($emails as $k => $to){
		$url = 'https://api.sendgrid.com/';
		$user = 'chandravasireddi';
		$pass = 'tissot123';
		
		$json_string = array(
		
		  'to' => array(
		    $to//$data['email']
		  ),
		  'category' => 'test_category'
		);
		
		$filePath = $_SERVER['DOCUMENT_ROOT'].'/'.'public';
		$fileName = "doc-att.zip";
		//$filePath = dirname(__FILE__);
		$params = array(
		    'api_user'  => $user,
		    'api_key'   => $pass,
		    'x-smtpapi' => json_encode($json_string),
		    'to'        => $to,
		    'subject'   => $subject,
		    'html'      => $message,
		    'text'      => 'testing body',
		    'from'      => 'espiranza@gmail.com', 'Espiranza',
		    'files['.$fileName.']' => '@'.$filePath.'/'.$fileName
		  );
		//print_r($params);
		$request =  $url.'api/mail.send.json';

		// Generate curl request
		$session = curl_init($request);
		
		// Tell curl to use HTTP POST
		curl_setopt ($session, CURLOPT_POST, true);
		
		// Tell curl that this is the body of the POST
		curl_setopt ($session, CURLOPT_POSTFIELDS, $params);
		
		// Tell curl not to return headers, but do return the response
		curl_setopt($session, CURLOPT_HEADER, false);
		// Tell PHP not to use SSLv3 (instead opting for TLS)
		curl_setopt($session, CURLOPT_SSLVERSION, CURL_SSLVERSION_TLSv1_2);
		curl_setopt($session, CURLOPT_RETURNTRANSFER, true);
		
		//obtain response
		$response = curl_exec($session);
		curl_close($session);	
	 }	
    }
	
	function campEnquirySendSMS($data="", $type="") {
		//print_r($data);die;
		$select_branch = $this->_db->select()
						->from('master_branch')
						->where("status !=?", 2)
						->where("branch_id =?", $data['branch_id']);
		$branch_data =	$this->getAdapter()
						->fetchRow($select_branch);	
						$address = $branch_data['branch_name'].'<br />'.$branch_data['branch_address'].'<br /> T:'.$branch_data['branch_phone_no1'].'<br /> M:'.$branch_data['branch_mobile_no'];
		
        if($type == 48)
		{		
			$select=$this->_db->select()
						->from('master_sms')
						->where("sms_type =?", $type);
						//->where("type =?", $type);
			$result=$this->getAdapter()
                      ->fetchRow($select);
			
			$message = str_replace("XXXX", $data['parent_name'], $result['description']);
			$message = str_replace("CAPTURE CAMPTYPE", $data['camp_type'], $message);
			$message = str_replace("CAPTURE BRANCH NAME",$branch_data['branch_name'], $message);
			$message = str_replace("CAPTURE ENQUIRY ID",$data['camp_enquiry_id'], $message);
			$message = str_replace("CAPTURE BRANCH",$branch_data['branch_name'], $message);
			//print_r($message);die;
		}
			$father_mobileNo = '';
			$mother_mobileNo = '';
			$guardian_mobileNo = '';
		    if(!empty($data['father_mobile'])){
				//$father_mobileNo = $data['father_mobile']; 
				$father_mobileNo = rtrim($data['father_mobile'],",");
				//print_r($father_mobileNo);die;
			}if(!empty($data['mother_mobile'])){
				//$mother_mobileNo = $data['mother_mobile']; 
				$mother_mobileNo = rtrim($data['mother_mobile'],",");
			}if(!empty($data['guardian_mobile'])){
				//$guardian_mobileNo = $data['guardian_mobile']; 
				$guardian_mobileNo = rtrim($data['guardian_mobile'],",");
			}
		$mobile = array($father_mobileNo,$mother_mobileNo,$guardian_mobileNo);
		//print_r($mobile);die;
		$mb = implode(',',$mobile);
		
		$m = rtrim($mb,",");
		//print_r($m);die;
		//print_r($m);die;
		//print_r(count($mobileNo));die;
		//$mobile_nos = implode(',',$Mobile);
		// for($i=0;$i<=count($mobileNo);$i++){
		//print_r($Mobile[$i]);die;
		$url = 'http://174.143.34.193/SendSMS/BulkSMS.aspx?';
		$data = "usr=chandra@esperanzacorporate.com&pass=9966000011&msisdn=".$m."&msg=".$message."&sid=ESPRNZ&mt=0";
		//print_r($data);die;
		//echo $url.$data;die;
		$ch = curl_init();
		
		curl_setopt($ch, CURLOPT_URL, $url);
		//curl_setopt($ch, CURLOPT_POST, count($kv));
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		
		curl_setopt($ch, CURLOPT_HEADER, FALSE);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, FALSE);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
		
		$result = curl_exec($ch);
		
		curl_close($ch);
	//  }
   }
	function CampEnquiryFollowupSendMail($data="",$type="") {
		//print_r($type);die;
		$select_branch = $this->_db->select()
						->from('master_branch')
						->where("status !=?", 2)
						->where("branch_id =?",$data['branch_id']);
		$branch_data =	$this->getAdapter()
						->fetchRow($select_branch);	
						$address = $branch_data['branch_name'].'<br />'.$branch_data['branch_address'].'<br /> T:'.$branch_data['branch_phone_no1'].'<br /> M:'.$branch_data['branch_mobile_no'];
			
		//Get head office details
		$select_main = $this->_db->select()
						->from('master_main_branch')
						->where("status !=?", 2);
		$mainbranch_data = $this->getAdapter()
						->fetchRow($select_main);
		
		if($type==49){
		//print_r($type);die;
		//print_r($data['father_email']);die;		
			$branch_mails['secondary_email_address'] = $data['allbranch_email'];
			//print_r($branch_mails['secondary_email_address']);die;
			foreach($branch_mails['secondary_email_address'] as $k=>$val1){ 
			$secondary_email['secondary_email_address']=$val1;
			//echo $secondary_email['secondary_email_address'];die;
			}
			$primary_branchemail['branch_email_id']=$branch_data['branch_email_id'];
			
			$select=$this->_db->select()
						->from('master_email')
						->where("status !=?", 2)
						->where("type =?", $type);
			$result=$this->getAdapter()
                      ->fetchRow($select);	
			//print_r($data); die;
			$primary_branchemail=$data['branch_email'];
			//print_r($primary_branchemail);die;
			//$secondary_email=explode(',',$data['secondary_email']);
			//$branch_mail = array_merge($primary_branchemail,$secondary_email);
			//$branch_email_id=$primary_branchemail;
			$message =  str_replace("XXXX",$data['parent_name'],$result['description']);
			$message = str_replace("CAPTURE CAMPTYPE",$data['camptype'],$message);
			$message = str_replace("CAPTURE COUNSELOR NAME",$data['counselor'],$message);
			$message = str_replace("CAPTURE DD-MM-YY",$data['date'],$message);
			$message = str_replace("HH:MM:SS",$data['time'],$message);
			$message = str_replace("CAPTURE CHILD NAME",$data['child_name'],$message);
			$message = str_replace("CAPTURE CAMP YEAR",$data['campyear'],$message);
			$message = str_replace("CAPTURE BRANCH NAME",$branch_data['branch_name'],$message);
			$message = str_replace("CAPTURE CAMP TYPE",$data['camptype'],$message);
			$message = str_replace("CAPTURE BRANCH TEAM ",$branch_data['branch_name'],$message);
			$message = str_replace("CAPTURE BRANCH ADDRESS",$branch_data['branch_address'],$message);
			$subject=  str_replace("CAPTURE CAMPTYPE",$data['camptype'], $result['subject']);
			//print_r($message);die;
			if(!empty($data['father_email'])){
			$mother= rtrim($data['father_email'],","); 
			$father_email  = explode(',',$mother);
			//$mother = explode(',',$data['father_email']);
			}		
			if(!empty($data['mother_email'])){
			$mother= rtrim($data['mother_email'],","); 
			$mother_email  = explode(',',$mother);
			//$mother = explode(',',$data['mother_email']);
			}
			if(!empty($data['guardian_email'])){
						
			///$guardian = explode(',',$data['guardian_email']);

			$guardian = rtrim($data['guardian_email'],","); 
			$guardian_email = explode(',',$guardian);
			}
			$emails= array();
			if(is_array($father_email))
				$emails= array_merge($emails, $father_email);
			if(is_array($mother_email))
				$emails= array_merge($emails, $mother_email);
			if(is_array($guardian_email))
				$emails= array_merge($emails, $guardian_email);
		
		}	
		

		//$father_mail=explode(',',$data['father_email']);
		//print_r($father_mail);die;
		//$mother_mail=explode(',',$data['mother_email']);
		//$guardian_mail=explode(',',$data['guardian_email']);
		//$Email = array_merge($father_mail,$mother_mail,$guardian_mail);
		//$to = $Email;
		//echo $to; die;
	foreach($emails as $k => $to){
	    //print_r($to);die;
		$url = 'https://api.sendgrid.com/';
		$user = 'chandravasireddi';
		$pass = 'tissot123';
		
		$json_string = array(
		
		  'to' => array(
		    $to//$data['email']
		  ),
		  'category' => 'test_category'
		);
		
		$filePath = $_SERVER['DOCUMENT_ROOT'].'/'.'public';
		$fileName = "doc-att.zip";
		//$filePath = dirname(__FILE__);
		$params = array(
		    'api_user'  => $user,
		    'api_key'   => $pass,
		    'x-smtpapi' => json_encode($json_string),
		    'to'        => $to,
		    'subject'   => $subject,
		    'html'      => $message,
		    'text'      => 'testing body',
		    'from'      => 'espiranza@gmail.com', 'Espiranza',
		    'files['.$fileName.']' => '@'.$filePath.'/'.$fileName
		  );
		//print_r($params);
		
		$request =  $url.'api/mail.send.json';

		// Generate curl request
		$session = curl_init($request);
		
		// Tell curl to use HTTP POST
		curl_setopt ($session, CURLOPT_POST, true);
		
		// Tell curl that this is the body of the POST
		curl_setopt ($session, CURLOPT_POSTFIELDS, $params);
		
		// Tell curl not to return headers, but do return the response
		curl_setopt($session, CURLOPT_HEADER, false);
		// Tell PHP not to use SSLv3 (instead opting for TLS)
		curl_setopt($session, CURLOPT_SSLVERSION, CURL_SSLVERSION_TLSv1_2);
		curl_setopt($session, CURLOPT_RETURNTRANSFER, true);
		
		// obtain response
		$response = curl_exec($session);
		curl_close($session);	
		}
    }
	function campFollowUpSendSMS($data="", $type="") {
		//print_r($data);die;
		$select_branch = $this->_db->select()
						->from('master_branch')
						->where("status !=?", 2)
						->where("branch_id =?", $data['branch_id']);
		$branch_data =	$this->getAdapter()
						->fetchRow($select_branch);	
						$address = $branch_data['branch_name'].'<br />'.$branch_data['branch_address'].'<br /> T:'.$branch_data['branch_phone_no1'].'<br /> M:'.$branch_data['branch_mobile_no'];
						
	   if($type == 49)
		{		
			$select=$this->_db->select()
						->from('master_sms')
						->where("sms_type =?", $type);
						//->where("type =?", $type);
			$result=$this->getAdapter()
                      ->fetchRow($select);
					  
			$message = str_replace("XXXX", $data['parent_name'], $result['description']);
			$message = str_replace("CAPTURE COUNSELOR NAME", $data['counselor'], $message);
			$message = str_replace("CAPTURE DD-MM-YY", $data['date'], $message);
			$message = str_replace("HH:MM:SS",$data['time'],$message);
			$message = str_replace("CAPTURE CHILD NAME",$data['child_name'],$message);
			$message = str_replace("CAPTURE CAMPTYPE",$data['camptype'],$message);
			$message = str_replace("CAPTURE BRANCH NAME",$branch_data['branch_name'],$message);
			$message = str_replace("CAPTURE CAMP YEAR",$data['campyear'],$message);
			$message = str_replace("CAPTURE CAMP TYPE",$data['camptype'],$message);
			$message = str_replace("CAPTURE BRANCH",$branch_data['branch_name'], $message);
			//print_r($message);die;
		}
		
			$father_mobileNo = '';
			$mother_mobileNo = '';
			$guardian_mobileNo = '';
		    if(!empty($data['father_mobile'])){
				//$father_mobileNo = $data['father_mobile']; 
				$father_mobileNo = rtrim($data['father_mobile'],",");
				//print_r($father_mobileNo);die;
			}if(!empty($data['mother_mobile'])){
				//$mother_mobileNo = $data['mother_mobile']; 
				$mother_mobileNo = rtrim($data['mother_mobile'],",");
			}if(!empty($data['guardian_mobile'])){
				//$guardian_mobileNo = $data['guardian_mobile']; 
				$guardian_mobileNo = rtrim($data['guardian_mobile'],",");
			}
		$mobile = array($father_mobileNo,$mother_mobileNo,$guardian_mobileNo);
		//print_r($mobile);die;
		$mb = implode(',',$mobile);
		
		$m = rtrim($mb,",");
		//print_r($m);die;
		$url = 'http://174.143.34.193/SendSMS/BulkSMS.aspx?';
		$data = "usr=chandra@esperanzacorporate.com&pass=9966000011&msisdn=".$m."&msg=".$message."&sid=ESPRNZ&mt=0";
		//print_r($data);die;
		//echo $url.$data;die;
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		//curl_setopt($ch, CURLOPT_POST, count($kv));
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		
		curl_setopt($ch, CURLOPT_HEADER, FALSE);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, FALSE);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
		
		$result = curl_exec($ch);
		
		curl_close($ch);
		
  }
	function CampFeequoteSendMail($data="",$type="") {
		
		$select_branch = $this->_db->select()
						->from('master_branch')
						->where("status !=?", 2)
						->where("branch_id =?", $data['branch_id']);
		$branch_data =	$this->getAdapter()
						->fetchRow($select_branch);	
						$address = $branch_data['branch_name'].'<br />'.$branch_data['branch_address'].'<br /> T:'.$branch_data['branch_phone_no1'].'<br /> M:'.$branch_data['branch_mobile_no'];
	   //print_r($branch_data);die;			
		//Get head office details
		$select_main = $this->_db->select()
						->from('master_main_branch')
						->where("status !=?", 2);
		$mainbranch_data = $this->getAdapter()
						->fetchRow($select_main);
		
		if($type==50){
		    //print_r($branch_data['branch_address']);die;
			$branch_mails['secondary_email_address'] = $data['secondary_email'];
			//print_r($branch_mails['secondary_email_address']);die;
			foreach($branch_mails['secondary_email_address'] as $k=>$val1){ 
			$secondary_email['secondary_email_address']=$val1;
			//echo $secondary_email['secondary_email_address'];die;
			}
			$primary_branchemail['branch_email_id']=$branch_data['primary_email_address'];
			//print_r($primary_branchemail['branch_email_id']);die;
			$select=$this->_db->select()
						->from('master_email')
						->where("status !=?", 2)
						->where("type =?", $type);
			$result=$this->getAdapter()
                      ->fetchRow($select);

			//$primary_branchemail=$data['branch_email'];
			//print_r($primary_branchemail);die;
			//$secondary_email=explode(',',$data['secondary_email']);
			//$branch_mail = $primary_branchemail;
			//print_r($branch_mail);die;
			//$branch_mails=implode(',',$data['secondary_email']);
			//$branch_emailid=$primary_branchemail.','.$branch_mails;
			//$branch_emailid=$primary_branchemail;
			//print_r($data['branch_phone_no']);die;
			$select=$this->_db->select()
						->from('master_email')
						->where("status !=?", 2)
						->where("type =?", $type);
			$result=$this->getAdapter()
                      ->fetchRow($select);	
			//print_r($data); die;
			//print_r($data['camptype']);die;
			$message =  str_replace("XXXX",$data['parent_name'],$result['description']);
			$message = str_replace("CAPTURE BRANCHNAME",$branch_data['branch_name'],$message);
			$message = str_replace("CAPTURE CAMP TYPE",$data['camptype'],$message);
			$message = str_replace("CAPTURE ALL BRANCH EMAIL ID",$primary_branchemail['branch_email_id'].','.$secondary_email['secondary_email_address'],$message);
			$message = str_replace("CAPTURE ALL BRANCH PHONE NUMBER",$branch_data['branch_phone_no1'].','.$branch_data['branch_mobile_no'],$message);
			$message = str_replace("CAPTURE BRANCH",$branch_data['branch_name'],$message);
			$message = str_replace("CAPTURE ADDRESS",$branch_data['branch_address'],$message);
			$subject=  str_replace("CAPTURE CAMP TYPE",$data['camptype'],$result['subject']);
			$subject=  str_replace("CAPTURE BRANCHNAME",$branch_data['branch_name'],$subject);
		//print_r($message);die;	
		if(!empty($data['father_email'])){
		$father= rtrim($data['father_email'],","); 
		$father_email  = explode(',',$father);
		//$mother = explode(',',$data['father_email']);
		}		
		if(!empty($data['mother_email'])){
		$mother= rtrim($data['mother_email'],","); 
		$mother_email  = explode(',',$mother);
		//$mother = explode(',',$data['mother_email']);
		}
		if(!empty($data['guardian_email'])){
					
		///$guardian = explode(',',$data['guardian_email']);

		$guardian = rtrim($data['guardian_email'],","); 
		$guardian_email = explode(',',$guardian);
		}
		$emails= array();
		if(is_array($father_email))
			$emails= array_merge($emails, $father_email);
		if(is_array($mother_email))
			$emails= array_merge($emails, $mother_email);
		if(is_array($guardian_email))
			$emails= array_merge($emails, $guardian_email);
	}	
		//print_r($message);die;
		//$father_mail=explode(',',$data['father_email_id']);
		//$mother_mail=explode(',',$data['mother_email']);
		//$guardian_mail=explode(',',$data['guardian_email']);	
		//$Email = array_merge($father_mail,$mother_mail,$guardian_mail);
		//print_r($Email);die;
		//$to = $Email;
		//echo $to; die;
		foreach($emails as $k => $to){
		//print_r($to);die;
		$url = 'https://api.sendgrid.com/';
		$user = 'chandravasireddi';
		$pass = 'tissot123';
		
		$json_string = array(
		
		  'to' => array(
		    $to//$data['email']
		  ),
		  'category' => 'test_category'
		);
		
		$filePath = $_SERVER['DOCUMENT_ROOT'].'/'.'public';
		$fileName = "doc-att.zip";
		//$filePath = dirname(__FILE__);
		$params = array(
		    'api_user'  => $user,
		    'api_key'   => $pass,
		    'x-smtpapi' => json_encode($json_string),
		    'to'        => $to,
		    'subject'   => $subject,
		    'html'      => $message,
		    'text'      => 'testing body',
		    'from'      => 'espiranza@gmail.com', 'Espiranza',
		    'files['.$fileName.']' => '@'.$filePath.'/'.$fileName
		  );
		//print_r($params);
		
		$request =  $url.'api/mail.send.json';

		// Generate curl request
		$session = curl_init($request);
		
		// Tell curl to use HTTP POST
		curl_setopt ($session, CURLOPT_POST, true);
		
		// Tell curl that this is the body of the POST
		curl_setopt ($session, CURLOPT_POSTFIELDS, $params);
		
		// Tell curl not to return headers, but do return the response
		curl_setopt($session, CURLOPT_HEADER, false);
		// Tell PHP not to use SSLv3 (instead opting for TLS)
		curl_setopt($session, CURLOPT_SSLVERSION, CURL_SSLVERSION_TLSv1_2);
		curl_setopt($session, CURLOPT_RETURNTRANSFER, true);
		
		// obtain response
		$response = curl_exec($session);
		curl_close($session);
		//print_r($mailSend_status);die;
		}
    }
	
	function campFeeQuoteSMS($data="", $type="") {
		//print_r($data);die;
		
		$select_branch = $this->_db->select()
						->from('master_branch')
						->where("status !=?", 2)
						->where("branch_id =?", $data['branch_id']);
		$branch_data =	$this->getAdapter()
						->fetchRow($select_branch);	
						$address = $branch_data['branch_name'].'<br />'.$branch_data['branch_address'].'<br /> T:'.$branch_data['branch_phone_no1'].'<br /> M:'.$branch_data['branch_mobile_no'];
		
		
		/*  if(!empty($data['father_mobile_no'])){
				$mobileNo[] = $data['father_mobile_no']; 
			}if(!empty($data['mother_mobile_no'])){
				$mobileNo[] = $data['mother_mobile_no']; 
			}if(!empty($data['guardian_mobile_no'])){
				$mobileNo[] = $data['guardian_mobile_no']; 
			}  */
		
		//print_r($mobileNo);die;
		if($type == 50)
		{		
			$select=$this->_db->select()
						->from('master_sms')
						->where("sms_type =?", $type);
						//->where("type =?", $type);
			$result=$this->getAdapter()
                      ->fetchRow($select);
			$message = str_replace("XXXX", $data['parent_name'], $result['description']);
			$message = str_replace("CAPTURE CAMP TYPE", $data['camptype'], $message);
			$message = str_replace("CAPTURE BRANCHNAME", $branch_data['branch_name'], $message);
			$message = str_replace("CAPTURE CAMP TYPE", $data['camptype'], $message);
			$message = str_replace("CAPTURE BRANCH", $branch_data['branch_name'], $message);
			//print_r($message);die;
		}
        
			$father_mobileNo = '';
			$mother_mobileNo = '';
			$guardian_mobileNo = '';
		    if(!empty($data['father_mobile_no'])){
				//$father_mobileNo = $data['father_mobile']; 
				$father_mobileNo = rtrim($data['father_mobile_no'],",");
				//print_r($father_mobileNo);die;
			}if(!empty($data['mother_mobile_no'])){
				//$mother_mobileNo = $data['mother_mobile']; 
				$mother_mobileNo = rtrim($data['mother_mobile_no'],",");
			}if(!empty($data['guardian_mobile_no'])){
				//$guardian_mobileNo = $data['guardian_mobile']; 
				$guardian_mobileNo = rtrim($data['guardian_mobile_no'],",");
			}
		$mobile = array($father_mobileNo,$mother_mobileNo,$guardian_mobileNo);
		//print_r($mobile);die;
		$mb = implode(',',$mobile);
		//print_r($mobile);die;
		$m = rtrim($mb,",");
		//print_r($m);die;
		//$url = 'http://174.143.34.193/SendSMS/BulkSMS.aspx?';
		//echo count($mobileNo);die;
		//print_r($data);die;
		//print_r($mobileNo[$i]);die;
		$url = 'http://174.143.34.193/SendSMS/BulkSMS.aspx?';
		$data = "usr=chandra@esperanzacorporate.com&pass=9966000011&msisdn=".$m."&msg=".$message."&sid=ESPRNZ&mt=0";
		$ch = curl_init();
		
		curl_setopt($ch, CURLOPT_URL, $url);
		//curl_setopt($ch, CURLOPT_POST, count($kv));
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		
		curl_setopt($ch, CURLOPT_HEADER, FALSE);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, FALSE);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
		
		$result = curl_exec($ch);
		
		curl_close($ch);

   }
   
	function presentleftSendMail($data="",$type="") {
		if($type==41)
		{		//echo $type; die;
			$select=$this->_db->select()
						->from('master_email')
						->where("status !=?", 2)
						->where("type =?", $type);
			$result=$this->getAdapter()
                      ->fetchRow($select);	
			//print_r($data); die;
			$message = str_replace("XXXX", $data['parent_name'], $result['description']);
			$message = str_replace("CAPTURE CHILD NAME",$data['child_name'],$message);
			$message =  str_replace("CAPTURE TIMESTAMP(HH:MM AM/PM)",$data['time_stamp'],$message);
			$message = str_replace("ESPERANZA CAPTURE BRANCH TEAM",$data['branch_name'],$message);
			$message = str_replace("CAPTURE BRANCH ADDRESS",$data['branch_address'],$message);
			//$subject= "Attendance & Left Early ";
			//print_r($message); die;
		}
		if($type==40)
		{		//echo $type; die;
			$select=$this->_db->select()
						->from('master_email')
						->where("status !=?", 2)
						->where("type =?", $type);
			$result=$this->getAdapter()
                      ->fetchRow($select);	
			//print_r($data); die;
			$message = str_replace("XXXX", $data['parent_name'], $result['description']);
			$message = str_replace("CAPTURE CHILD NAME",$data['child_name'],$message);
			$message = str_replace("ESPERANZA CAPTURE BRANCH TEAM",$data['branch_name'],$message);
			$message = str_replace("CAPTURE BRANCH ADDRESS",$data['branch_address'],$message);
			//$subject= "Error in Attendance capture";
			//print_r($message); die;
		} 
			$mail='';
			if(!empty($data['father_email'])){
				$mail = $data['father_email']; 
			}elseif(!empty($data['motheremail'])){
				$mail = $data['motheremail']; 
			}elseif(!empty($data['guardian_email'])){
				$mail = $data['guardian_email']; 
			}
			$to = $mail;
		//echo $to; die;
		$url = 'https://api.sendgrid.com/';
		$user = 'chandravasireddi';
		$pass = 'tissot123';
		
		$json_string = array(
		
		  'to' => array(
		    $to//$data['email']
		  ),
		  'category' => 'test_category'
		);
		if($type == 41){
        $subject=$result['subject'];	
		}
		else if($type == 40){
		 $subject=$result['subject'];
		}
		$filePath = $_SERVER['DOCUMENT_ROOT'].'/'.'public';
		$fileName = "doc-att.zip";
		//$filePath = dirname(__FILE__);
		$params = array(
		    'api_user'  => $user,
		    'api_key'   => $pass,
		    'x-smtpapi' => json_encode($json_string),
		    'to'        => $to,
		    'subject'   => $subject,
		    'html'      => $message,
		    'text'      => 'testing body',
		    'from'      => $data['branch_email'],
		    'files['.$fileName.']' => '@'.$filePath.'/'.$fileName
		  );
		//print_r($params);
		
		$request =  $url.'api/mail.send.json';

		// Generate curl request
		$session = curl_init($request);
		
		// Tell curl to use HTTP POST
		curl_setopt ($session, CURLOPT_POST, true);
		
		// Tell curl that this is the body of the POST
		curl_setopt ($session, CURLOPT_POSTFIELDS, $params);
		
		// Tell curl not to return headers, but do return the response
		curl_setopt($session, CURLOPT_HEADER, false);
		// Tell PHP not to use SSLv3 (instead opting for TLS)
		curl_setopt($session, CURLOPT_SSLVERSION, CURL_SSLVERSION_TLSv1_2);
		curl_setopt($session, CURLOPT_RETURNTRANSFER, true);
		
		// obtain response
		$response = curl_exec($session);
		curl_close($session);
		
		
    }
	
	function presentleftSendSMS($data="", $type="") {
		//print_r($data);die;
		
		if(!empty($data['father_mobile_no'])){
				$mobileNo[] = $data['father_mobile_no']; 
			}if(!empty($data['mother_mobile_no'])){
				$mobileNo[] = $data['mother_mobile_no']; 
			}if(!empty($data['guardian_mobile_no'])){
				$mobileNo[] = $data['guardian_mobile_no']; 
			}
		//print_r($mobileNo);die;
		if($type == 17)
		{		
			$select=$this->_db->select()
						->from('master_sms')
						->where("sms_type =?", $type);
						//->where("type =?", $type);
			$result=$this->getAdapter()
                      ->fetchRow($select);
			$message = str_replace("XXXX", $data['parent_name'], $result['description']);
			$message = str_replace("CHILDNAME", $data['child_name'], $message);
			//print_r($message);die;
		}
		if($type == 43)
		{		
			$select=$this->_db->select()
						->from('master_sms')
						->where("sms_type =?", $type);
						//->where("type =?", $type);
			$result=$this->getAdapter()
                      ->fetchRow($select);
			$message = str_replace("XXXX", $data['parent_name'], $result['description']);
			$message = str_replace("CAPTURE CHILD NAME", $data['child_name'], $message);
			$message = str_replace("CAPTURE BRANCH", $data['branch_name'], $message);
			//print_r($message);die;
		}
        if($type == 44)
		{		
			$select=$this->_db->select()
						->from('master_sms')
						->where("sms_type =?", $type);
						//->where("type =?", $type);
			$result=$this->getAdapter()
                      ->fetchRow($select);
			$message = str_replace("XXXX", $data['parent_name'], $result['description']);
			$message = str_replace("CAPTURE CHILD NAME", $data['child_name'], $message);
			$message = str_replace("CAPTURE TIMESTAMP(HH:MM AM/PM)", $data['time_stamp'], $message);
			$message = str_replace("CAPTURE BRANCH", $data['branch_name'], $message);
			//print_r($message);die;
		}
		
		//$url = 'http://174.143.34.193/SendSMS/BulkSMS.aspx?';
		//echo count($mobileNo);die;
		//print_r($mobileNo);die;
		for($i=0;$i<count($mobileNo);$i++)
		{
		//print_r($mobileNo[$i+1]);die;
		$url = 'http://174.143.34.193/SendSMS/BulkSMS.aspx?';
		$data = "usr=chandra@esperanzacorporate.com&pass=9966000011&msisdn=".$mobileNo[$i]."&msg=".$message."&sid=ESPRNZ&mt=0";
		
		$ch = curl_init();
		
		curl_setopt($ch, CURLOPT_URL, $url);
		//curl_setopt($ch, CURLOPT_POST, count($kv));
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		
		curl_setopt($ch, CURLOPT_HEADER, FALSE);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, FALSE);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
		
		$result = curl_exec($ch);
		
		curl_close($ch);
		}
	 
   }
	function ComplaintsSendMail($data="",$type=""){

		if($type==14)
		{		//echo $type; die;
			$select=$this->_db->select()
						->from('master_email')
						->where("status !=?", 2)
						->where("type =?", $type);
			$result=$this->getAdapter()
                      ->fetchRow($select);	
			//print_r($data); die;
			$message = str_replace("XXXX", $data['parent_name'], $result['description']);
			$message = str_replace("CAPTURE COMPLAINT",$data['complaint'],$message);
			$message =  str_replace("CAPTURE",$data['complaint_id'],$message);
			//$message = str_replace("ESPERANZA CAPTURE BRANCH TEAM",$data['branch_name'],$message);
			$message = str_replace("ADDRESS",$data['branch_addr'],$message);
			$subject=  str_replace("Capture Complaint type",$data['complaint_type'],$result['subject']);
			//print_r($data['father_email']); die;
if(!empty($data['father_email'])){
				///$email = explode(',',$data['father_email']);
$email = rtrim($data['father_email'],","); 
$father_email = explode(',',$email);
}if(!empty($data['mother_email'])){
$mother= rtrim($data['mother_email'],","); 
$mother_email = explode(',',$mother);
}
if(!empty($data['guardian_email'])){
			

			///$guardian = explode(',',$data['guardian_email']);

$guardian = rtrim($data['guardian_email'],","); 
$guardian_email = explode(',',$guardian);
}

				
			$emails= array();
if(is_array($father_email))
    $emails= array_merge($emails, $father_email);
if(is_array($mother_email))
    $emails= array_merge($emails, $mother_email);
if(is_array($guardian_email))
 $emails= array_merge($emails, $guardian_email);
//print_r($emails); die;
		}
		if($type==24)
		{		//echo $type; die;
			$select=$this->_db->select()
						->from('master_email')
						->where("status !=?", 2)
						->where("type =?", $type);
			$result=$this->getAdapter()
                      ->fetchRow($select);	
			//print_r($data); die;
			$message = str_replace("XXXX", $data['parent_name'], $result['description']);
			$message = str_replace("CAPTURE COMPLAINT",$data['complaint'],$message);
			$message =  str_replace("CAPTURE",$data['complaint_id'],$message);
			//$message = str_replace("ESPERANZA CAPTURE BRANCH TEAM",$data['branch_name'],$message);
			$message = str_replace("ADDRESS",$data['branch_addr'],$message);
			$subject=  str_replace("Capture Complaint type",$data['complaint_type'],$result['subject']);
			//print_r($data['father_email']); die;
if(!empty($data['father_email'])){
				///$email = explode(',',$data['father_email']);
$email = rtrim($data['father_email'],","); 
$father_email = explode(',',$email);
}if(!empty($data['mother_email'])){
$mother= rtrim($data['mother_email'],","); 
$mother_email = explode(',',$mother);
}
if(!empty($data['guardian_email'])){
			

			///$guardian = explode(',',$data['guardian_email']);

$guardian = rtrim($data['guardian_email'],","); 
$guardian_email = explode(',',$guardian);
}

				
			$emails= array();
if(is_array($father_email))
    $emails= array_merge($emails, $father_email);
if(is_array($mother_email))
    $emails= array_merge($emails, $mother_email);
if(is_array($guardian_email))
 $emails= array_merge($emails, $guardian_email);
//print_r($emails); die;
		}	
		
foreach($emails as $k => $to){
		//echo $to; die;
		$url = 'https://api.sendgrid.com/';
		$user = 'chandravasireddi';
		$pass = 'tissot123';
		
		$json_string = array(
		
		  'to' => array(
		    $to//$data['email']
		  ),
		  'category' => 'test_category'
		);
		
		$filePath = $_SERVER['DOCUMENT_ROOT'].'/'.'public';
		$fileName = "doc-att.zip";
		//$filePath = dirname(__FILE__);
		$params = array(
		    'api_user'  => $user,
		    'api_key'   => $pass,
		    'x-smtpapi' => json_encode($json_string),
		    'to'        => $to,
		    'subject'   => $subject,
		    'html'      => $message,
		    'text'      => 'testing body',
		    'from'      => $data['branch_email'],
		    'files['.$fileName.']' => '@'.$filePath.'/'.$fileName
		  );
//print_r($params);die;
		
		$request =  $url.'api/mail.send.json';

		// Generate curl request
		$session = curl_init($request);
		
		// Tell curl to use HTTP POST
		curl_setopt ($session, CURLOPT_POST, true);
		
		// Tell curl that this is the body of the POST
		curl_setopt ($session, CURLOPT_POSTFIELDS, $params);
		
		// Tell curl not to return headers, but do return the response
		curl_setopt($session, CURLOPT_HEADER, false);
		// Tell PHP not to use SSLv3 (instead opting for TLS)
		curl_setopt($session, CURLOPT_SSLVERSION, CURL_SSLVERSION_TLSv1_2);
		curl_setopt($session, CURLOPT_RETURNTRANSFER, true);
		
		// obtain response
		$response = curl_exec($session);
		curl_close($session);
}
		
    }
	
	function ComplaintsSendSMS($data="", $type="") {
		//print_r($data);die;
		/*$mobileNo ='';
		if(!empty($data['father_mobile_no'])){
				$mobileNo = $data['father_mobile_no']; 
			}if(!empty($data['mother_mobile_no'])){
				$mobileNo = $data['mother_mobile_no']; 
			}if(!empty($data['guardian_mobile_no'])){
				$mobileNo = $data['guardian_mobile_no']; 
			} */
		//print_r($data['guardian_mobile']);die;
		//print_r($mobileNo);die;
		if($type == 21)
		{		
			$select=$this->_db->select()
						->from('master_sms')
						->where("sms_type =?", $type);
						//->where("type =?", $type);
			$result=$this->getAdapter()
                      ->fetchRow($select);
			$message = str_replace("XXXX", $data['parent_name'], $result['description']);
			$message = str_replace("CAPTURE COMPLAINT", $data['complaint'], $message);
			$message = str_replace("CAPTURE", $data['complaint_id'], $message);
			//print_r($message);die;
		}
		
		if($type == 22)
		{		
			$select=$this->_db->select()
						->from('master_sms')
						->where("sms_type =?", $type);
						//->where("type =?", $type);
			$result=$this->getAdapter()
                      ->fetchRow($select);
			$message = str_replace("XXXX", $data['parent_name'], $result['description']);
			$message = str_replace("CAPTURE COMPLAINT", $data['complaint'], $message);
			$message = str_replace("CAPTURE", $data['complaint_id'], $message);
			//print_r($message);die;
		}
        
		//$url = 'http://174.143.34.193/SendSMS/BulkSMS.aspx?';
		//echo count($mobileNo);die;
		//print_r($data);die;
		
		//print_r($mobileNo);die;
		//print_r($data['father_mobile_no']);die;
$father_mobile = '';
		$mother_mobile = '';
		$guardian_mobile = '';
			if(!empty($data['father_mobile'])){
				$father_mobile = rtrim($data['father_mobile'],","); 
			}
			if(!empty($data['mother_mobile'])){
				$mother_mobile = rtrim($data['mother_mobile'],","); 
			}
			if(!empty($data['guardian_mobile'])){
			//echo 'dsd' ; die;
				$guardian_mobile = rtrim($data['guardian_mobile'],","); 
				//echo $guardian_mobile; die;
			} 	
			$mobile = array($father_mobile,$mother_mobile,$guardian_mobile);
			$mb = implode(',',$mobile);
			$m = rtrim($mb,",");
		$url = 'http://174.143.34.193/SendSMS/BulkSMS.aspx?';
		//echo $url;
	$data = "usr=chandra@esperanzacorporate.com&pass=9966000011&msisdn=".$m."&msg=".$message."&sid=ESPRNZ&mt=0";
	//print_r($data);
	//echo $url.$data;
		$ch = curl_init();
		
		curl_setopt($ch, CURLOPT_URL, $url);
		//curl_setopt($ch, CURLOPT_POST, count($kv));
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		
		curl_setopt($ch, CURLOPT_HEADER, FALSE);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, FALSE);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
		
		$result = curl_exec($ch);
		
		curl_close($ch);
	 
   }
	function suggestionsSendMail($data="",$type="") {
		if($type==13)
		{		//echo $type; die;
			$select=$this->_db->select()
						->from('master_email')
						->where("status !=?", 2)
						->where("type =?", $type);
			$result=$this->getAdapter()
                      ->fetchRow($select);	
			//print_r($data); die;
			$message = str_replace("XXXX", $data['parent_name'], $result['description']);
			$message = str_replace("CAPTURE SUGGESTION",$data['suggestion'],$message);
			$message =  str_replace("CAPTURE",$data['suggestion_id'],$message);
			$message = str_replace("ADDRESS",$data['branch_address'],$message);
			$subject=  str_replace("Capture Suggestion type",$data['suggestion_type'],$result['subject']);
			//print_r($message); die;
		
		if(!empty($data['father_email'])){
		    //print_r($data['father_email']);die;
			$father=rtrim($data['father_email'],",");
            //print_r($father);die;			
		    $father_email = explode(',',$father);
		   

			//print_r($father_email);die;
			//$mother = explode(',',$data['father_email']);
			//print_r($father_email);die;
			}		
			if(!empty($data['mother_email'])){
			$mother= rtrim($data['mother_email'],","); 
			$mother_email  = explode(',',$mother);
			//$mother = explode(',',$data['mother_email']);
			}
			
			if(!empty($data['guardian_email'])){
						
			//$guardian = explode(',',$data['guardian_email']);

			$guardian = rtrim($data['guardian_email'],","); 
			$guardian_email = explode(',',$guardian);
			}
			$emails= array();
			if(is_array($father_email))
				$emails= array_merge($emails, $father_email);
			if(is_array($mother_email))
				$emails= array_merge($emails, $mother_email);
			if(is_array($guardian_email))
				$emails= array_merge($emails, $guardian_email);
		
		}
		

		//$father_mail=explode(',',$data['father_email']);
		//print_r($father_mail);die;
		//$mother_mail=explode(',',$data['mother_email']);
		//$guardian_mail=explode(',',$data['guardian_email']);
		//$Email = array_merge($father_mail,$mother_mail,$guardian_mail);
		//$to = $Email;
		//echo $to; die;
		
	foreach($emails as $k => $to){
	    //print_r($to);die;
		$url = 'https://api.sendgrid.com/';
		$user = 'chandravasireddi';
		$pass = 'tissot123';
		
		$json_string = array(
		
		  'to' => array(
		    $to//$data['email']
		  ),
		  'category' => 'test_category'
		);
		
		$filePath = $_SERVER['DOCUMENT_ROOT'].'/'.'public';
		$fileName = "doc-att.zip";
		//$filePath = dirname(__FILE__);
		$params = array(
		    'api_user'  => $user,
		    'api_key'   => $pass,
		    'x-smtpapi' => json_encode($json_string),
		    'to'        => $to,
		    'subject'   => $subject,
		    'html'      => $message,
		    'text'      => 'testing body',
		    'from'      => $primary_branchemail,
		    'files['.$fileName.']' => '@'.$filePath.'/'.$fileName
		  );
		//print_r($params);
		
		$request =  $url.'api/mail.send.json';

		// Generate curl request
		$session = curl_init($request);
		
		// Tell curl to use HTTP POST
		curl_setopt ($session, CURLOPT_POST, true);
		
		// Tell curl that this is the body of the POST
		curl_setopt ($session, CURLOPT_POSTFIELDS, $params);
		
		// Tell curl not to return headers, but do return the response
		curl_setopt($session, CURLOPT_HEADER, false);
		// Tell PHP not to use SSLv3 (instead opting for TLS)
		curl_setopt($session, CURLOPT_SSLVERSION, CURL_SSLVERSION_TLSv1_2);
		curl_setopt($session, CURLOPT_RETURNTRANSFER, true);
		
		// obtain response
		$response = curl_exec($session);
		curl_close($session);	
		}
    }	
	function suggestionsSendSMS($data="", $type="") {
		//print_r($data);die;
		
		/*if(!empty($data['father_mobile_no'])){
				$mobileNo[] = $data['father_mobile_no']; 
			}if(!empty($data['mother_mobile_no'])){
				$mobileNo[] = $data['mother_mobile_no']; 
			}if(!empty($data['guardian_mobile_no'])){
				$mobileNo[] = $data['guardian_mobile_no']; 
			}*/
			//print_r($data['father_mobile']);die;
		//print_r($mobileNo);die;
		if($type == 20)
		{		
			$select=$this->_db->select()
						->from('master_sms')
						->where("sms_type =?", $type);
						//->where("type =?", $type);
			$result=$this->getAdapter()
                      ->fetchRow($select);
			$message = str_replace("XXXX", $data['parent_name'], $result['description']);
			$message = str_replace("CAPTURE SUGGESTION", $data['suggestion'], $message);
			$message = str_replace("CAPTURE", $data['suggestion_id'], $message);
			//print_r($message);die;
		}
        
		//$url = 'http://174.143.34.193/SendSMS/BulkSMS.aspx?';
		//echo count($mobileNo);die;
		//print_r($data);die;
		$father_mobile = '';
		$mother_mobile = '';
		$guardian_mobile = '';
			if(!empty($data['father_mobile'])){
				$father_mobile = rtrim($data['father_mobile'],","); 
			}
			if(!empty($data['mother_mobile'])){
				$mother_mobile = rtrim($data['mother_mobile'],","); 
			}
			if(!empty($data['guardian_mobile'])){
			//echo 'dsd' ; die;
				$guardian_mobile = rtrim($data['guardian_mobile'],","); 
				//echo $guardian_mobile; die;
			} 	
			$mobile = array($father_mobile,$mother_mobile,$guardian_mobile);
			$mb = implode(',',$mobile);
			$m = rtrim($mb,",");
		$url = 'http://174.143.34.193/SendSMS/BulkSMS.aspx?';
		$data = "usr=chandra@esperanzacorporate.com&pass=9966000011&msisdn=".$m."&msg=".$message."&sid=ESPRNZ&mt=0";
		//print_r($data);
		$ch = curl_init();
		
		curl_setopt($ch, CURLOPT_URL, $url);
		//curl_setopt($ch, CURLOPT_POST, count($kv));
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		
		curl_setopt($ch, CURLOPT_HEADER, FALSE);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, FALSE);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
		
		$result = curl_exec($ch);
		
		curl_close($ch);
		
	 
   }

   function FoodUpdateItemSendMail($data="") {
		
			//echo $type; die;
			/* $select=$this->_db->select()
						->from('master_email')
						->where("status !=?", 2)
						//->where("type =?", $type);
			$result=$this->getAdapter()
                      ->fetchRow($select);	*/
			//print_r($data); die;
			$message = str_replace("Item is changed");
			$subject=  str_replace("Today's Menu Item changed",  $data['items_name']);
			//print_r($message); die;
		
		
		//print_r($Email);die;
			$to = $data['branch_email'];
		//echo $to; die;
		$url = 'https://api.sendgrid.com/';
		$user = 'chandravasireddi';
		$pass = 'tissot123';
		
		$json_string = array(
		
		  'to' => array(
		    $to//$data['email']
		  ),
		  'category' => 'test_category'
		);
		
		$filePath = $_SERVER['DOCUMENT_ROOT'].'/'.'public';
		$fileName = "doc-att.zip";
		//$filePath = dirname(__FILE__);
		$params = array(
		    'api_user'  => $user,
		    'api_key'   => $pass,
		    'x-smtpapi' => json_encode($json_string),
		    'to'        => $to,
		    'subject'   => $subject,
		    'html'      => $message,
		    'text'      => 'testing body',
		    'from'      => $data['branch_email'],
		    'files['.$fileName.']' => '@'.$filePath.'/'.$fileName
		  );
		//print_r($params);
		
		$request =  $url.'api/mail.send.json';

		// Generate curl request
		$session = curl_init($request);
		
		// Tell curl to use HTTP POST
		curl_setopt ($session, CURLOPT_POST, true);
		
		// Tell curl that this is the body of the POST
		curl_setopt ($session, CURLOPT_POSTFIELDS, $params);
		
		// Tell curl not to return headers, but do return the response
		curl_setopt($session, CURLOPT_HEADER, false);
		// Tell PHP not to use SSLv3 (instead opting for TLS)
		curl_setopt($session, CURLOPT_SSLVERSION, CURL_SSLVERSION_TLSv1_2);
		curl_setopt($session, CURLOPT_RETURNTRANSFER, true);
		
		// obtain response
		$response = curl_exec($session);
		curl_close($session);
		
    }
function feePaymentRecievedMail($data="", $type="") {
	//print_r($data);die;
	
		$select_branch = $this->_db->select()
						->from('master_branch')
						->where("status !=?", 2)
						->where("branch_id =?", $data['branch_id']);
		$branch_data =	$this->getAdapter()
						->fetchRow($select_branch);	
		$address = $branch_data['branch_name'].'<br />'.$branch_data['branch_address'].'<br /> T:'.$branch_data['branch_phone_no1'].'<br /> M:'.$branch_data['branch_mobile_no'];
		$branch_mail1 = $data['branch_email'];
		$branch_mail2 = $data['branch_secondary_mail'];
		$branch_email = array($branch_mail1,$branch_mail2);
		$branch_mobile_no1 = $data['branch_mobile'];
		$branch_mobile_no2 = $data['branch_secondary_mobile_no'];
		$branch_mobile = array($branch_mobile_no1,$branch_mobile_no2);
        if($type==55)
		{		
			$select=$this->_db->select()
						->from('master_email')
						->where("status !=?", 2)
						->where("type =?", $type);
			$result=$this->getAdapter()
                      ->fetchRow($select);			
			$message = str_replace("XXXX", $data['parent_name'], $result['description']);				
			$message = str_replace("CAPTURE CHILD NAME", $data['child_name'], $message);
			
			$message = str_replace("CAPTURE FEE HEAD", $data['fee_heads'], $message);
			$message = str_replace(" CAPTURE ALL BRANCH NUMBERS ", $branch_mobile, $message);
			$message = str_replace("CAPTURE ALL BRANCH EMAIL ID  ", $branch_email, $message);
			$message = str_replace("CAPTURE Branch Address",$address, $message);
if(!empty($data['father_email'])){
				///$email = explode(',',$data['father_email']);
$father = rtrim($data['father_email'],","); 
$father_email = explode(',',$father);
//$father = array($father_email);
}
if(!empty($data['mother_mail'])){
$mother= rtrim($data['mother_mail'],","); 
$mother_email  = explode(',',$mother);
//$mother = explode(',',$data['mother_mail']);
}
if(!empty($data['guardian_email'])){
			

			///$guardian = explode(',',$data['guardian_email']);

$guardian = rtrim($data['guardian_email'],","); 
$guardian_email = explode(',',$guardian);
}
$emails= array();
if(is_array($father_email))
    $emails= array_merge($emails, $father_email);
if(is_array($mother_email))
    $emails= array_merge($emails, $mother_email);
if(is_array($guardian_email))
    $emails= array_merge($emails, $guardian_email);

$subject =  str_replace("CAPTURE FEE HEAD", $data['fee_heads'], $result['subject']);
			
		}
		
		//$message = 'you are registered successfully';
foreach($emails as $k => $to){
		
		$url = 'https://api.sendgrid.com/';
		$user = 'chandravasireddi';
		$pass = 'tissot123';
		
		$json_string = array(
		
		  'to' => array(
		    $to//$data['email']
		  ),
		  'category' => 'test_category'
		);
		
		//$filePath = $_SERVER['DOCUMENT_ROOT'].'/'.'public';
		//$fileName = "doc-att.zip";
		//$filePath = dirname(__FILE__);
		$params = array(
		    'api_user'  => $user,
		    'api_key'   => $pass,
		    'x-smtpapi' => json_encode($json_string),
		    'to'        => $to,
		    'subject'   => $subject,
		    'html'      => $message,
		    'text'      => 'testing body',
		    'from'      => $data['branch_email']
		    //'files['.$fileName.']' => '@'.$filePath.'/'.$fileName
		  );
		//print_r($params);
		
		$request =  $url.'api/mail.send.json';

		// Generate curl request
		$session = curl_init($request);
		
		// Tell curl to use HTTP POST
		curl_setopt ($session, CURLOPT_POST, true);
		
		// Tell curl that this is the body of the POST
		curl_setopt ($session, CURLOPT_POSTFIELDS, $params);
		
		// Tell curl not to return headers, but do return the response
		curl_setopt($session, CURLOPT_HEADER, false);
		// Tell PHP not to use SSLv3 (instead opting for TLS)
		curl_setopt($session, CURLOPT_SSLVERSION, CURL_SSLVERSION_TLSv1_2);
		curl_setopt($session, CURLOPT_RETURNTRANSFER, true);
		
		// obtain response
		$response = curl_exec($session);
		curl_close($session);
		//die;
}
    }
	function feePaymentRecievedSms($data="", $type="") {
		//print_r($data);die;
        if($type == 55)
		{		
			$select=$this->_db->select()
						->from('master_sms')
						->where("sms_type =?", $type);
						//->where("type =?", $type);
			$result=$this->getAdapter()
                      ->fetchRow($select);
			$message = str_replace("XXXX", $data['parent_name'], $result['description']);
			$message = str_replace("CAPTURE CHILD NAME", $data['child_name'], $message);
			
			$message = str_replace("CAPTURE FEE HEAD", $data['fee_heads'], $message);
		}
		
		
		$father_mobile = '';
		$mother_mobile = '';
		$guardian_mobile = '';
			if(!empty($data['father_mobile'])){
				$father_mobile = rtrim($data['father_mobile'],","); 
			}
			if(!empty($data['mother_mobile'])){
				$mother_mobile = rtrim($data['mother_mobile'],","); 
			}
			if(!empty($data['guardian_mobile'])){
			//echo 'dsd' ; die;
				$guardian_mobile = rtrim($data['guardian_mobile'],","); 
				//echo $guardian_mobile; die;
			} 	
			$mobile = array($father_mobile,$mother_mobile,$guardian_mobile);
			$mb = implode(',',$mobile);
			$m = rtrim($mb,",");
		$url = 'http://174.143.34.193/SendSMS/BulkSMS.aspx?';
		$data = "usr=chandra@esperanzacorporate.com&pass=9966000011&msisdn=".$m."&msg=".$message."&sid=ESPRNZ&mt=0";

		$ch = curl_init();
		
		curl_setopt($ch, CURLOPT_URL, $url);
		//curl_setopt($ch, CURLOPT_POST, count($kv));
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		
		curl_setopt($ch, CURLOPT_HEADER, FALSE);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, FALSE);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
		
		$result = curl_exec($ch);
		
		curl_close($ch);
		
		
		
	
    }
function AdmissionFeeQuote($data="", $type="") {
	
	
		$select_branch = $this->_db->select()
						->from('master_branch')
						->where("status !=?", 2)
						->where("branch_id =?", $data['branch_id']);
		$branch_data =	$this->getAdapter()
						->fetchRow($select_branch);	
		$address = $branch_data['branch_name'].'<br />'.$branch_data['branch_address'].'<br /> T:'.$branch_data['branch_phone_no1'].'<br /> M:'.$branch_data['branch_mobile_no'];
		$branch_mail1 = $data['branch_email'];
		$branch_mail2 = $data['branch_secondary_mail'];
		$branch_email = array($branch_mail1,$branch_mail2);
		$branch_mobile_no1 = $data['branch_mobile'];
		$branch_mobile_no2 = $data['branch_secondary_mobile_no'];
		$branch_mobile = array($branch_mobile_no1,$branch_mobile_no2);
        if($type==44)
		{		
			$select=$this->_db->select()
						->from('master_email')
						->where("status !=?", 2)
						->where("type =?", $type);
			$result=$this->getAdapter()
                      ->fetchRow($select);			
			$message = str_replace("XXXX", $data['parent_name'], $result['description']);				
			$message = str_replace("CAPTURE CHILD NAME", $data['child_name'], $message);
			$message = str_replace("CAPTURE ALL BRANCH EMAIL ID", $branch_email, $message);
			$message = str_replace("CAPTURE ALL BRANCH PHONE NUMBER", $branch_mobile, $message);
			
			//$to ='tissailaja@gmail.com';
			if(!empty($data['father_email'])){
				///$email = explode(',',$data['father_email']);
			$email = rtrim($data['father_email'],","); 
			}
			if(!empty($data['mother_email'])){
			$mother= rtrim($data['mother_email'],","); 
			//$mother = explode(',',$data['mother_email']);
			}
			if(!empty($data['guardian_email'])){
						

						///$guardian = explode(',',$data['guardian_email']);

			$guardian = rtrim($data['guardian_email'],","); 
			}

				
			$emails= array($email ,$mother,$guardian);


		}
		//$to  = "tissailaja@gmail.com";
		//$message = 'you are registered successfully';
		foreach($emails as $k => $to){
		$url = 'https://api.sendgrid.com/';
		$user = 'chandravasireddi';
		$pass = 'tissot123';
		
		$json_string = array(
		
		  'to' => array(
		    $to//$data['email']
		  ),
		  'category' => 'test_category'
		);
		
		$filePath = $_SERVER['DOCUMENT_ROOT'].'/'.'application'.'/'.'views'.'/'.'scripts'.'/'.'quotation';
///print_r($filePath); die;
		$fileName = "ajax-get-count.phtml";

		//$filePath = dirname(__FILE__);
		$params = array(
		    'api_user'  => $user,
		    'api_key'   => $pass,
		    'x-smtpapi' => json_encode($json_string),
		    'to'        => $to,
		    'subject'   => $result['subject'],
		    'html'      => $message,
		    'text'      => 'testing body',
		    'from'      => $data['branch_email'],
		    'files['.$fileName.']' => '@'.$filePath.'/'.$fileName
		  );
		//print_r($params);die;
		
		$request =  $url.'api/mail.send.json';

		// Generate curl request
		$session = curl_init($request);
		
		// Tell curl to use HTTP POST
		curl_setopt ($session, CURLOPT_POST, true);
		
		// Tell curl that this is the body of the POST
		curl_setopt ($session, CURLOPT_POSTFIELDS, $params);
		
		// Tell curl not to return headers, but do return the response
		curl_setopt($session, CURLOPT_HEADER, false);
		// Tell PHP not to use SSLv3 (instead opting for TLS)
		curl_setopt($session, CURLOPT_SSLVERSION, 'CURL_SSLVERSION_TLSv1_2');
		curl_setopt($session, CURLOPT_RETURNTRANSFER, true);
		
		// obtain response
		$response = curl_exec($session);
		//print_r($response);  die;
		curl_close($session);
		
		///die;
		}
    }
function SendOtpSms($data){

			//print_r($data); die;
			$message = "Your OTP Password is ********";
			$message = str_replace("********", $data['otp_password'], $message);
			
		$url = 'http://174.143.34.193/SendSMS/BulkSMS.aspx?';
		$data = "usr=chandra@esperanzacorporate.com&pass=9966000011&msisdn=".$data['mobile_number'].",".$data['alternate_mobile_number']."&msg=".$message."&sid=ESPRNZ&mt=0";		
		//echo $data; die;
		$ch = curl_init();
		
		curl_setopt($ch, CURLOPT_URL, $url);
		//curl_setopt($ch, CURLOPT_POST, count($kv));
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		
		curl_setopt($ch, CURLOPT_HEADER, FALSE);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, FALSE);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
		
		$result = curl_exec($ch);
		
		curl_close($ch); 

}
	 
    function sendGpsLocationSMS($data="") {
	//print_r($data);die;
		$select_branch = $this->_db->select()
						->from('master_branch')
						->where("status !=?", 2)
						->where("branch_id =?", $data['branch_id']);
		$branch_data =	$this->getAdapter()
						->fetchRow($select_branch);	
						$address = $branch_data['branch_name'].'<br />'.$branch_data['branch_address'].'<br /> T:'.$branch_data['branch_phone_no1'].'<br /> M:'.$branch_data['branch_mobile_no'];
		
     		$result = "Dear Mr/Ms XXXX, 
					         This is to inform that CAPTURE CAMPTYPE in Camp Admission Number CAMPADMISSION ID of STUDENT NAME is Present Location at LOCATIONADDR.This information is send from ESPERANZA CAPTURE BRANCH NAME TEAM.And also get the information CAPTUREBRANCH PHONENUMBER.<br>
							 ESP CAPTURE BRANCH TEAM <br>
							 BRANCHADDRES.";
							  
			
			$message = str_replace("XXXX", $data['parent_name'], $result);
			$message = str_replace("STUDENT NAME", $data['child_name'], $message);
			$message = str_replace("CAPTURE CAMPTYPE", $data['camp_type'], $message);
			$message = str_replace("CAPTURE BRANCH NAME",$branch_data['branch_name'], $message);
			//$message = str_replace("ADDRESS",$data['address'], $message);
			$message = str_replace("LOCATIONADDR",$data['location_addr'],$message);
			$message = str_replace("CAMPADMISSION ID",$data['admission_number'], $message);
			$message = str_replace("CAPTURE BRANCH",$branch_data['branch_name'], $message);
			$message = str_replace("BRANCHADDRES",$branch_data['branch_address'], $message);
			$message = str_replace("CAPTUREBRANCH PHONENUMBER",$branch_data['branch_phone_no1'].','.$branch_data['branch_mobile_no'], $message);
			//print_r($message);die;

		$father_mobileNo = '';
			$mother_mobileNo = '';
			$guardian_mobileNo = '';
		    if(!empty($data['father_mobile'])){
				//$father_mobileNo = $data['father_mobile']; 
				$father_mobileNo = rtrim($data['father_mobile'],",");
				//print_r($father_mobileNo);die;
			}if(!empty($data['mother_mobile'])){
				//$mother_mobileNo = $data['mother_mobile']; 
				$mother_mobileNo = rtrim($data['mother_mobile'],",");
			}if(!empty($data['guardian_mobile'])){
				//$guardian_mobileNo = $data['guardian_mobile']; 
				$guardian_mobileNo = rtrim($data['guardian_mobile'],",");
			}
		$mobile = array($father_mobileNo,$mother_mobileNo,$guardian_mobileNo);
		
		$mb = implode(',',$mobile);
		//print_r($mb);die;
		$m = rtrim($mb,",");	
		//print_r($m);die;
		//print_r($m);die;
		//print_r(count($mobileNo));die;
		//$mobile_nos = implode(',',$Mobile);
		// for($i=0;$i<=count($mobileNo);$i++){
		//print_r($Mobile[$i]);die;
		$url = 'http://174.143.34.193/SendSMS/BulkSMS.aspx?';
		$data = "usr=chandra@esperanzacorporate.com&pass=9966000011&msisdn=".$m."&msg=".$message."&sid=ESPRNZ&mt=0";
		//echo $data;die;
//	echo $url.$data;die;
	//	echo $url.$data;die;
		$ch = curl_init();
		
		curl_setopt($ch, CURLOPT_URL, $url);
		//curl_setopt($ch, CURLOPT_POST, count($kv));
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		
		curl_setopt($ch, CURLOPT_HEADER, FALSE);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, FALSE);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
		
		$result = curl_exec($ch);
		
		curl_close($ch);

   }
   

   


}

