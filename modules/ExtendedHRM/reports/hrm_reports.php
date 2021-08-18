<?php
/****************************************
/*  Author 	: Kvvaradha
/*  Module 	: Extended HRM
/*  E-mail 	: admin@kvcodes.com
/*  Version : 1.0
/*  Http 	: www.kvcodes.com
*****************************************/

$page_security = 'HR_REPORTS';
$path_to_root="../../..";

include($path_to_root . "/includes/session.inc");
include($path_to_root . "/modules/ExtendedHRM/includes/Payroll.inc" );
include_once($path_to_root . "/includes/data_checks.inc");
include_once($path_to_root . "/includes/ui.inc");
include_once($path_to_root . "/modules/ExtendedHRM/reports/reports_classes.inc");

$version_id = get_company_prefs('version_id');

$js = '';
if($version_id['version_id'] == '2.4.1'){
	if ($SysPrefs->use_popup_windows) 
		$js .= get_js_open_window(900, 500);	

	if (user_use_date_picker()) 
		$js .= get_js_date_picker();
	
}else{
	if ($use_popup_windows)
		$js .= get_js_open_window(900, 500);
	if ($use_date_picker)
		$js .= get_js_date_picker();
}

add_js_file('reports.js');

page(_($help_context = "Reports and Analysis"), false, false, "", $js);

check_db_has_employees(_("There is no employee in this system. Kindly Open <a href='".$path_to_root."/modules/ExtendedHRM/manage/employees.php'>Add And Manage Employees</a> to update it"));

$reports = new BoxReports;

$dim = get_company_pref('use_dimension');

$reports->addReportClass(_('HRM & Payroll'), RC_EMPLOYEES);
$reports->addReportClass(_('Allowances'), RC_ALLOWANCES);
$reports->addReport(RC_EMPLOYEES, 801, _('Employees'),
	array( 				
			_('Department') => 'DEPARTMENTS',			
			_('Comments') => 'TEXTBOX',
			_('Orientation') => 'ORIENTATION',
			_('Destination') => 'DESTINATION'));

$reports->addReport(RC_EMPLOYEES, 802, _('Payslip'),
	array(	_('Year') => 'KV_TRANS_YEARS',
			_('Months') => 'MONTHS',
			_('Employee') => 'KV_EMPLOYEES',
			_('Comments') => 'TEXTBOX',
			_('E-mail')  => 'YES_NO'));
$reports->addReport(RC_EMPLOYEES, 803, _('Payroll History'),
	array(	_('Year') => 'KV_TRANS_YEARS',
			_('Months') => 'MONTHS',
			_('Department') => 'DEPARTMENTS',
			_('Comments') => 'TEXTBOX',
			_('Destination') => 'DESTINATION'));

$reports->addReport(RC_EMPLOYEES, 804, _('Attendance'),
	array(	_('Year') => 'KV_TRANS_YEARS',
			_('Months') => 'MONTHS',
			_('Department') => 'DEPARTMENTS',	
			_('Comments') => 'TEXTBOX',
			_('Destination') => 'DESTINATION'));

$reports->addReport(RC_EMPLOYEES, 805, _('Loan'),
	array(	_('Employee') => 'KV_EMPLOYEES',	
			_('Comments') => 'TEXTBOX'));

$reports->addReport(RC_EMPLOYEES, 806, _('Monthly Summary'),
	array(	_('Year') => 'KV_TRANS_YEARS',
			_('Months') => 'MONTHS',	
			_('Comments') => 'TEXTBOX'));

$reports->addReport(RC_EMPLOYEES, 808, _('Annual Summary'),
	array(	_('Year') => 'KV_TRANS_YEARS',
			_('Comments') => 'TEXTBOX'));
			
$reports->addReport(RC_EMPLOYEES, 809, _('Holidays'),
	array(	_('Year') => 'KV_TRANS_YEARS',
			//_('Months') => 'MONTHS',
			//_('Department') => 'DEPARTMENTS',
			//_('Comments') => 'TEXTBOX',
			_('Destination') => 'DESTINATION'));
			
$reports->addReport(RC_EMPLOYEES, 810, _('Leave Request'),
	array(	//_('Year') => 'KV_TRANS_YEARS',
			//_('Months') => 'MONTHS',
			_('Department') => 'DEPARTMENTS',
			//_('Comments') => 'TEXTBOX',
			_('Destination') => 'DESTINATION')); 

$reports->addReport(RC_EMPLOYEES, 810, _('Leave Request'),
	array(	//_('Year') => 'KV_TRANS_YEARS',
			//_('Months') => 'MONTHS',
			_('Department') => 'DEPARTMENTS',
			//_('Comments') => 'TEXTBOX',
			_('Destination') => 'DESTINATION')); 
$reports->addReport(RC_EMPLOYEES, 812, _('PF Inquiry'),
	array(	//_('Year') => 'KV_TRANS_YEARS',
			//_('Months') => 'MONTHS',
			_('Department') => 'DEPARTMENTS',
			//_('Comments') => 'TEXTBOX',
			_('Destination') => 'DESTINATION')); 

			
$result = get_allowances();	

while ($myrow = db_fetch($result)) {		
	$reports->addReport(RC_ALLOWANCES, $myrow['id'], _($myrow['description']),
	array(	_('Year') => 'KV_TRANS_YEARS',
			_('Months') => 'MONTHS',			
			_('Comments') => 'TEXTBOX'));			
}		
add_custom_reports($reports);

echo $reports->getDisplay(); 
end_page(); ?>