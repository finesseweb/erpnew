<?php
/**********************************************************************
    Copyright (C) FrontAccounting, LLC.
	Released under the terms of the GNU General Public License, GPL, 
	as published by the Free Software Foundation, either version 3 
	of the License, or (at your option) any later version.
    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  
    See the License here <http://www.gnu.org/licenses/gpl-3.0.html>.
***********************************************************************/
/*
	Security sections groups various areas on both functionality and privilege levels.
	Often analytic inquires are available only for management, and configuration
	for administration or management staff. This is why we have those three
	section type inside near every FA module.

	Section codes 0-99 are reserved for core FA functionalities.
	Every security section can contain up to 256 different areas.
	External modules can extend security roles system by adding rows to 
	$security_sections and $security_areas using section codes >=100.
	Security areas and sections created by extension modules/plugins
	have dynamically assigned 3-byte integer codes. The highest byte is zero
	for sections/areas defined in this file, and extid+1 for those defined 
	by extensions 
*/
define('SS_SADMIN',	1<<8);	// site admin
define('SS_SETUP',	2<<8);	// company level setup
define('SS_SPEC',	3<<8);	// special administration

define('SS_SALES_C',11<<8); // configuration
define('SS_SALES',	12<<8);	// transactions
define('SS_SALES_A',13<<8); // analytic functions/reports/inquires

define('SS_PURCH_C',21<<8);
define('SS_PURCH',	22<<8);
define('SS_PURCH_A',23<<8);

define('SS_ITEMS_C',31<<8);
define('SS_ITEMS',	32<<8);
define('SS_ITEMS_A',33<<8);

define('SS_ASSETS_C',36<<8);
define('SS_ASSETS',	37<<8);
define('SS_ASSETS_A',38<<8);

define('SS_MANUF_C',41<<8);
define('SS_MANUF',	42<<8);
define('SS_MANUF_A',43<<8);

define('SS_DIM_C',	51<<8);
define('SS_DIM',	52<<8);
define('SS_DIM_A',	53<<8);

define('SS_GL_C',	61<<8);
define('SS_GL',		62<<8);
define('SS_GL_A',	63<<8);

define('SS_LMS_C',	71<<8);
define('SS_LMS',	72<<8);

define('SS_STAT_M',	81<<8);
define('SS_STAT_T',	82<<8);
define('SS_STAT_A',	83<<8);
define('SS_STAT_ME',    84<<8);

define('SS_TALLY_T',	91<<8);
define('SS_TALLY_M',	92<<8);
define('SS_TALLY_A',	93<<8);

define('SS_DIS_C',	101<<8);
define('SS_DIS',	102<<8);
define('SS_DIS_A',	103<<8);

define('SS_ACAD_T',	111<<8);
define('SS_ACAD_M',	112<<8);
define('SS_ACAD_P',	113<<8);
define('SS_ACAD_FP',	114<<8);
define('SS_ACAD_R',	115<<8);
define('SS_ACAD_A',	116<<8);
define('SS_ACAD_L',     117<<8);
define('SS_ACAD_SA',    118<<8);
define('SS_ACAD_EV',    119<<8);
define('SS_ACAD_PL',    120<<8);
define('SS_ACAD_ST',    126<<8);

define('SS_MAINT_C',    121<<8);
define('SS_TRANS_C',	122<<8);
define('SS_TDS_C',	123<<8);
define('SS_ATTENDANCE_C',124<<8);
define('SS_TMS_C',	125<<8);
define('SS_ACAD_AR',    127<<8);



//define('SS_ACAD_T', 305 << 8);
//define('SS_ACAD_M', 306 << 8);
//define('SS_ACAD_P', 307 << 8);
//define('SS_ACAD_FP', 308 << 8);
//define('SS_ACAD_R', 309 << 8);
//define('SS_ACAD_A', 310 << 8);
//define('SS_ACAD_L', 311 << 8);
//define('SS_ACAD_SA', 312 << 8);
//define('SS_ACAD_EV', 314 << 8);
//define('SS_ACAD_PL', 315 << 8);


