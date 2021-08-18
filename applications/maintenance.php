<?php
/**********************************************************************
  
	Released underhe GNU General Public License, GPL,
	as published by the Free Software Foundation, either version 3
	of the License, or (at your option) any later version.
    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
    See the License here <http://www.gnu.org/licenses/gpl-3.0.html>.
***********************************************************************/
class maintenance_app extends application
{
	function __construct()
	{
                 parent::__construct("maintenance", _($this->help_context = "&Maintenance"));
		//$this->application("maintenance", _($this->help_context = "&Maintenance"));
		$this->add_module(_("Transactions"));
	    $this->add_lapp_function(0, _("Preventive Maintenance "),
		"maintenance/manage/preventive_maintenance.php?", 'SA_MAINTAINPREVENTIVE', MENU_TRANSACTION);
		$this->add_lapp_function(0, _("Breakdown Maintenance"),
		"maintenance/manage/breakdown_maintenance.php?", 'SA_MAINTAINBREAKDOWN', MENU_TRANSACTION);	
		$this->add_lapp_function(0, _("Process Maintenance"),
		"maintenance/manage/process_maintenance.php?", 'SA_MAINTAINPROCESS', MENU_TRANSACTION);
		$this->add_lapp_function(0, _("Help Desk"),
		"maintenance/manage/help_desk.php?", 'SA_HELPDESK', MENU_TRANSACTION);
		$this->add_lapp_function(0, _("Visitor Desk"),
		"maintenance/manage/visit_mng.php?", 'SA_HELPDESK', MENU_TRANSACTION);
			
	/*	$this->add_lapp_function(0, _("&Inventory Issue"),
			"purchasing/po_entry_items.php?NewIssue=Yes", 'SA_PURCHASEENQUIRY', MENU_TRANSACTION);
		$this->add_lapp_function(0, _("Inventory Location &Transfers"),
			"inventory/transfers.php?NewTransfer=1", 'SA_LOCATIONTRANSFER', MENU_TRANSACTION);
		$this->add_lapp_function(0, _("Inventory &Adjustments"),
			"inventory/adjustments.php?NewAdjustment=1", 'SA_INVENTORYADJUSTMENT', MENU_TRANSACTION); */

		$this->add_module(_("Maintenance Reports"));
		$this->add_lapp_function(1, _("Preventive Reports"),
			"maintenance/manage/prevent_maintain_report.php?", 'SA_PREVENTVIEW', MENU_INQUIRY);
		$this->add_lapp_function(1, _("Breakdown Reports"),
			"maintenance/manage/breakdown_maintain_report.php?", 'SA_BREAKDOWNVIEW', MENU_INQUIRY);
		$this->add_rapp_function(1, _("Process Reports"),
			"maintenance/manage/process_maintain_report.php?Class=2", 'SA_PROCESSVIEW', MENU_REPORT);
        $this->add_rapp_function(1, _("Help Desk Reports"),
			"maintenance/manage/helpdesk_complaint_report.php?Class=2", 'SA_HELPDESKVIEW', MENU_REPORT);	
		$this->add_rapp_function(1, _("Visitor Reports"),
			"maintenance/manage/visite_report.php?Class=2", 'SA_HELPDESKVIEW', MENU_REPORT);			

		$this->add_module(_("Maintenance Masters"));
		
			$this->add_lapp_function(2, _("&Frequency"),
			"maintenance/manage/frequency.php?", 'SA_ITEM', MENU_ENTRY);
			
		$this->add_lapp_function(2, _("&Utility/Process"),
			"maintenance/manage/utility.php?", 'SA_ITEM', MENU_ENTRY);
		
		$this->add_lapp_function(2, _("&Maintenance Departments"),
			"maintenance/manage/maintain_dept.php?", 'SA_ITEM', MENU_ENTRY);
		
		$this->add_lapp_function(2, _("&Contractor"),
			"maintenance/manage/contractor.php?", 'SA_ITEM', MENU_ENTRY);
		//$this->add_lapp_function(1, _("&Process"),
		//	"maintenance/manage/maintenance.php?", 'SA_ITEM', MENU_ENTRY);	
		
	
			
	//	$this->add_lapp_function(1, _("&Process Parameters"),
			//"maintenance/manage/parameters.php?", 'SA_ITEM', MENU_ENTRY);
		
		$this->add_lapp_function(2, _("&Utility/Process Parameters"),
			"maintenance/manage/utility_parameters.php?", 'SA_ITEM', MENU_ENTRY);
	/*	 $this->add_lapp_function(2, _("&Foreign Item Codes"),
			"inventory/manage/item_codes.php?", 'SA_FORITEMCODE', MENU_MAINTENANCE);
		$this->add_lapp_function(2, _("Sales &Kits"),
			"inventory/manage/sales_kits.php?", 'SA_SALESKIT', MENU_MAINTENANCE);
		$this->add_lapp_function(2, _("Item &Categories"),
			"inventory/manage/item_categories.php?", 'SA_ITEMCATEGORY', MENU_MAINTENANCE);
		$this->add_lapp_function(2, _("Item &Sub Categories"),
			"inventory/manage/item_subcategories.php?", 'SA_ITEMCATEGORY', MENU_MAINTENANCE);
		$this->add_lapp_function(2, _("Inventory &Locations"),
			"inventory/manage/locations.php?", 'SA_INVENTORYLOCATION', MENU_MAINTENANCE);
		$this->add_rapp_function(2, _("Inventory &Movement Types"),
			"inventory/manage/movement_types.php?", 'SA_INVENTORYMOVETYPE', MENU_MAINTENANCE);
		$this->add_rapp_function(2, _("&Units of Measure"),
			"inventory/manage/item_units.php?", 'SA_UOM', MENU_MAINTENANCE);
		$this->add_rapp_function(2, _("&Reorder Levels"),
			"inventory/reorder_level.php?", 'SA_REORDER', MENU_MAINTENANCE);

		$this->add_module(_("Pricing and Costs"));
		$this->add_lapp_function(3, _("Sales &Pricing"),
			"inventory/prices.php?", 'SA_SALESPRICE', MENU_MAINTENANCE);
		$this->add_lapp_function(3, _("Purchasing &Pricing"),
			"inventory/purchasing_data.php?", 'SA_PURCHASEPRICING', MENU_MAINTENANCE);
		$this->add_rapp_function(3, _("Standard &Costs"),
			"inventory/cost_update.php?", 'SA_STANDARDCOST', MENU_MAINTENANCE); */

		$this->add_extensions();
	}
}


?>