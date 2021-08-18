<?php
/**********************************************************************
  
	Released under the terms of the GNU General Public License, GPL,
	as published by the Free Software Foundation, either version 3
	of the License, or (at your option) any later version.
    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
    See the License here <http://www.gnu.org/licenses/gpl-3.0.html>.
***********************************************************************/


// ----------------------------------------------------------------
// $ Revision:	2.0 $
// Creator:	Joe Hunt
// date_:	2005-05-19
// Title:	Purchase Orders
// ----------------------------------------------------------------
$page_security = 'SA_OPEN';
$path_to_root="../../..";

include_once($path_to_root . "/includes/session.inc");
include_once($path_to_root . "/includes/date_functions.inc");
include_once($path_to_root . "/includes/data_checks.inc");
include_once($path_to_root . "/includes/db/crm_contacts_db.inc");
include_once($path_to_root . "/taxes/tax_calc.inc");
if(isset($_GET['rep_v'])){
		include($path_to_root . "/modules/ExtendedHRM/includes/Payroll.inc");
	}

//----------------------------------------------------------------------------------------------------
$request_id = (isset($_POST['PARAM_0']) ? $_POST['PARAM_0'] : (isset($_GET['PARAM_0']) ? $_GET['PARAM_0'] : 1));
print_po($request_id);

//----------------------------------------------------------------------------------------------------

function get_po($request_id)
{
 
   	$sql = "SELECT req.*,des_group.name as grp_name,des.name as desig_name,dept.description as department,type.leave_type as leaves,empl.empl_firstname as employee FROM ".TB_PREF."kv_allocation_request AS req  LEFT JOIN ".TB_PREF."kv_desig_group AS des_group ON req.desig_group_id=des_group.id LEFT JOIN ".TB_PREF."designation_master AS des ON req.desig_id=des.id LEFT JOIN ".TB_PREF."kv_departments AS dept ON req.dept_id=dept.id LEFT JOIN ".TB_PREF."kv_type_leave_master AS type ON req.type_leave=type.type_id LEFT JOIN ".TB_PREF."kv_empl_info AS empl  ON empl.id=req.employees_id WHERE req.allocate_id = ".db_escape($request_id);

	//return $sql;
    return db_fetch($result);
}

function get_po_details($request_id)
{
	$sql = "SELECT prev.prevent_id,items.item_id AS item,items.quantity AS qty,items.stock_qty AS stock_qty FROM ".TB_PREF."prevent_maintain_entry AS prev LEFT JOIN ".TB_PREF."preventmaintain_entry_items AS items ON items.prevent_id= prev.prevent_id  WHERE prev.prevent_id=".db_escape($prevent_id)." ";
	
	return db_query($sql, "Retreive order Line Items");
}

function get_prv_params($request_id)
{
	$sql = "SELECT *,empl.empl_firstname as employee,type.leave_type as leaves,des_group.name as grp_name,des.name as desig_name,dept.description as department FROM ".TB_PREF."kv_allocation_request as request LEFT JOIN ".TB_PREF."kv_empl_info AS empl ON empl.empl_id = request.employees_id  LEFT JOIN ".TB_PREF."kv_desig_group AS des_group ON request.desig_group_id=des_group.id  LEFT JOIN ".TB_PREF."designation_master AS des ON request.desig_id=des.id LEFT JOIN ".TB_PREF."kv_departments AS dept ON request.dept_id=dept.id  LEFT JOIN ".TB_PREF."kv_type_leave_master AS type ON request.type_leave=type.type_id WHERE allocate_id = ".db_escape($request_id)."";
	
	return db_query($sql, "Retreive order Line Items");
}