$security_sections = array(
 	SS_SADMIN => _("System administration"),
 	SS_SETUP => _("Company setup"),
 	SS_SPEC => _("Special maintenance"),
 	SS_SALES_C => _("Sales configuration"),
 	SS_SALES => _("Sales transactions"),
 	SS_SALES_A => _("Sales related reports"),
 	SS_PURCH_C => _("Purchase configuration"),
 	SS_PURCH => _("Purchase transactions"),
 	SS_PURCH_A => _("Purchase analytics"),
 	SS_ITEMS_C => _("Inventory configuration"),
 	SS_ITEMS => _("Inventory operations"),
 	SS_ITEMS_A => _("Inventory analytics"),
	SS_ASSETS_C => _("Fixed Assets configuration"),
	SS_ASSETS => _("Fixed Assets operations"),
	SS_ASSETS_A => _("Fixed Assets analytics"),
 	SS_MANUF_C => _("Manufacturing configuration"),
 	SS_MANUF => _("Manufacturing transactions"),
 	SS_MANUF_A => _("Manufacturing analytics"),
 	SS_DIM_C => _("Dimensions configuration"),
 	SS_DIM => _("Dimensions"),
 	SS_GL_C => _("Banking & GL configuration"),
 	SS_GL => _("Banking & GL transactions"),
 	SS_GL_A => _("Banking & GL analytics"),
	SS_LMS_C => _("LMS configuration"),
        SS_LMS => _("LMS Transaction"),
	SS_STAT_M => _("Statutory Master"),
        SS_STAT_T => _("Statutory Transaction"),
        SS_STAT_A => _("Statutory Report"),
        SS_TALLY_M => _("Tally Access"),
        SS_TRANS_C => _("Transport configuration"),
        SS_TMS_C => _("TMS configuration"),
        SS_ATTENDANCE_C => _("Attendance configuration"),
        SS_DIS_C => _("Dispatch configuration"),
        SS_DIS => _("Dispatch transactions"),
        SS_DIS_A=> _("Dispatch analytics"),
        SS_MAINT_C=> _("Maintenance"),
    
       //========TDS=====================/
        SS_TDS_C => _("Vendor configuration"), 
    
    //==[ONLY FOR ACADEMIC] ==//
    SS_ACAD_T => ("Academic Transactions"),
    SS_ACAD_M => ("Academic Masters"),
    SS_ACAD_P => ("Academic Participants"),
    SS_ACAD_FP => ("Academic Faculty Portal"),
    SS_ACAD_R => ("Academic Reports"),
    SS_ACAD_A => ("Academic Attendance"),
    SS_ACAD_L => ("Academic LMS"),
    SS_ACAD_SA => ("Academic Seating Arrangment"),
    SS_ACAD_EV => ("Academic Events"),
    SS_ACAD_PL => ("Academic placement"),
      SS_ACAD_ST => ("Academic Settings"),
      SS_ACAD_AR=>("Admission Report"),
);

