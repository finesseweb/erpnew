<?php
class attendance_app extends application
{
	function __construct()
	{
		//$this->application("Attendance", _($this->help_context = "&Attendance"));
		parent::__construct("Attendance", _($this->help_context = "&Attendance"));
		$this->add_module(_("Transection"));
		$this->add_lapp_function(0, _("Attendance "),"attendance/attendance.php", 'SA_ATTENDANCE', MENU_TRANSACTION);
                 $this->add_lapp_function(0, _('Attendance Inquiry'), 'modules/ExtendedHRM/inquires/attendance_inquiry.php', 'SA_ATTENDANCEUSER', MENU_INQUIRY);
                 
		$this->add_lapp_function(0, _("Attendance List"),"attendance/attendanceList.php", 'SA_ATTENDANCELIST', MENU_TRANSACTION);
		
		$this->add_module(_("Maintance Reports"));
               // $this->add_lapp_function(1, _("Add Attendance "),"attendance/mannual-attendance.php", 'SA_ATTENDANCE2', MENU_TRANSACTION);
		$this->add_lapp_function(1, _("User Attendance List "),"attendance/attendanceListing.php", 'SA_ATTENDANCELISTING', MENU_TRANSACTION);
		$this->add_lapp_function(1, _("LogIn / LogOut "),"attendance/update_attendance.php", 'SA_UPDATEATTENDANCE', MENU_TRANSACTION);
            $this->add_lapp_function(1, _("Attendance Chart"),"attendance/user_attend.php", 'SA_UPDATEATTENDANCEUSER', MENU_TRANSACTION);
			
		$this->add_module(_("Maintance Master"));
		$this->add_lapp_function(2, _("Set Working Hours "),"attendance/set_time.php", 'SA_ATTENDANCEHOURS', MENU_TRANSACTION);
		

		$this->add_extensions();
	}
}


?>