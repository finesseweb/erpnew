<?php
/****************************************
/*  Author 	: Kvvaradha
/*  Module 	: Extended HRM
/*  E-mail 	: admin@kvcodes.com
/*  Version : 1.0
/*  Http 	: www.kvcodes.com
*****************************************/

$page_security = 'SA_OPEN';
$path_to_root="../../";

include_once($path_to_root . "/includes/db_pager.inc");
include($path_to_root . "/includes/session.inc");
include_once($path_to_root . "/admin/db/attachments_db.inc");	
include($path_to_root . "/includes/ui.inc");
include($path_to_root . "/modules/ExtendedHRM/includes/Payroll.inc" );
include_once($path_to_root . "/includes/date_functions.inc");
include_once($path_to_root . "/includes/data_checks.inc");
include_once($path_to_root . "/includes/ui/contacts_view.inc");
$_POST['REP_ID'] = 811;
if (!@$_GET['popup'])
{
	$js = "";
	//if ($use_popup_windows)
		$js .= get_js_open_window(900, 500);
	//if ($use_date_picker)
		$js .= get_js_date_picker();
	page(("Visitors Report"), @$_GET['popup'], false, "", $js);
}

check_db_has_employees(_("There is no employee in this system. Kindly Open <a href='".$path_to_root."/modules/ExtendedHRM/manage/employees.php'>Add And Manage Employees</a> to update it"));

simple_page_mode(true);
//----------------------------------------------------------------------------------------
if (isset($_GET['first_name'])) // catch up external links
	$_POST['first_name'] = $_GET['first_name'];
if (isset($_GET['last_name']))
	$_POST['last_name'] = $_GET['last_name'];
if (isset($_GET['to_meet']))
	$_POST['to_meet'] = $_GET['to_meet'];
if (isset($_GET['coming_from']))
	$_POST['coming_from'] = $_GET['coming_from'];
if (isset($_GET['company']))
	$_POST['company'] = $_GET['company'];


/*if (isset($_GET['tr_fromdate']))
	$_POST['tr_fromdate'] = $_GET['tr_fromdate'];
if (isset($_GET['tr_todate']))
	$_POST['tr_todate'] = $_GET['tr_todate'];
if (isset($_GET['time_tr_fromdate']))
	$_POST['time_tr_fromdate'] = $_GET['time_tr_fromdate'];
if (isset($_GET['time_tr_todate']))
	$_POST['time_tr_todate'] = $_GET['time_tr_todate'];*/



//----------------------------------------------------------------------------------------
// Ajax updates
//
$ids = '';
if (get_post('Search'))
{ 
	$Ajax->activate('kv_empl_info');
}
//--------------------------------------------------------------------------------------
if (!isset($_POST['filterType']))
	$_POST['filterType'] = -1;

start_form();

start_table(TABLESTYLE_NOBORDER);
start_row();
ref_cells(_("First Name:"), 'first_name', '',null, _('Enter Employee id fragment or leave empty'));
//ref_cells(_("Last Name:"), 'last_name', '',null, _('Enter Employee Name fragment or leave empty'));
ref_cells(_("To Meet:"), 'to_meet', '',null, _('Enter Email ID fragment or leave empty'));
ref_cells(_("Coming From:"), 'coming_from', '',null, _('Enter Email ID fragment or leave empty'));
ref_cells(_("Company:"), 'company', '',null, _('Enter Email ID fragment or leave empty'));
//datetime_row(_("Check In (Date &amp; Time)*") . ":", 'tr_fromdate', 20, null, '', '', '', null, true,false);
date_row(_("Check In Date") . "", 'tr_fromdate');
//datetime_row(_("Check Out (Date &amp; Time)*") . ":", 'tr_todate', 20, null, '', '', '', null, true,false);
//date_row(_("Check In Date") . "", 'tr_fromdate');

submit_cells('Search', _("Search"), '', '', 'default');
end_row();