/*
	This table stores security areas available in FA. 
	Key is area identifier used to check user rights, values are
	codes stored for each role in security_roles table and description used
	in roles editor.

	Set of allowed access areas codes is retrieved during user login from
	security_roles table, and cached in user profile.

	Special value 'SA_OPEN' is used for publicly available pages like login/logout.
*/
$security_areas =array(
//
//	Site administration
//
	'SA_CREATECOMPANY' =>array(SS_SADMIN|1, _("Install/update companies")),
	'SA_CREATELANGUAGE' => array(SS_SADMIN|2, _("Install/update languages")),
	'SA_CREATEMODULES' => array(SS_SADMIN|3, _("Install/upgrade modules")),
	'SA_SOFTWAREUPGRADE' => array(SS_SADMIN|4, _("Software upgrades")),
//
//	Company setup
//
	'SA_SETUPCOMPANY' => array(SS_SETUP|1, _("Company parameters")),
	'SA_SECROLES' => array(SS_SETUP|2, _("Access levels edition")),
	'SA_USERS' => array(SS_SETUP|3, _("Users setup")),
	'SA_POSSETUP' => array(SS_SETUP|4, _("Point of sales definitions")),
	'SA_PRINTERS' => array(SS_SETUP|5, _("Printers configuration")),
	'SA_PRINTPROFILE' => array(SS_SETUP|6, _("Print profiles")),
	'SA_PAYTERMS' => array(SS_SETUP|7, _("Payment terms")),
	'SA_SHIPPING' => array(SS_SETUP|8, _("Shipping ways")),
	'SA_CRSTATUS' => array(SS_SETUP|9, _("Credit status definitions changes")),
	'SA_INVENTORYLOCATION' => array(SS_SETUP|10, _("Inventory locations changes")),
	'SA_INVENTORYMOVETYPE'  => array(SS_SETUP|11, _("Inventory movement types")),
	'SA_WORKCENTRES' => array(SS_SETUP|12, _("Manufacture work centres")),
	'SA_FORMSETUP' => array(SS_SETUP|13, _("Forms setup")),
	'SA_CRMCATEGORY' => array(SS_SETUP|14, _("Contact categories")),
//
// Special and common functions
//
	'SA_VOIDTRANSACTION' => array(SS_SPEC|1, _("Voiding transactions")),
	'SA_BACKUP' => array(SS_SPEC|2, _("Database backup/restore")),
	'SA_VIEWPRINTTRANSACTION' => array(SS_SPEC|3, _("Common view/print transactions interface")),
	'SA_ATTACHDOCUMENT' => array(SS_SPEC|4, _("Attaching documents")),
	'SA_SETUPDISPLAY' => array(SS_SPEC|5, _("Display preferences")),
	'SA_CHGPASSWD' => array(SS_SPEC|6, _("Password changes")),
	'SA_EDITOTHERSTRANS' => array(SS_SPEC|7, _("Edit other users transactions")),
//
// Sales related functionality
//
	'SA_SALESTYPES' => array(SS_SALES_C|1, _("Sales types")),
	'SA_SALESPRICE' => array(SS_SALES_C|2, _("Sales prices edition")),
	'SA_SALESMAN' => array(SS_SALES_C|3, _("Sales staff maintenance")),
	'SA_SALESAREA' => array(SS_SALES_C|4, _("Sales areas maintenance")),
	'SA_SALESGROUP' => array(SS_SALES_C|5, _("Sales groups changes")),
	'SA_STEMPLATE' => array(SS_SALES_C|6, _("Sales templates")),
	'SA_SRECURRENT' => array(SS_SALES_C|7, _("Recurrent invoices definitions")),

	'SA_SALESTRANSVIEW' => array(SS_SALES|1,  _("Sales transactions view")),
	'SA_CUSTOMER' => array(SS_SALES|2,  _("Sales customer and branches changes")),
	'SA_SALESQUOTE' => array(SS_SALES|10, _("Sales quotations")),
	'SA_SALESORDER' => array(SS_SALES|3, _("Sales orders edition")),
	'SA_SALESDELIVERY' => array(SS_SALES|4, _("Sales deliveries edition")),
	'SA_SALESINVOICE' => array(SS_SALES|5, _("Sales invoices edition")),
	'SA_SALESCREDITINV' => array(SS_SALES|6, _("Sales credit notes against invoice")),
	'SA_SALESCREDIT' => array(SS_SALES|7, _("Sales freehand credit notes")),
	'SA_SALESPAYMNT' => array(SS_SALES|8, _("Customer payments entry")),
	'SA_SALESALLOC' => array(SS_SALES|9, _("Customer payments allocation")),

	'SA_SALESANALYTIC' => array(SS_SALES_A|1, _("Sales analytical reports")),
	'SA_SALESBULKREP' => array(SS_SALES_A|2, _("Sales document bulk reports")),
	'SA_PRICEREP' => array(SS_SALES_A|3, _("Sales prices listing")),
	'SA_SALESMANREP' => array(SS_SALES_A|4, _("Sales staff listing")),
	'SA_CUSTBULKREP' => array(SS_SALES_A|5, _("Customer bulk listing")),
	'SA_CUSTSTATREP' => array(SS_SALES_A|6, _("Customer status report")),
	'SA_CUSTPAYMREP' => array(SS_SALES_A|7, _("Customer payments report")),

//
// Purchase related functions
//
	'SA_PURCHASEPRICING' => array(SS_PURCH_C|1, _("Purchase price changes")),

	'SA_SUPPTRANSVIEW' => array(SS_PURCH|1, _("Supplier transactions view")),
	'SA_SUPPLIER' => array(SS_PURCH|2, _("Suppliers changes")),
	'SA_PURCHASEORDER' => array(SS_PURCH|3, _("Purchase order entry")),
        'SA_PURCHASEENQUIRY' => array(SS_PURCH|10, _("Purchase Enquiry entry")),
        'SA_PURCHASEENQUIRY2' => array(SS_PURCH|11, _("RFQ PAGE")),
        'SA_PURCHASEINDENT' => array(SS_PURCH|13, _("Purchase Indent entry")),
        'SA_PURCHASEQUOTE' => array(SS_PURCH|12, _("Purchase &Quotation Entry")),
	'SA_GRN' => array(SS_PURCH|4, _("Purchase receive")),
	'SA_SUPPLIERINVOICE' => array(SS_PURCH|5, _("Supplier invoices")),
	'SA_GRNDELETE' => array(SS_PURCH|9, _("Deleting GRN items during invoice entry")),
	'SA_SUPPLIERCREDIT' => array(SS_PURCH|6, _("Supplier credit notes")),
	'SA_SUPPLIERPAYMNT' => array(SS_PURCH|7, _("Supplier payments")),
	'SA_SUPPLIERALLOC' => array(SS_PURCH|8, _("Supplier payments allocations")),
        'SA_ADDTDS'=> array(SS_PURCH|14, _("Supplier TDS")),

	'SA_SUPPLIERANALYTIC' => array(SS_PURCH_A|1, _("Supplier analytical reports")),
	'SA_SUPPBULKREP' => array(SS_PURCH_A|2, _("Supplier document bulk reports")),
	'SA_SUPPPAYMREP' => array(SS_PURCH_A|3, _("Supplier payments report")),
//
// Inventory 
//
	'SA_ITEM' => array(SS_ITEMS_C|1, _("Stock items add/edit")),
	'SA_SALESKIT' => array(SS_ITEMS_C|2, _("Sales kits")),
	'SA_ITEMCATEGORY' => array(SS_ITEMS_C|3, _("Item categories")),
	'SA_UOM' => array(SS_ITEMS_C|4, _("Units of measure")),

	'SA_ITEMSSTATVIEW' => array(SS_ITEMS|1, _("Stock status view")),
	'SA_ITEMSTRANSVIEW' => array(SS_ITEMS|2, _("Stock transactions view")),
	'SA_FORITEMCODE' => array(SS_ITEMS|3, _("Foreign item codes entry")),
	'SA_LOCATIONTRANSFER' => array(SS_ITEMS|4, _("Inventory location transfers")),
	'SA_INVENTORYADJUSTMENT' => array(SS_ITEMS|5, _("Inventory adjustments")),
        'SA_INVETISSUE' => array(SS_ITEMS|7, _("Inventory Issue")),
        'SA_INVENTISSUE'=>array(SS_ITEMS|6, _("Inventory Issue")),
    
	'SA_REORDER' => array(SS_ITEMS_A|1, _("Reorder levels")),
	'SA_ITEMSANALYTIC' => array(SS_ITEMS_A|2, _("Items analytical reports and inquiries")),
	'SA_ITEMSVALREP' => array(SS_ITEMS_A|3, _("Inventory valuation report")),

//
// Fixed Assets
//
	'SA_ASSET' => array(SS_ASSETS_C|1, _("Fixed Asset items add/edit")),
	'SA_ASSETCATEGORY' => array(SS_ASSETS_C|2, _("Fixed Asset categories")),
	'SA_ASSETCLASS' => array(SS_ASSETS_C|4, _("Fixed Asset classes")),

	'SA_ASSETSTRANSVIEW' => array(SS_ASSETS|1, _("Fixed Asset transactions view")),
	'SA_ASSETTRANSFER' => array(SS_ASSETS|2, _("Fixed Asset location transfers")),
	'SA_ASSETDISPOSAL' => array(SS_ASSETS|3, _("Fixed Asset disposals")),
        'SA_DEPRECIATION' => array(SS_ASSETS|4, _("Depreciation")),

	'SA_ASSETSANALYTIC' => array(SS_ASSETS_A|1, _("Fixed Asset analytical reports and inquiries")),

//
// Manufacturing module 
//
	'SA_BOM' => array(SS_MANUF_C|1, _("Bill of Materials")),

	'SA_MANUFTRANSVIEW' => array(SS_MANUF|1, _("Manufacturing operations view")),
	'SA_WORKORDERENTRY' => array(SS_MANUF|2, _("Work order entry")),
	'SA_MANUFISSUE' => array(SS_MANUF|3, _("Material issues entry")),
	'SA_MANUFRECEIVE' => array(SS_MANUF|4, _("Final product receive")),
	'SA_MANUFRELEASE' => array(SS_MANUF|5, _("Work order releases")),

	'SA_WORKORDERANALYTIC' => array(SS_MANUF_A|1, _("Work order analytical reports and inquiries")),
	'SA_WORKORDERCOST' => array(SS_MANUF_A|2, _("Manufacturing cost inquiry")),
	'SA_MANUFBULKREP' => array(SS_MANUF_A|3, _("Work order bulk reports")),
	'SA_BOMREP' => array(SS_MANUF_A|4, _("Bill of materials reports")),
//
// Dimensions
//
	'SA_DIMTAGS' => array(SS_DIM_C|1, _("Dimension tags")),
	'SA_DIMTRANSVIEW' => array(SS_DIM|1, _("Dimension view")),
	'SA_DIMENSION' => array(SS_DIM|2, _("Dimension entry")),
	'SA_DIMENSIONREP' => array(SS_DIM|3, _("Dimension reports")),
	
	
	
	//LMS
	'SA_RETURNPOLICY' => array(SS_LMS_C|1, _("Return Policy")),
	'SA_TIMEEXT' => array(SS_LMS_C|2, _("Max Extension Time")),
	'SA_BOOKALLOW' => array(SS_LMS_C|3, _("Max. Books Allowed")),
	'SA_BOOKHOLD' => array(SS_LMS_C|4, _("Max. Books Hold")),
	'SA_BOOKFINE' => array(SS_LMS_C|5, _("Book Fine")),
	'SA_BOOKCATEGORY' => array(SS_LMS_C|6, _("Add Book Category")),
	'SA_ADDBOOK' => array(SS_LMS_C|7, _("Add Book")),
	'SA_ADDFLOOR' => array(SS_LMS_C|8, _("Add Floor")),
	'SA_ADDASILE' => array(SS_LMS_C|9, _("Add Floor Asile")),
	'SA_ADDSELF' => array(SS_LMS_C|10, _("Add Self")),
	'SA_BOOKRESERVATION' => array(SS_LMS_C|11, _("Book Reserve")),
	'SA_BOOKCHECKOUT' => array(SS_LMS|12, _("CheckOut Book")),
	'SA_BOOKRETURN' => array(SS_LMS|13, _("Return Book")),
	'SA_BOOKSTATUS' => array(SS_LMS|14, _("Book Status")),
	'SA_BOOKISSUE' => array(SS_LMS|15, _("Book Issues (Student)")),
	'SA_BOOKISSUESTAFF' => array(SS_LMS|16, _("Book Issues (Staff)")),
	'SA_BOOKEXTENDED' => array(SS_LMS|17, _("Book Extension")),
	
//
// Banking and General Ledger
//
	'SA_ITEMTAXTYPE' => array(SS_GL_C|1, _("Item tax type definitions")),
	'SA_GLACCOUNT' => array(SS_GL_C|2, _("GL accounts edition")),
	'SA_GLACCOUNTGROUP' => array(SS_GL_C|3, _("GL account groups")),
	'SA_GLACCOUNTCLASS' => array(SS_GL_C|4, _("GL account classes")),
	'SA_QUICKENTRY' => array(SS_GL_C|5, _("Quick GL entry definitions")),
	'SA_CURRENCY' => array(SS_GL_C|6, _("Currencies")),
	'SA_BANKACCOUNT' => array(SS_GL_C|7, _("Bank accounts")),
	'SA_TAXRATES' => array(SS_GL_C|8, _("Tax rates")),
	'SA_TAXGROUPS' => array(SS_GL_C|12, _("Tax groups")),
	'SA_FISCALYEARS' => array(SS_GL_C|9, _("Fiscal years maintenance")),
	'SA_GLSETUP' => array(SS_GL_C|10, _("Company GL setup")),
	'SA_GLACCOUNTTAGS' => array(SS_GL_C|11, _("GL Account tags")),
	'SA_GLCLOSE' => array(SS_GL_C|14, _("Closing GL transactions")),
	'SA_GLREOPEN' => array(SS_GL_C|15, _("Reopening GL transactions")), // see below
	'SA_MULTIFISCALYEARS' => array(SS_GL_C|13, _("Allow entry on non closed Fiscal years")),

	'SA_BANKTRANSVIEW' => array(SS_GL|1, _("Bank transactions view")),
	'SA_GLTRANSVIEW' => array(SS_GL|2, _("GL postings view")),
	'SA_EXCHANGERATE' => array(SS_GL|3, _("Exchange rate table changes")),
	'SA_PAYMENT' => array(SS_GL|4, _("Bank payments")),
	'SA_DEPOSIT' => array(SS_GL|5, _("Bank deposits")),
	'SA_BANKTRANSFER' => array(SS_GL|6, _("Bank account transfers")),
	'SA_RECONCILE' => array(SS_GL|7, _("Bank reconciliation")),
	'SA_JOURNALENTRY' => array(SS_GL|8, _("Manual journal entries")),
	'SA_BANKJOURNAL' => array(SS_GL|11, _("Journal entries to bank related accounts")),
	'SA_BUDGETENTRY' => array(SS_GL|9, _("Budget edition")),
	'SA_STANDARDCOST' => array(SS_GL|10, _("Item standard costs")),
	'SA_ACCRUALS' => array(SS_GL|12, _("Revenue / Cost Accruals")),

	'SA_GLANALYTIC' => array(SS_GL_A|1, _("GL analytical reports and inquiries")),
	'SA_TAXREP' => array(SS_GL_A|2, _("Tax reports and inquiries")),
	'SA_BANKREP' => array(SS_GL_A|3, _("Bank reports and inquiries")),
	'SA_GLREP' => array(SS_GL_A|4, _("GL reports and inquiries")),
    
    
    
    
    
//
// Maintenance 
//
	'SA_UTILITY' => array(SS_MAINT_C|1, _("Utility")),
	'SA_MAINTAIN_DEPT' => array(SS_MAINT_C|2, _("Maintenance Department")),
	'SA_MAINTENANCE' => array(SS_MAINT_C|3, _("Maintenance")),
	'SA_MAINTAINPREVENTIVE' => array(SS_MAINT_C|4, _("Preventive Maintenance")),
	'SA_MAINTAINPROCESS' => array(SS_MAINT_C|5, _("Process Maintenance")),
	'SA_MAINTAINBREAKDOWN' => array(SS_MAINT_C|6, _("Breakdown Maintenance")),
	'SA_HELPDESK' => array(SS_MAINT_C|7, _("Help Desk")),
	'SA_PREVENTVIEW' => array(SS_MAINT_C|8, _("Preventive Reports")),
	'SA_BREAKDOWNVIEW' => array(SS_MAINT_C|9, _("Breakdown Reports")),
	'SA_PROCESSVIEW' => array(SS_MAINT_C|10, _("Process Reports")),
	'SA_HELPDESKVIEW' => array(SS_MAINT_C|11, _("Help Desk Reports")),
    
    //Transportation
        'SA_ROUTE' => array(SS_TMS_C|1, _("Route Master")),
        'SA_STOP' => array(SS_TMS_C|2, _("Stop Master")),
        'SA_ROUTECONFIG' => array(SS_TMS_C|3, _("Route Configuration")),
        'SA_ROUTECONFIGDETAILS' => array(SS_TMS_C|4, _("Route Configuration Details")),
        'SA_ROUTEDETAIL' => array(SS_TMS_C|5, _("Route Details")),
        'SA_VEHICLECONFIG' => array(SS_TMS_C|6, _("Vehical Configuration")),
        'SA_TRANSCONFIG' => array(SS_TMS_C|7, _("Transport Configuration")),
        'SA_TRANSPORTATIONDETAIL' => array(SS_TMS_C|8, _("Transportation Details")),
    //ATTENDANCE
	'SA_ATTENDANCE' => array(SS_ATTENDANCE_C|1, _("Attendance")),
	'SA_ATTENDANCELIST' => array(SS_ATTENDANCE_C|2, _("Attendance List")),
	'SA_ATTENDANCELISTING' => array(SS_ATTENDANCE_C|3, _("User Attendance List")),
	'SA_ATTENDANCEHOURS' => array(SS_ATTENDANCE_C|4, _("Working Hours")),
	'SA_UPDATEATTENDANCE' => array(SS_ATTENDANCE_C|5, _("Add Attendance")),
	'SA_UPDATEATTENDANCEUSER' => array(SS_ATTENDANCE_C|6, _("Attendance chart")),
	'SA_ATTENDANCE2' => array(SS_ATTENDANCE_C|7, _("Attendance Entry")),
    
    //Transport
	'SA_BOOKVEHICAL' => array(SS_TRANS_C|1, _("Book Your Vehical")),
	'SA_MYBOOKING' => array(SS_TRANS_C|2, _("My Booking")),
	'SA_TRANSADMIN' => array(SS_TRANS_C|3, _("ADMIN")),
	'SA_MANAGEVEHICAL' => array(SS_TRANS_C|4, _("Manage Vehical")),
	'SA_DRIVER' => array(SS_TRANS_C|5, _("Manage Driver")),
    
    //Statutory Compliances
        'SA_STATUTORY_MASTER' => array(SS_STAT_M|1,_('Statutory Master')),
        'SA_STATUTORY_BODY' =>array(SS_STAT_M|2,_('Statutory')),
        'SA_STATUTORY_NAME_RETURN' =>array(SS_STAT_M|3,_('Name Of  Return')),
        'SA_STATUTORY_USER' => array(SS_STAT_M|4,_('Statutory Transaction')),
        'SA_STATUTORY_REPORT' => array(SS_STAT_M|5,_('Statutory Report')),
    //
    //DISPATCH
    //
        'SA_ITEMDISPATCH' => array(SS_DIS_C|1, _("Dispatch Items")),
	'SA_ITEMRECIEVE' => array(SS_DIS_C|2, _("Recieved Items")),
    
   //ATTENDANCE
	'SA_ATTENDANCE' => array(SS_ATTENDANCE_C|1, _("Attendance")),
	'SA_ATTENDANCELIST' => array(SS_ATTENDANCE_C|2, _("Attendance List")),
	'SA_ATTENDANCELISTING' => array(SS_ATTENDANCE_C|3, _("User Attendance List")),
	'SA_ATTENDANCEHOURS' => array(SS_ATTENDANCE_C|4, _("Working Hours")),
	//'SA_UPDATEATTENDANCE' => array(SS_ATTENDANCE_C|5, _("Add Attendance")),
	'SA_UPDATEATTENDANCEUSER' => array(SS_ATTENDANCE_C|6, _("Attendance chart")),
	//'SA_ATTENDANCE2' => array(SS_ATTENDANCE_C|7, _("Attendance Entry")),
	
    
    
    
    
    
                 //masters
        'SA_ACAD_CLASS' => array(SS_ACAD_M | 1, _("Class")),
        'SA_ACAD_BATCH' => array(SS_ACAD_M | 2, _("Batch")),
        'SA_ACAD_DEPARTMENT' => array(SS_ACAD_M | 3, _("Department")),
        'SA_ACAD_COURSE_CAT' => array(SS_ACAD_M | 4, _("Course Category")),
        'SA_ACAD_COURSE_TYPE' => array(SS_ACAD_M | 5, _("Course Type")),
        'SA_ACAD_E_LEARN_C' => array(SS_ACAD_M | 6, _("Experential LEarning Component")),
        'SA_ACAD_FEE_CAT' => array(SS_ACAD_M | 7, _("Fee Cat")),
        'SA_ACAD_RATING' => array(SS_ACAD_M | 8, _("Rating")),
        'SA_ACAD_QUESTION' => array(SS_ACAD_M | 9, _("Question")),
        'SA_ACAD_CREDIT' => array(SS_ACAD_M | 10, _("Credit")),
        'SA_ACAD_FEE_HEADS' => array(SS_ACAD_M | 11, _("Fee Heads")),
        'SA_ACAD_SECTION' => array(SS_ACAD_M | 12, _("Section")),
        'SA_ACAD_ROOM' => array(SS_ACAD_M | 13, _("Room")),
        'SA_ACAD_TIME_TABLE' => array(SS_ACAD_M | 14, _("Time Table")),
        'SA_ACAD_COURSE' => array(SS_ACAD_M | 15, _("Course")),
        'SA_ACAD_COURSE_FEE' => array(SS_ACAD_M | 33,_('Exam Fee')),
        'SA_ACAD_CORE_COURSE' => array(SS_ACAD_M | 16, _("Core Course")),
        'SA_ACAD_EC_LEARN' => array(SS_ACAD_M | 17, _("Elective Course Learning")),
        'SA_ACAD_E_LEARN' => array(SS_ACAD_M | 18, _("Exprential Learning")),
        'SA_ACAD_REF_GRADE' => array(SS_ACAD_M | 19, _("Refrence Grade")),
        'SA_ACAD_PROG_DESIGN' => array(SS_ACAD_M | 20, _("Program Design")),
        'SA_ACAD_PROG_CAL_YEAR' => array(SS_ACAD_M | 21, _("Program Calendar Year")),
        'SA_ACAD_FEE_STRUCTURE' => array(SS_ACAD_M | 22, _("Fee Structure")),
        'SA_ACAD_SCHOLAR_SHIP' => array(SS_ACAD_M | 23, _("Scholar Ship")),
        'SA_ACAD_PART_SCHOLAR_SHIP' => array(SS_ACAD_M | 24, _("Participants Scholar ship")),
        'SA_ACAD_INTERACTIVE_LEARNING_SESSION' => array(SS_ACAD_M | 25, _("Interactive Learning Sessions")),
        'SA_ACAD_FEED_TEMPLATE' => array(SS_ACAD_M | 26, _("Feed BAck Templates")),
        'SA_ACAD_TERM_VIEW' => array(SS_ACAD_M | 27, _("Term View")),
        'SA_ACAD_CORE_COURSE_LEARN' => array(SS_ACAD_M | 28, _("Core Course Learn")),
        'SA_ACAD_E_LEARN_DESIGN_VIEW' => array(SS_ACAD_M | 29, _("Exprential Learn Desing View")),
        'SA_ACAD_PROG_CAL_VIEW' => array(SS_ACAD_M | 30, _("Program Calendar View")),
        'SA_ACAD_GLOBAL_SET' => array(SS_ACAD_M | 31, _("Global Settings")),
        'SA_ACAD_TERM_MASTER' => array(SS_ACAD_M | 32, _("Term Master")),
	'SA_ACAD_ROOMMAPPING' => array(SS_ACAD_M | 34, _("Room Mapping")),
    'SA_ACAD_GE' => array(SS_ACAD_M | 35, _("Elective category")),
	'SA_ACAD_AECC_GE' => array(SS_ACAD_M | 36, _("Elective type")),
	'SA_ACAD_ACC' => array(SS_ACAD_M | 37, ("Account Master")),
    
      //transaction
        'SA_ACAD_TO_DO_LIST' => array(SS_ACAD_T | 1, _("To Do List")),
        
        
        
     //faculty Portal);
        'SA_ACAD_FACULTY_ALLOTMENT' => array(SS_ACAD_FP | 1, _("Faculty Allotment")),
        'SA_ACAD_LEARNING_ALOTMENT' => array(SS_ACAD_FP | 2, _("Exprential Learning Allotment")),
        'SA_ACAD_EVALUATION_COMPONENT' => array(SS_ACAD_FP | 3, _("Evaluation Component")),
        'SA_ACAD_GRADE_ALLOCATION' => array(SS_ACAD_FP | 4, _("Grade Allocation")),
        
        'SA_ACAD_E_GRADE_ALLOCATION' => array(SS_ACAD_FP | 5, _("Exprential Grade Allocation")),
        'SA_ACAD_REVIEW_PUBLISH' => array(SS_ACAD_FP | 6, _("Review And Publish")),
        'SA_ACAD_LEARNING_PROJECT' => array(SS_ACAD_FP | 7, _("Exprential Learning Project")),
        'SA_ACAD_LEARN_PROJECT_ALLOCATION' => array(SS_ACAD_FP | 8, _("Exprential Learning Project Allocation ")),
        'SA_ACAD_NOTIFICATION' => array(SS_ACAD_FP | 9, _("Notification Push")),
        'SA_ACAD_ASSIGNMENT' => array(SS_ACAD_FP | 10, _("Participants Assignment")),
        'SA_ACAD_BACK_GRADE_ALLOCATION' => array(SS_ACAD_FP | 11, _("Back Grade Allocation")),
    
                
     //participants
        'SA_ACAD_ENROLLMENT' => array(SS_ACAD_P | 1, _("Participants Enrollment")),
        'SA_ACAD_E_SELECTION' => array(SS_ACAD_P | 2, _("Elective Selection")),
        'SA_ACAD_APPLICATION_FORM' => array(SS_ACAD_P | 3, _("Application Form")),
        'SA_ACAD_STUDENTDETAIL' => array(SS_ACAD_P | 4, _("participants Details")),
        'SA_ACAD_STUD' => array(SS_ACAD_P | 5, _("Admission Record")),
         'SA_ACAD_PROMOTION' => array(SS_ACAD_P | 6, ("Promotion certification")),
         'SA_ACAD_TC' => array(SS_ACAD_P | 7, ("TC certification")),
         'SA_ACAD_CHAR' => array(SS_ACAD_P | 8, ("Character certification")),
         
         // Admission Report
         'SA_ACAD_AR_DECLARERESULT' => array(SS_ACAD_AR | 1, ("Declare Result")),
         'SA_ACAD_AR_DECLARELIST' => array(SS_ACAD_AR | 2, ("Declared List")),
'SA_ACAD_AR_ADMDOC' => array(SS_ACAD_AR | 3, ("Applicant Document")),
                
    //Reports
        'SA_ACAD_GRADE_REPORT' => array(SS_ACAD_R | 1, _("Course Wise Grade Report")),
        'SA_ACAD_TRNC' => array(SS_ACAD_R | 8, _("Back Tabulation Register")),
        'SA_ACAD_TRC' => array(SS_ACAD_R | 9, _("Tabulation Register")),
        'SA_ACAD_PENALTIES_REPORT' => array(SS_ACAD_R | 2, _("Course Wise Penalties Report")),
        'SA_ACAD_AFTER_PENALTIES' => array(SS_ACAD_R | 3, _("Course Grade After Penalties")),
        'SA_ACAD_CGPA_GPA' => array(SS_ACAD_R | 4, _("Participants GPA/CGPA")),
        'SA_ACAD_TERM_GRADE_SHEET' => array(SS_ACAD_R | 5, _("Generate Term Wise Grade Sheet")),
    'SA_ACAD_BACK_REPORT' => array(SS_ACAD_R | 10, _("Generate NON Collegiate Term Wise Grade Sheet")),
        'SA_ACAD_FINAL GRADE' => array(SS_ACAD_R | 6, _("Generate Final Grade")),
        'SA_ACAD_DIRECT_FINAL_GRADE' => array(SS_ACAD_R | 7, _("Direct Final Grade")),
    'SA_ACAD_STUDENTEXAMDETAILS' => array(SS_ACAD_R | 11, ("Exam Report")),
      'SA_ACAD_STUD_ADMITCARD' => array(SS_ACAD_R | 12, ("End Sem Admit Card")), 
'SA_ACAD_NON_UG' => array(SS_ACAD_R | 13, ("NON UG ADMIT CARD")), 
		'SA_ACAD_NON_PG' => array(SS_ACAD_R | 14, ("NON PG ADMIT CARD")),	  
		'SA_ACAD_SHEET' => array(SS_ACAD_R | 20, ("Attendance sheet")),
    //Attendance
       'SA_ACAD_P_ATTENDANCE' => array(SS_ACAD_A | 1, _("Semester Wise Attendance")),
        'SA_ACAD_ATTENDANCE_REPORT' => array(SS_ACAD_A | 2, _("Attendance Report")),
        'SA_ACAD_M_ATTENDANCE' => array(SS_ACAD_A | 3, _("Monthly Attendance")),
        'SA_ACAD_D_ATTENDANCE' => array(SS_ACAD_A | 4, ("Daily Attendance")),
    
     //LMS
        'SA_ACAD_BM' => array(SS_ACAD_L | 1, _("Book Managemnt")),
        'SA_ACAD_BH' => array(SS_ACAD_L | 2, _("Hold Book List")),
        'SA_ACAD_BE' => array(SS_ACAD_L | 3, _("Book Extension")),
    
    
    //SA_ACAD_ADDEVENT
        'SA_ACAD_ADDEVENT' => array(SS_ACAD_EV | 1,_("Add Event")),
        'SA_ACAD_EVENTLIST' => array(SS_ACAD_EV | 2,_("Event List")),
        'SA_ACAD_EVENTCATEGORY' => array(SS_ACAD_EV | 3,_("Event Category")),
    
    //Seating Arrangment
        'SS_ACAD_OPERATION' => array(SS_ACAD_SA | 1,_("Seating Plan")),
        'SS_ACAD_STUDENT' => array(SS_ACAD_SA | 2,_("Add Student")),
        'SS_ACAD_DURATION' => array(SS_ACAD_SA | 3, _("Duration")),
        'SS_ACAD_EXAMBATCH' => array(SS_ACAD_SA | 4,_("Exam Batch")),
    
         //Settings
        'SA_ACAD_OTP_INFO' => array(SS_ACAD_ST | 1,("Manage OTP")),
        'SA_ACAD_NUMBER_UPDATE' => array(SS_ACAD_ST | 2,("Update Student Info")),
        'SA_ACAD_UPDATE_OFFLINE' => array(SS_ACAD_ST | 3,("Update Offline Applicant Info")),
        'SA_ACAD_ADMIN_SET' => array(SS_ACAD_ST | 4,("Users Authentication")),
        'SA_ACAD_RAZR' => array(SS_ACAD_ST | 5,("Razor")),
        'SA_ACAD_ADMIN_DOCSET' => array(SS_ACAD_ST | 6,("Unlock Doc Status")),
                
    //-----------------------------Academic Access ----------// Acadmic Ends
    
             
    
);

