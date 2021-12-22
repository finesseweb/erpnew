<?php
/****************************************
/*  Author  : Kvvaradha
/*  Module  : Extended HRM
/*  E-mail  : admin@kvcodes.com
/*  Version : 1.0
/*  Http    : www.kvcodes.com
*****************************************/
define ('SS_EXHRM', 250<<8);
define ('SS_EXHRM_SETTINGS', 251<<8);
define ('SS_EXHRM_PAYROLL', 252<<8);
define('SS_EXHRM_EMPLOYEE',253<<8);
define('SS_HOSTEL_MANAGEMENT',254<<8);


class ExtendedHRM_app extends application{
    var $apps;
    function __construct()  {
        //$this->application("extendedhrm", _($this->help_context = "&HRM"));
        parent::__construct("extendedhrm", _($this->help_context = "&HRM"));
       $this->add_module(_("Transactions"));
        
        $this->add_lapp_function(0, _('PaySlip Entry'), 'modules/ExtendedHRM/payslip.php', 'HR_PAYSLIP', MENU_TRANSACTION);
        
		$this->add_lapp_function(0, _('Direct Payslip'), 'modules/ExtendedHRM/payslip_detail.php', 'HR_PAYSLIP_DETAILS', MENU_TRANSACTION);
                
        $this->add_lapp_function(0, _('Payroll'), 'modules/ExtendedHRM/manage/payroll.php', 'HR_PAYROLL', MENU_TRANSACTION);
        $this->add_lapp_function(0, _('Arrear Calculation'), 'modules/ExtendedHRM/arrear_calculation.php', 'HR_ARREAR_CALCULATION', MENU_TRANSACTION);
        $this->add_lapp_function(0, _('Pay Commission Upgrade'), 'modules/ExtendedHRM/pay_commission_upgrade.php', 'HR_UPGRADE', MENU_TRANSACTION);
          $this->add_lapp_function(0, _('Arrear Calculation By Month'), 'modules/ExtendedHRM/arrear_calculation_by_month.php', 'HR_ARREAR_CALCULATION_MONTH', MENU_TRANSACTION);
          
          $this->add_lapp_function(0, _('Pay Commission Upgrade By Month'), 'modules/ExtendedHRM/pay_commission_upgrade_by_month.php', 'HR_UPGRADE_MONTH', MENU_TRANSACTION);
          
        $this->add_lapp_function(0, _('Direct Arrear Pay'), 'modules/ExtendedHRM/arrear_direct_pay.php', 'HR_PAYSLIP', MENU_TRANSACTION);
		
        $this->add_lapp_function(0, _('Attachments'), 'modules/ExtendedHRM/manage/attachments.php', 'HR_ATTACHMENT', MENU_TRANSACTION);
        $this->add_lapp_function(0, _('Attachments'), 'modules/ExtendedHRM/manage/attachments_user.php', 'HR_ATTACHMENT_VIEW', MENU_TRANSACTION);
		$this->add_rapp_function(0, _('Tour Bill Claim'), 'modules/ExtendedHRM/manage/tour_request_form.php', 'HR_TOURREQUEST', MENU_TRANSACTION);
        $this->add_rapp_function(0, _('Tour Bill List'), 'modules/ExtendedHRM/manage/tour_bill_list.php', 'HR_TOURBILL', MENU_TRANSACTION);
        
     //  $this->add_rapp_function(0, _('Attendance Entry'), 'modules/ExtendedHRM/empl_attend.php', 'HR_ATTENDANCE', MENU_TRANSACTION); 

		//$this->add_rapp_function(0, _('Leave Request'), 'modules/ExtendedHRM/manage/leave_request.php', 'HR_EMPL_INFO', //MENU_TRANSACTION);	
        	$this->add_rapp_function(0, _('Componsentory Request'), 'modules/ExtendedHRM/manage/componsentory_request.php', 'HR_CMPFORM', MENU_TRANSACTION);
	$this->add_rapp_function(0, _('Leave Request'), 'modules/ExtendedHRM/manage/bom_edit.php', 'HR_LEAVEFORM', MENU_TRANSACTION);	
                
        $this->add_rapp_function(0, _('Tour Request'), 'modules/ExtendedHRM/manage/tour_request.php', 'HR_TOURFORM', MENU_TRANSACTION);
       
        $this->add_lapp_function(0, _('Leave Encashment Request'), 'modules/ExtendedHRM/manage/leave_encashment_request.php', 'HR_ENCASHMENT_REQUEST', MENU_TRANSACTION);
        // $this->add_lapp_function(0, _('Advance Salary'), 'modules/ExtendedHRM/advance_salary.php', 'HR_PAYSLIP', MENU_TRANSACTION);        

      //  $this->add_rapp_function(0, _('Loan Entry'), 'modules/ExtendedHRM/manage/loan_form.php', 'HR_LOANFORM', MENU_TRANSACTION);

     //   $this->add_lapp_function(0, _('Claims And Reimbursement Entry'), 'modules/ExtendedHRM/claims.php', 'SA_EMPLOYEE', MENU_TRANSACTION);

        $this->add_module(_("Inquires"));

        $this->add_lapp_function(1, _('Payroll Inquiry'), 'modules/ExtendedHRM/inquires/payroll_history_inquiry.php', 'HR_PAYSLIP', MENU_INQUIRY);
        
        $this->add_lapp_function(1, _('Payroll Inquiry'), 'modules/ExtendedHRM/inquires/payroll_history_inquiry_user.php', 'HR_PAYSLIP_USER', MENU_INQUIRY);

        $this->add_lapp_function(1, _('Attendance Inquiry'), 'modules/ExtendedHRM/inquires/attendance_inquiry.php', 'HR_SELATTENDANCE', MENU_INQUIRY);
        
       
        
        $this->add_lapp_function(1, _('PF Inquirty'), 'modules/ExtendedHRM/inquires/pf_report.php', 'SA_PF_REPORT', MENU_INQUIRY);
        
        $this->add_lapp_function(1, _('ESI Inquiry'), 'modules/ExtendedHRM/inquires/esi_report.php', 'SA_ESI_REPORT', MENU_INQUIRY);

      //  $this->add_rapp_function(1, _('Loan Inquiry'), 'modules/ExtendedHRM/inquires/loan_inquiry.php', 'HR_LOANIN', MENU_INQUIRY);

        $this->add_lapp_function(1, _('Employees Inquiry'), 'modules/ExtendedHRM/inquires/employees_inquiry.php', 'HR_EMPLOYEE_INQ', MENU_INQUIRY);
		
        $this->add_lapp_function(1, _('Contract Inquiry'), 'modules/ExtendedHRM/inquires/contract_inquiry.php', 'HR_EMPLOYEE_INQ', MENU_INQUIRY);
        
		//$this->add_lapp_function(1, _('Leaves Request Report'), 'modules/ExtendedHRM/inquires/leaves_request_report.php', //'HR_EMPLOYEE_INQ', MENU_INQUIRY);
	$this->add_lapp_function(1,_('Componsentory Approval'),
		'modules/ExtendedHRM/inquires/componsentory_aproval.php','HR_CMPFORM_APROVAL', MENU_INQUIRY);
        $this->add_lapp_function(1,_('Leave Approval'),
		'modules/ExtendedHRM/inquires/leaves_request_report_new.php','HR_EMPLOYEE_INQ', MENU_INQUIRY);
        
	$this->add_lapp_function(1,_('Leave Encashment Approval'),
		'modules/ExtendedHRM/inquires/leaves_encash_request_approval.php','HR_EMPLOYEE_INQ', MENU_INQUIRY);
                
        $this->add_lapp_function(1,_('Tour Approval'),
		'modules/ExtendedHRM/inquires/tour_request_approve.php','HR_EMPLOYEE_INQ', MENU_INQUIRY);
        
       // $this->add_rapp_function(1, _('Leave Inquiry'), 'modules/ExtendedHRM/inquires/leave_inquiry.php', 'HR_LEAVEFORM', MENU_INQUIRY);

        //$this->add_rapp_function(1, _('HRM Reports'), 'modules/ExtendedHRM/reports/hrm_reports.php?Class=8&REP_ID=801', 'HR_REPORTS', MENU_INQUIRY);
		$this->add_lapp_function(1,_('Bill Approval'),
		'modules/ExtendedHRM/manage/bill_approval_list.php','HR_TBILLAPPROVE', MENU_INQUIRY);

        $this->add_module(_("Maintainance Masters"));

      //  $this->add_lapp_function(2, _('Add And Manage Employees'), 'modules/ExtendedHRM/manage/employees.php', 'HR_EMPL_INFO', MENU_ENTRY);
		
      $this->add_lapp_function(2, _('Employee Info(Import Data)'), 'modules/ExtendedHRM/manage/import_employees.php', 'HR_EMPL_BULK', MENU_ENTRY);
        
       $this->add_lapp_function(2, _('Add And Manage Employees'), 'modules/ExtendedHRM/manage/employeeswithctc.php', 'HR_EMPL_INFO_WITH_CTC', MENU_ENTRY);
         
     //  $this->add_lapp_function(2, _('Add And Manage Employees'), 'modules/ExtendedHRM/manage/employeeswithctc.php', 'HR_EMPL_INFO_WITH_CTC', MENU_ENTRY);

        $this->add_lapp_function(2, _('Department'), 'modules/ExtendedHRM/manage/department.php', 'HR_DEPARTMENT', MENU_MAINTENANCE);
      
	  $this->add_lapp_function(2, _('Designation Group Master'), 'modules/ExtendedHRM/manage/desi_group_master.php', 'HR_DESIGNATION', MENU_MAINTENANCE);
	  
	  $this->add_lapp_function(2, _('Designation Master'), 'modules/ExtendedHRM/manage/designation_master.php', 'HR_DESIGNATIONMASTER', MENU_MAINTENANCE);
	  
	  $this->add_lapp_function(2, _('Leave Master'), 'modules/ExtendedHRM/manage/leave_master.php', 'HR_LEAVEMASTER', MENU_MAINTENANCE);
          
          $this->add_lapp_function(2, _('Leave Accural'), 'modules/ExtendedHRM/manage/leave_acural.php', 'HR_LEAVEACURAL', MENU_MAINTENANCE);
          
          $this->add_lapp_function(2, _('Leave Encashment'), 'modules/ExtendedHRM/manage/leave_encashment.php', 'HR_LEAVENCASHMENT', MENU_MAINTENANCE);
          
          $this->add_lapp_function(2, _('Occasion Master'), 'modules/ExtendedHRM/manage/occ_master.php', 'HR_OCCMASTER', MENU_MAINTENANCE);
          
          
          $this->add_lapp_function(2, _('Leave Encashment'), 'modules/ExtendedHRM/manage/leave_encashment.php', 'HR_LEAVENCASHBASED', MENU_MAINTENANCE);
          
         // $this->add_lapp_function(2, _("Holiday List"), 'modules/ExtendedHRM/manage/calendar_master.php', 'HR_DMIHOLIDAY', MENU_MAINTENANCE);
	  
	   $this->add_lapp_function(2, _('Type of Leave'), 'modules/ExtendedHRM/manage/type_of_leave_master.php', 'HR_TYPELEAVE', MENU_MAINTENANCE);
	   
	   $this->add_lapp_function(2, _('Holiday Master'), 'modules/ExtendedHRM/manage/holiday_master.php', 'HR_EMPL_INFO', MENU_MAINTENANCE);
	   

        $this->add_lapp_function(2, _('Allowance Setup'), 'modules/ExtendedHRM/manage/pay_items_setup.php', 'HR_ALLOWANCE', MENU_MAINTENANCE);
        
        $this->add_lapp_function(2, _('Salary Finance Setup'), 'modules/ExtendedHRM/manage/hrm_finance_setup.php', 'HR_FINANCE', MENU_MAINTENANCE);
         $this->add_lapp_function(2, _('Settings'), 'modules/ExtendedHRM/manage/hrm_settings.php', 'HR_EMPLOYEE_SETUP', MENU_MAINTENANCE);

     //   $this->add_rapp_function(2, _('Taxes'), 'modules/ExtendedHRM/tax/', 'HR_EMPL_TAX', MENU_MAINTENANCE);

       // $this->add_lapp_function(2, _('Loan Types'), 'modules/ExtendedHRM/manage/loan_type.php', 'HR_LOANTYPE', MENU_MAINTENANCE);       
	   
    //   $this->add_rapp_function(2, _("Demos And Documentation"), 'modules/ExtendedHRM/docs/', 'HR_EMPLOYEE_SETUP', MENU_MAINTENANCE);
       
    //   $this->add_rapp_function(2, _('Contact Me'), 'http://www.kvcodes.com/contact-me/', 'SA_OPEN', MENU_MAINTENANCE);


      
       
       //  $this->add_rapp_function(2, _('Gazetted Off Days'), 'modules/ExtendedHRM/manage/off_days.php', 'SA_EMPLOYEE', MENU_MAINTENANCE); 

        $this->add_extensions();
        
    }      
}

