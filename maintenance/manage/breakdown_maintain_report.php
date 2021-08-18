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
$page_security = 'SA_BREAKDOWNVIEW';
$path_to_root="../..";
include_once($path_to_root . "/includes/session.inc");

include_once($path_to_root . "/includes/date_functions.inc");
include_once($path_to_root . "/includes/ui.inc");
include_once($path_to_root . "/includes/data_checks.inc");
include_once($path_to_root . "/reporting/includes/reporting.inc");
//include_once($path_to_root . "/maintenance/manage/add_preventive_maintenance.php");

$js = "";
if ($use_popup_windows)
	$js .= get_js_open_window(800, 500);
if ($use_date_picker)
	$js .= get_js_date_picker();
page(_($help_context = "Breakdown Report"), isset($_GET['bank_account']), false, "", $js);

//check_db_has_bank_accounts(_("There are no bank accounts defined in the system."));

//-----------------------------------------------------------------------------------
// Ajax updates
//
if (get_post('Show'))
{
	$Ajax->activate('trans_tbl');
}
//------------------------------------------------------------------------------------------------

/* if (isset($_GET['bank_account']))
	$_POST['bank_account'] = $_GET['bank_account'];

start_form();
start_table(TABLESTYLE_NOBORDER);
start_row();
bank_accounts_list_cells(_("Account:"), 'bank_account', null);

date_cells(_("From:"), 'TransAfterDate', '', null, -30);
date_cells(_("To:"), 'TransToDate');

submit_cells('Show',_("Show"),'','', 'default');
end_row();
end_table();
end_form();  */

//------------------------------------------------------------------------------------------------

//$query = "SELECT break.*,utly.name AS u_name FROM ".TB_PREF."breakdown_maintenance AS break LEFT JOIN ".TB_PREF."utility AS utly ON utly.id= break.utility_id";
$query  ="SELECT break.*,CASE  WHEN(utly.name !='') THEN  utly.name  WHEN (utly.items_id !='') THEN s.description END AS u_name,con.supp_name AS contractor FROM ".TB_PREF."breakdown_maintenance AS break LEFT JOIN ".TB_PREF."utility AS utly ON utly.id= break.utility_id LEFT JOIN ".TB_PREF."stock_master AS s ON  utly.items_id=s.stock_id LEFT JOIN ".TB_PREF."contractor con ON con.supplier_id= break.contractor_id";
$res=db_query($query);
//$result=db_fetch($res);

function get_trans($break_id,$type)
{
	$label = $break_id;
	$class ='';
	$id=$break_id;
	$icon = '';
	 $viewer = $path_to_root."maintenance/view/";
	if ($type == 'Breakdown')
		$viewer .= "maintain_breakdown_view.php?id=".$break_id;
	
	return viewer_link($label, $viewer, $class, $id,  $icon);
	
}

div_start('trans_tbl');
//$act = get_bank_account($_POST["bank_account"]);
//display_heading($act['bank_account_name']." - ".$act['bank_curr_code']);

start_table(TABLESTYLE);

$th = array(_("#"),_("Reference"), _("Date"),
	_("Utility / Process"), _("Contractor"), _("Start Time"), _("End Time"), _("Reason"),_("Observations"),_("Comments"), _(""));
table_header($th);

$k = 0; 
$i=1; //row colour counter
while ($myrow = db_fetch($res))
{

	alt_table_row_color($k);

	// $running_total += $myrow["amount"];

	//$trandate = sql2date($myrow["prevent_id"]);
	label_cell($i);
	label_cell(get_trans($myrow["break_id"],'Breakdown'));
	//label_cell(get_trans_view_str($myrow["type"],$myrow["trans_no"],$myrow['ref']));
	label_cell($myrow["maintain_date"]);
	label_cell($myrow["u_name"]);
	label_cell($myrow["contractor"]);
	label_cell($myrow["break_st_time"]);
	label_cell($myrow["break_end_time"]);
	label_cell($myrow["ob_reason"]);
	label_cell($myrow["ob_1"]);
	label_cell($myrow["ob_2"]);
	$type="SA_BREAKDOWNVIEW";
	label_cell(print_document_link($myrow["break_id"], _('Print'), true, $type,ICON_PRINT),$myrow["break_id"]);
	
	end_row();
 /*	if ($myrow["amount"] > 0 ) 
 		$debit += $myrow["amount"];
 	else 
 		$credit += $myrow["amount"];

	if ($j == 12)
	{
		$j = 1;
		table_header($th);
	}
	$j++; */
	$i++;
}
//end of while loop

/* start_row("class='inquirybg' style='font-weight:bold'");
label_cell(_("Ending Balance")." - ". $_POST['TransToDate'], "colspan=4");
amount_cell($debit);
amount_cell(-$credit);
//display_debit_or_credit_cells($running_total);
amount_cell($debit+$credit);
label_cell("");
label_cell("", "colspan=2");
end_row(); */ 
end_table(2);
div_end();
//------------------------------------------------------------------------------------------------

end_page();

?>