end_table();
if(!isset($_POST['visitor_management_page_next']))
$_SESSION['fun1']['id'] = 0;
function display_rows(){
	$sql = kv_get_visitor_list( get_post('first_name'),get_post('last_name'),get_post('to_meet'), get_post('coming_from'),get_post('company'), get_post('tr_fromdate'), get_post('tr_todate'), get_post('time_tr_fromdate'), get_post('time_tr_todate'));        unset($_SESSION['fun2']);
       
	$cols = array(
		_("Id") => array('name'=>'visitor_id','fun'=>'getid'),
		_("Ref Id") => array('name'=>'ref_id'),
		_("First Name") => array('name'=>'first_name'),
		_("Last Name") => array('name'=>'last_name'),
		_("To Meet") => array('name'=>'to_meet'),
		_("Company") => array('name'=>'company'),
	   	_("Coming From") => array('name'=>'coming_from'),  
		//_("Grade") => array('name'=>'grade'),
		_("Purpose") => array('name'=>'purpose'),
	    _("Contact Number") => array('name'=>'contact_number'),
	    _("Email") => array('name'=>'email'),
		_("Check In (Date &amp; Time)") => array('align' => 'center', 'fun' => 'checking'),
        _("Check Out (Date &amp; Time)") => array('align' => 'center', 'fun' => 'checkout'),
        _("Remarks") => array('name'=>'remarks')
		//array('')
	);	
	$table =& new_db_pager('visitor_management', $sql, $cols);
	$table->width = "90%";
	//$table->set_marker('check_contractperiod', _("List of employees whoose contract period is going to be completed before one month."));
	display_db_pager($table);
}

function getid($row){
    $_SESSION['fun2'][$_SESSION['fun1']['id']]= $row['vistitor_id'];
   return ++$_SESSION['fun1']['id'] ;
}
   
function checking($row) {
	$date = date('Y-m-d h:i A',strtotime($row['tr_fromdate']));
	return $date ;
}
function checkout($row) {
        if($row['tr_todate']){
            $date = date('Y-m-d h:i A',strtotime($row['tr_todate']));
        }else{
           $date = '---'; 
        }
	//$date = date('Y-m-d h:i A',strtotime($row['tr_todate']));
	return $date ;
}

//----------------------------------------------------------------------------------------
	start_form(true);
    if (isset($_GET['delete_id'])){} else{
            // display_warning(_(" Once you delete the Employee, The whole informations can be removed from the Database"));
    }
	display_rows();
        $idstr = (string)implode(',', $_SESSION['fun2']);
       // display_error('yy'.$idstr);
         $_SESSION['my_ids']=$idstr;
       
       // echo "<pre>"; print_r($_SESSION);
        
      
    echo "<span  class='exp_excel'><a  style='color:#000 !important' href='" . $path_to_root . "/modules/ExtendedHRM/reports/rep811.php?PARAM_0=$idstr&PARAM_3=1' target='_blank' class='printlink'> Export to Excel </a></span>";
   
	end_form();

//----------------------------------------------------------------------------------------
function kv_get_visitor_list($first_name,$last_name,$to_meet,$coming_from,$company,$tr_fromdate,$tr_todate,$tr_ftime,$tr_totime){

	//display_error($status); die;
	
    $sql ="SELECT * FROM ".TB_PREF."visitor_management WHERE 1=1";
	$check_in=date('Y-m-d', strtotime($tr_fromdate));
        $check_out=date('Y-m-d', strtotime($tr_todate));
	if ($first_name) {
		$sql .= " AND first_name LIKE ". db_escape("%$first_name%");
	}
	if ($last_name) {
		$sql .= " AND last_name LIKE ". db_escape("%$last_name%");
	}
	if ($to_meet) {
		$sql .= " AND to_meet LIKE ". db_escape("%$to_meet%");
	}
	if ($coming_from) {
		$sql .= " AND coming_from=".db_escape($coming_from);
	}
        if ($check_in) {
		//$sql .= " GETDATE tr_fromdate=".db_escape($check_in);
           // $sql .= " AND date(tr_fromdate)";
            $sql .= " AND tr_fromdate LIKE". db_escape("%$check_in%");
	}
       /* if ($check_out) {
		$sql .= " GETDATE tr_todate=".db_escape($check_out);
	}*/
	//display_error($sql);
	return $sql;
}

	
end_page();
?>
<style>
.tablestyle_noborder tr {
	float: left;
	padding: 12px;
}
.label {
    background-color: #fff;
    color: black;
}

.exp_excel{
    font-size: 11px;
    border: 1px #ccc solid;
    background-image: url(images/footer_bg.png);
    background-repeat: repeat-x;
    padding: 5px 13px;
    float:right;
   background-color: #eee;
    margin-top: 10px;

}
</style>
