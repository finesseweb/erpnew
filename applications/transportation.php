<?php
class transportation_app extends application
{
	function __construct()
	{
            
                 parent::__construct("transportation", _($this->help_context = "&Facilities"));
		//$this->application("transportation", _($this->help_context = "&Facilities"));
		$this->add_module(_("Route Managment"));
		$this->add_lapp_function(0, _("Route"),"transportation/route_master.php", 'SA_ROUTE', MENU_TRANSACTION);
		$this->add_lapp_function(0, _("Stop"),"transportation/stop_master.php", 'SA_STOP', MENU_TRANSACTION);
		$this->add_lapp_function(0, _("Route Configuration"),"transportation/route_configuration.php", 'SA_ROUTECONFIG', MENU_TRANSACTION);
		$this->add_lapp_function(0, _("Route Details"),"transportation/route_details.php", 'SA_ROUTEDETAIL', MENU_TRANSACTION);
		//$this->add_lapp_function(0, _("Route Configuration Details"),"transportation/route_configurationdetails.php", 'SA_ROUTECONFIGDETAILS', MENU_TRANSACTION);
		$this->add_lapp_function(0, _("Vehicle Configuration"),"transportation/vehicle_config.php", 'SA_VEHICLECONFIG', MENU_TRANSACTION);
		$this->add_lapp_function(0, _("Transport Configuration"),"transportation/transport_config.php", 'SA_TRANSCONFIG', MENU_TRANSACTION);
		$this->add_lapp_function(0, _("Transportation Details"),"transportation/transportation_detail.php", 'SA_TRANSPORTATIONDETAIL', MENU_TRANSACTION);
		
		
		$this->add_module(_("Hostel Management"));
                       $this->add_lapp_function(1, _("Room Allotment"),
				"modules/ExtendedHRM/manage/room_allotment.php", 'ROOM_ALLOTMENT', MENU_TRANSACTION);
                        
                       $this->add_lapp_function(1, _("Room Status"),
				"modules/ExtendedHRM/manage/hostel_record_status.php", 'ROOM_STATUS', MENU_TRANSACTION);
                       $this->add_lapp_function(1, _("Bed Shift"),
				"modules/ExtendedHRM/manage/change_bed.php", 'BED_SHIFT', MENU_TRANSACTION);
			$this->add_lapp_function(1, _("Relinquishment of Occupancy"),
				"modules/ExtendedHRM/manage/leave_bed.php", 'LEAVE_BED', MENU_TRANSACTION);
                        
                        
                        $this->add_module(_("Hostel Master"));
                         $this->add_lapp_function(2, _("Guest Registration"),
				"modules/ExtendedHRM/manage/guest_registration.php", 'GUEST_REGISTRATION', MENU_INQUIRY);
			$this->add_lapp_function(2, _("Room Master"),
				"modules/ExtendedHRM/manage/room_master.php", 'ROOM_MASTER', MENU_INQUIRY);
			$this->add_lapp_function(2, _("Create Bed"),
				"modules/ExtendedHRM/manage/hostel_bed_create.php", 'BED_CREATE', MENU_INQUIRY);	

		$this->add_extensions();
	}
}


?>