function print_po($request_id)
{
	global $path_to_root, $show_po_item_codes;

	include_once($path_to_root . "/modules/ExtendedHRM//reports/pdf_report.inc");

	$from = $request_id;
	//display_error($from);
	$to = $request_id;
	//$currency = $_POST['PARAM_2'];
	//$email = $_POST['PARAM_3'];
	//$comments = $_POST['PARAM_4'];
	$orientation = $_POST['PARAM_5'];

	if (!$from || !$to) return;

	$orientation = ($orientation ? 'L' : 'P');
	$dec = user_price_dec();

	$cols = array(15, 100,  200, 250 ,300, 400, 500);

	// $headers in doctext.inc
	$aligns = array('left',	'left', 'left', 'right', 'left', 'left', 'left');

	$params = array('comments' => $comments);

	$cur = get_company_Pref('curr_default');

	if ($email == 0)
		$rep = new FrontReport(_(''), "", user_pagesize(), 9, $orientation);
	
   if ($orientation == 'L')
    	recalculate_cols($cols);
	for ($i = $from; $i <= $to; $i++)
	{
	  
		$myrow = get_po($i);
				 
		$baccount = get_default_bank_account($myrow['curr_code']);
		
		$params['allocate_id'] = $myrow['allocate_id'];

		if ($email == 1)
		{
		    $rep = new FrontReport("", "", user_pagesize(), 12, $orientation);
			//$rep->title = _('Preventive Maintenance');
			//$rep->filename = "Preventive Maintenance" . $i . ".pdf";
		}	
		$rep->SetHeaderType('Header23');
		$rep->currency = $cur;
		$rep->Font();
		$rep->Info($params, $cols, null, $aligns);

		$contacts = get_supplier_contacts($myrow['allocate_id'], 'Preventive');
		$rep->SetCommonData($myrow, null, $myrow, $baccount, SA_OPEN, $contacts);
		$rep->NewPage();
        	
		//$rep->row = $rep->topMargin + (34 * $rep->lineHeight);
		$result2 = get_prv_params($i);
		$SubTotal = 0;
		$items = $prices = array();
		$j=1;
		while ($myrow3=db_fetch($result2))
		{
		//display_error($myrow3[14]);
				//$rep->TextCol(0, 1,	$j, -2);
				//$rep->TextCol(1, 2,	$myrow3['param'], -2);
			    $rep->NewLine(2);
				$j++;
			    if ($rep->row < $rep->bottomMargin + (1 * $rep->lineHeight))
				    $rep->NewPage();
		
		$rep->row = $rep->topMargin + (50 * $rep->lineHeight);
		$rep->Text(40, _("Name               : "), -2);
		$rep->Text(110, $myrow3['employee'],-2);
        $rep->NewLine(2);		
		$rep->Text(40, _("Designation      : "), -2);
		$rep->Text(110, $myrow3['desig_name'],-2);
		$rep->NewLine(2);
		$rep->Text(40, _("Leave From      : " ), -2);
		$rep->Text(110, sql2date($myrow3['from_date']).'---'.sql2date($myrow3['to_date']),-2);
		$rep->TextCol(5, 6, _("No. of days  :  " .$myrow3['no_of_days']), -2);
		$rep->NewLine(2);
		$rep->Text(40, _("Type of Leave  :   " .$myrow3['leaves']), -2);
		$rep->NewLine(2);
		$rep->Text(40, _("Reason             : " ), -2);
		$rep->Text(115, $myrow3['reason'],-2);
		$rep->NewLine(4);
		$rep->Text(40, _("Contact Address / No. During Leave  :"), -2);
		$rep->NewLine(4);
		$rep->Text(40, _("Date : ".date('d-m-Y')), -2);
		$rep->NewLine(3);
		$rep->TextCol(5, 6, _("(Signature)"), -2);
		$rep->NewLine(3);
		$rep->Line($rep->row - 3);
		$rep->NewLine(2);
		$rep->Font('bold');
		$rep->fontSize += 6;
		$rep->TextCol(2, 6, _("For Office Use"), -2);
		$rep->fontSize -= 6;
		$rep->Font();
		$rep->NewLine(3);
		$rep->Text(40, _("Leave status (Balance) till date of CL : "), -2);
		$rep->NewLine(2);
		$rep->Text(40, _("Leave status (Balance) till date of EL : "), -2);
		$rep->NewLine(3);
		$rep->Text(40, _("Recommended :"), -2);
		$rep->NewLine(2);
		$rep->TextCol(5, 6, _("Leave approved / rejected"), -2);
		$rep->NewLine(2);
		$rep->TextCol(5, 6, _("Authority : "), -2);
		$rep->NewLine(2);
		$rep->TextCol(5, 6, _("Date : "), -2);
	    }
		$result = get_po_details($i);
		$SubTotal = 0;
		$items = $prices = array();
		while ($myrow2=db_fetch($result))
		{
		
				//$rep->TextCol(0, 1,	$myrow2['item'], -2);
				//$rep->TextCol(1, 2,	$myrow2['qty'], -2);
			//	$rep->TextCol(2, 3,	$myrow2['stock_qty'], -2);
			    $rep->NewLine(1);
			    if ($rep->row < $rep->bottomMargin + (5 * $rep->lineHeight))
				    $rep->NewPage();
		}
		$rep->row = $rep->bottomMargin + (15 * $rep->lineHeight);
		$doctype = SA_OPEN;
		$rep->NewLine();
		$rep->Font();
		if ($email == 1)
		{
			$myrow['DebtorName'] = $myrow['supp_name'];

			if ($myrow['reference'] == "")
				$myrow['prevent_id'] = $myrow['prevent_id'];
			$rep->End($email);
		}
	}
	if ($email == 0)
		$rep->End();
}

?>