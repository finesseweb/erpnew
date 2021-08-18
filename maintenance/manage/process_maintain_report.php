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
$page_security = 'SA_PROCESSVIEW';
$path_to_root="../..";
include_once($path_to_root . "/includes/session.inc");

include_once($path_to_root . "/includes/date_functions.inc");
include_once($path_to_root . "/includes/ui.inc");
include_once($path_to_root . "/includes/data_checks.inc");
include_once($path_to_root . "/reporting/includes/reporting.inc");
include_once($path_to_root . "/reporting/includes/excel_report.inc");
//include_once($path_to_root . "/maintenance/manage/add_preventive_maintenance.php");

$js = "";
if ($use_popup_windows)
	$js .= get_js_open_window(800, 500);
if ($use_date_picker)
	$js .= get_js_date_picker();
page(_($help_context = "Process Report"), isset($_GET['bank_account']), false, "", $js);

//-----------------------------------------------------------------------------------
// Ajax updates
//
if (get_post('Show'))
{
	$Ajax->activate('trans_tbl');
}
//------------------------------------------------------------------------------------------------

$query = "SELECT process.*,utly.name AS u_name,freq.frequency_name AS frequency,con.supp_name AS contractor FROM ".TB_PREF."process_maintenance AS process LEFT JOIN ".TB_PREF."utility AS utly ON utly.id= process.utility_id LEFT JOIN ".TB_PREF."frequency_master AS freq ON freq.freq_id= process.frequency_id LEFT JOIN ".TB_PREF."contractor con ON con.supplier_id= process.contractor_id WHERE utly.type=2";
$res=db_query($query);
//$result=db_fetch($res);

div_start('trans_tbl');

start_table(TABLESTYLE);

$th = array(_("#"),_("Reference"), _("Date"),
	_("Process"), _("Frequency"),_("Contractor"), _("Observation Date"), _("Observations"), _("Corrective Action Suggested"), _("Corrective Action Initiated"),_(""));
table_header($th);

function get_trans($process_id,$type)
{
	$label = $process_id;
	$class ='';
	$id=$process_id;
	$icon = '';
	 $viewer = $path_to_root."maintenance/view/";
	if ($type == 'Process')
		$viewer .= "maintain_process_view.php?id=".$process_id;
	
	return viewer_link($label, $viewer, $class, $id,  $icon);
	
}


$k = 0; 
$i=1; //row colour counter
while ($myrow = db_fetch($res))
{

	alt_table_row_color($k);

	// $running_total += $myrow["amount"];

	//$trandate = sql2date($myrow["prevent_id"]);
	label_cell($i);
	label_cell(get_trans($myrow["process_id"],'Process'));
	//label_cell(get_trans_view_str($myrow["type"],$myrow["trans_no"],$myrow['ref']));
	label_cell($myrow["maintain_date"]);
	label_cell($myrow["u_name"]);
	label_cell($myrow["frequency"]);
	label_cell($myrow["contractor"]);
	label_cell($myrow["ob_date"]);
	label_cell($myrow["ob_1"]);
	label_cell($myrow["ob_2"]);
	label_cell($myrow["ob_3"]);
	$type="SA_PROCESSVIEW";
	label_cell(print_document_link($myrow["process_id"], _('Print'), true, $type,ICON_PRINT));
	
//	label_cell("<a href=".$path_to_root."/reporting/prn_redirect.php?xls=1&&PARAM_0=4&PARAM_1=4&PARAM_2=0&PARAM_3=&PARAM_4=0&REP_ID=603>EXCEl</a>");
	$rep = new FrontReport(_('Year of Holidays: '.$year_of_selection[0]), "Holidays", user_pagesize(), 9, $orientation);
	    
	    if ($orientation == 'L')
	    	recalculate_cols($cols);
			
	    $rep->Font();
	    $rep->Info(null, $cols, $headers, $aligns);
	    $rep->NewPage();
	//display_debit_or_credit_cells($myrow["amount"]);
	//amount_cell($running_total);
	//label_cell(payment_person_name($myrow["person_type_id"],$myrow["person_id"]));
	//label_cell(get_comments_string($myrow["type"], $myrow["trans_no"]));
	//label_cell(get_gl_view_str($myrow["type"], $myrow["trans_no"]));
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
