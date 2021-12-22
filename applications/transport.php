<?php
class transport_app extends application
{
	function __construct()
	{
             parent::__construct("transport", _($this->help_context = "&Admin"));
		//$this->application("transport", _($this->help_context = "&Admin"));
		$this->add_module(_("Transport Managment"));
		$this->add_lapp_function(0, _("Book Your Vehicle "),"transport_managment", 'SA_BOOKVEHICAL', MENU_TRANSACTION);
		$this->add_lapp_function(0, _("MY Booking"),"transport_managment/my-booking.php?", 'SA_MYBOOKING', MENU_TRANSACTION);
		$this->add_lapp_function(0, _("Manage Booking"),"transport_managment/admin/manage-bookings.php?", 'SA_TRANSADMIN', MENU_INQUIRY);
		$this->add_lapp_function(0, _("Manage Vehicle"),"transport_managment/admin/manage-vehicles.php?", 'SA_MANAGEVEHICAL', MENU_INQUIRY);
		$this->add_lapp_function(0, _("Driver Managment"),"transport_managment/admin/manage-driver.php?", 'SA_DRIVER', MENU_INQUIRY);
                
                $this->add_module(_("Dispatch Managment"));
		$this->add_lapp_function(1, _("&Dispatch Items"),"modules/ExtendedHRM/manage/d_sender.php", 'SA_ITEMDISPATCH', MENU_TRANSACTION);
                $this->add_lapp_function(1, _("&Recieved Items"),"modules/ExtendedHRM/manage/d_reciever.php", 'SA_ITEMRECIEVE', MENU_TRANSACTION);
                
		$this->add_module(_("Statutory Compliances"));
	        $this->add_lapp_function(2, _("&Statutory Transactions"),"statutory/manage/statutory_transction.php?", 'SA_STATUTORY_USER', MENU_ENTRY);
		$this->add_lapp_function(2, _("&Statutory Report"),"statutory/manage/statutory_report.php?", 'SA_STATUTORY_REPORT', MENU_ENTRY);
		$this->add_lapp_function(2, _("&Statutory Master"),"statutory/manage/statutory_master.php?", 'SA_STATUTORY_MASTER', MENU_ENTRY);
                $this->add_lapp_function(2, _("&Statutory Body Master"),"statutory/manage/statutory_body.php?", 'SA_STATUTORY_BODY', MENU_ENTRY);
	        $this->add_lapp_function(2, _("&Name of Compliance"),"statutory/manage/name_return.php?", 'SA_STATUTORY_NAME_RETURN', MENU_ENTRY);
		
		$this->add_module(_("Attendance"));
		$this->add_lapp_function(3, _("Attendance "),"attendance/attendance.php", 'SA_ATTENDANCE', MENU_TRANSACTION);
		$this->add_lapp_function(3, _("Attendance List"),"attendance/attendanceList.php", 'SA_ATTENDANCELIST', MENU_TRANSACTION);
       // $this->add_lapp_function(3, _("Add Attendance "),"attendance/mannual-attendance.php", 'SA_ATTENDANCE2', MENU_TRANSACTION);
                  $this->add_lapp_function(3, _('Attendance Inquiry'), 'modules/ExtendedHRM/inquires/attendance_inquiry_user.php', 'SA_ATTENDANCEUSER', MENU_INQUIRY);
		$this->add_lapp_function(3, _("User Attendance List "),"attendance/attendanceListing.php", 'SA_ATTENDANCELISTING', MENU_TRANSACTION);
		$this->add_lapp_function(3, _("LogIn / LogOut "),"attendance/update_attendance.php", 'SA_UPDATEATTENDANCE', MENU_TRANSACTION);
                $this->add_lapp_function(3, _("Attendance Chart"),"attendance/user_attend.php", 'SA_UPDATEATTENDANCEUSER', MENU_TRANSACTION);
		$this->add_lapp_function(3, _("Set Working Hours "),"attendance/set_time.php", 'SA_ATTENDANCEHOURS', MENU_TRANSACTION);
                

		$this->add_extensions();
	}
}


?>