class hooks_ExtendedHRM extends hooks {
	var $module_name = 'ExtendedHRM';

	/*
		Install additonal menu options provided by module
	*/
    function install_tabs($app) {
        $app->add_application(new ExtendedHRM_app);
    }
  
    function install_access(){
        $security_sections[SS_EXHRM]               = _("HRM");
        $security_sections[SS_EXHRM_SETTINGS]       = _("HRM Settings");
        $security_sections[SS_EXHRM_PAYROLL]        = _("HRM Payroll");
        $security_sections[SS_EXHRM_EMPLOYEE]        = _("HRM Employee Settings");
        $security_sections[SS_HOSTEL_MANAGEMENT]        = _("Hostel Management");
        
        // ############################################################################################
        // HRM related functionality
        //
        // Employee Information
         $security_areas['HR_EMPL_INFO'] = array(SS_EXHRM|1, _("HRM Employee info")); 
         
         $security_areas['HR_EMPL_BULK'] = array(SS_EXHRM|30, _("HRM Employee Bulk")); 
         
         
         $security_areas['HR_EMPL_INFO_WITH_CTC'] = array(SS_EXHRM|10, _("HRM Employee info with ctc")); 
         
         $security_areas['HR_DEPARTMENT'] = array(SS_EXHRM|11, _("Department")); 
         
         $security_areas['HR_DESIGNATION'] = array(SS_EXHRM|12, _("Designation Group Master")); 
         
         $security_areas['HR_DESIGNATIONMASTER'] = array(SS_EXHRM|13, _("Designation Master")); 
         
         $security_areas['HR_LEAVEMASTER'] = array(SS_EXHRM|14, _("Leave Master")); 
         
         $security_areas['HR_LEAVEACURAL'] = array(SS_EXHRM|15, _("Leave Accural")); 
         
         $security_areas['HR_DMIHOLIDAY'] = array(SS_EXHRM|16, _("DMI's Holidays")); 
         
         $security_areas['HR_TYPELEAVE'] = array(SS_EXHRM|17, _("Type of Leave")); 
         
         $security_areas['HR_ALLOWANCE'] = array(SS_EXHRM|18, _("Allowance Setup")); 
         
         $security_areas['HR_FINANCE'] = array(SS_EXHRM|19, _("Salary Finance Setup"));
         
         $security_areas['HR_LEAVENCASHMENT'] = array(SS_EXHRM|20, _("Leave Encashment")); 
         
         $security_areas['HR_TOURREQUEST'] = array(SS_EXHRM|23, _("Tour Request")); 
         $security_areas['HR_TOURBILL'] = array(SS_EXHRM|24, _("Tour Bill")); 
         $security_areas['HR_TBILLAPPROVE'] = array(SS_EXHRM|22, _("Tour Bill Aprove")); 
         

         $security_areas['HR_PAYSLIP'] = array(SS_EXHRM_PAYROLL|4, _("Pay Slip Generation")); 
         
         $security_areas['HR_PAYSLIP_DETAILS'] = array(SS_EXHRM_PAYROLL|22, _("Direct Payslip")); 
         
         $security_areas['HR_ARREAR_CALCULATION_MONTH'] = array(SS_EXHRM_PAYROLL|23, _("Arrear Calculation By Month")); 
         
         $security_areas['HR_ARREAR_CALCULATION'] = array(SS_EXHRM_PAYROLL|24, _("Arrear Calculation")); 
         
         $security_areas['HR_UPGRADE'] = array(SS_EXHRM_PAYROLL|25, _("Pay Commission Upgrade")); 
         
         $security_areas['HR_UPGRADE_MONTH'] = array(SS_EXHRM_PAYROLL|26, _("Pay Commission Upgrade By Month")); 
         
         $security_areas['HR_PAYROLL'] = array(SS_EXHRM_PAYROLL|27, _("Payroll")); 
         
         $security_areas['HR_ATTACHMENT'] = array(SS_EXHRM_PAYROLL|28, _("Attachments")); 
         
         $security_areas['HR_ATTACHMENT_VIEW'] = array(SS_EXHRM_PAYROLL|29, _("Attachments View User")); 
         

         $security_areas['HR_ATTENDANCE'] = array(SS_EXHRM|2, _("Employee Attendence"));

         $security_areas['HR_LOANFORM'] = array(SS_EXHRM|3, _("Loan Application Form"));

         $security_areas['HR_REPORTS'] = array(SS_EXHRM_SETTINGS|5, _("HRM Reports")); 

         $security_areas['HR_EMPL_TAX'] = array(SS_EXHRM_SETTINGS|9, _("Tax Setup")); 

         $security_areas['HR_EMPLOYEE_INQ'] = array(SS_EXHRM|4, _("Employee Inquiry")); 
         
         $security_areas['SA_PF_REPORT'] = array(SS_EXHRM|21, _("EPF Inquiry"));
         
         $security_areas['SA_ESI_REPORT'] = array(SS_EXHRM|100, _("ESI Inquiry"));
         
         $security_areas['HR_SELATTENDANCE'] = array(SS_EXHRM|5, _("Selective Attendance List Show"));
      
         $security_areas['HR_LOANIN'] = array(SS_EXHRM|6, _("Loan Approve Inquiry"));
       
         $security_areas['HR_LEAVEFORM'] = array(SS_EXHRM|7, _("Leave Application Form"));
         
         $security_areas['HR_ENCASHMENT_REQUEST'] = array(SS_EXHRM|9, _("Leave Encashment Application Form"));
         
         $security_areas['HR_TOURFORM'] = array(SS_EXHRM|8, _("Tour Application Form"));
        $security_areas['HR_CMPFORM_APROVAL'] = array(SS_EXHRM|200, _("Compensatory Aproval Form"));
        $security_areas['HR_CMPFORM'] = array(SS_EXHRM|300, _("Compensatory Request Form"));
         
         $security_areas['HR_LOANTYPE'] = array(SS_EXHRM_SETTINGS|3, _("Loan Type Setup"));
		 
         $security_areas['HR_EMPLOYEE_SETUP'] = array(SS_EXHRM_SETTINGS|3, _("Setup"));

         $security_areas['SA_SELATTENDANCE'] = array(SS_EXHRM_EMPLOYEE|1, _("Attendance Inquiry"));
         
           
         $security_areas['HR_PAYSLIP_USER'] = array(SS_EXHRM_EMPLOYEE|2, _("Payroll Inquiry"));
         
         
         
         $security_areas['ROOM_MASTER'] = array(SS_HOSTEL_MANAGEMENT|1, _("Room Master"));
         $security_areas['BED_CREATE'] = array(SS_HOSTEL_MANAGEMENT|2, _("Create Bed"));
         $security_areas['BED_SHIFT'] = array(SS_HOSTEL_MANAGEMENT|3, _("Change Bed"));
         $security_areas['GUEST_REGISTRATION'] = array(SS_HOSTEL_MANAGEMENT|4, _("Guest Registration"));
         $security_areas['LEAVE_BED'] = array(SS_HOSTEL_MANAGEMENT|5, _("Relinquishment of occupancy"));
         $security_areas['ROOM_ALLOTMENT'] = array(SS_HOSTEL_MANAGEMENT|6, _("Room Allotment"));
         $security_areas['ROOM_STATUS'] = array(SS_HOSTEL_MANAGEMENT|7, _("Room Status"));


         
         

          
		return array($security_areas, $security_sections);
	}

    /* This method is called on extension activation for company.   */
    function activate_extension($company, $check_only=true)
    {
        global $db_connections;

        $updates = array(
            'update.sql' => array('SimpleHRM')
        );

        return $this->update_databases($company, $updates, $check_only);
    }

    function deactivate_extension($company, $check_only=true)
    {
        global $db_connections;

        $updates = array(
            'drop.sql' => array('ugly_hack') // FIXME: just an ugly hack to clean database on deactivation
        );

        return $this->update_databases($company, $updates, $check_only);
    }
}

?>