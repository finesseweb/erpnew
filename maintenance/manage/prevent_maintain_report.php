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
$page_security = 'SA_PREVENTVIEW';
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
page(_($help_context = "Preventive Report"), isset($_GET['bank_account']), false, "", $js);

check_db_has_bank_accounts(_("There are no bank accounts defined in the system."));

//-----------------------------------------------------------------------------------
// Ajax updates
//
if (get_post('Show'))
{
	$Ajax->activate('trans_tbl');
}
//------------------------------------------------------------------------------------------------


//------------------------------------------------------------------------------------------------
$query = "SELECT prev.*,s.description AS Item,freq.frequency_name AS frequency,con.supp_name AS contractor FROM ".TB_PREF."prevent_maintain_entry AS prev LEFT JOIN ".TB_PREF."utility AS utly ON utly.id= prev.utility_id LEFT JOIN ".TB_PREF."frequency_master AS freq ON freq.freq_id= prev.frequency_id LEFT JOIN ".TB_PREF."stock_master s ON s.stock_id= utly.items_id LEFT JOIN ".TB_PREF."contractor con ON con.supplier_id= prev.contractor_id WHERE utly.type=1";
$res=db_query($query);
//$result=db_fetch($res);

div_start('trans_tbl');
start_table(TABLESTYLE);

$th = array(_("#"),_("Reference"), _("Date"),
	_("Utility"), _("Frequency"),_("Contractor"), _("Observations Date"), _("Observations"), _("Corrective Action Suggested"),_("Corrective Action Initiated"),_(""));
table_header($th);


function get_trans($prevent_id,$type)
{

	$label = $prevent_id;
	$class ='';
	$id=$prevent_id;
	$icon = '';
	 $viewer = $path_to_root."maintenance/view/";
	if ($type == 'Prevent')
	hidden('Prevent_id',$prevent_id);
		$viewer .= "maintain_preventive_view.php?id=".$prevent_id;
	
	return viewer_link($label, $viewer, $class, $id,  $icon);
	
}
	

$k = 0; 
$i=1; //row colour counter
while ($myrow = db_fetch($res))
{

	alt_table_row_color($k);
    label_cell($i);
	label_cell(get_trans($myrow['prevent_id'],'Prevent'));
	label_cell($myrow["maintain_date"]);
	label_cell($myrow["Item"]);
	label_cell($myrow["frequency"]);
	label_cell($myrow["contractor"]);
	label_cell($myrow["prv_ob_date"]);
	label_cell($myrow["prv_ob_1"]);
	label_cell($myrow["prv_ob_2"]);
	label_cell($myrow["prv_ob_3"]);
	$type="SA_PREVENTVIEW";
	label_cell(print_document_link($myrow['prevent_id'], _('Print'), true, $type,ICON_PRINT));
	
	end_row();
 
	$i++;
}

end_table(2);
div_end();
//------------------------------------------------------------------------------------------------

end_page();

?>