if (!@$SysPrefs->allow_gl_reopen)
	unset($security_areas['SA_GLREOPEN']);
function add_access_extensions()
{
	global $security_areas, $security_sections, $installed_extensions;

	foreach($installed_extensions as $extid => $ext) {
		$accext = hook_invoke($ext['package'], 'install_access', $dummy);
		if ($accext == null) continue;

		$scode = 100;
		$acode = 100;
		$extsections = $accext[1];
		$extareas = $accext[0];
		$extcode = $extid<<16;
		
		$trans = array();
		foreach($extsections as $code =>$name) {
			$trans[$code] = $scode<<8;
			// reassign section codes
			$security_sections[$trans[$code]|$extcode] = $name;
			$scode++;
		}
		foreach($extareas as $code => $area) {
			$section = $area[0]&0xff00;
			// extension modules:
			// if area belongs to nonstandard section
			// use translated section codes and
			// preserve lower part of area code
			if (isset($trans[$section])) {
				$section = $trans[$section];
			} 
				// otherwise assign next available
				// area code >99
			$area[0] = $extcode | $section | ($acode++);
			$security_areas[$code] = $area;
		}
	}
}

function check_edit_access($name)
{
	global $input_security;

	$access = @$input_security[$name];

	if (!$access)
		$access = @$input_security['']; // default access level

	if (!$access)
		return true; // if constraint is not defined edit access is allowed

	return  user_check_access($access);
}
/*
	Returns POST value or null if edit access to $name control is forbidden.
*/
function access_post($name, $dflt=null)
{
	if (!check_edit_access($name))
		return $dflt;
	else
		return get_post($name, $dflt);
}

/*
	Returns numeric input value or null if edit access to $name control is forbidden.
*/
function access_num($name, $dflt=null)
{
	if (!check_edit_access($name))
		return $dflt;
	else
		return input_num($name, $dflt);
}
