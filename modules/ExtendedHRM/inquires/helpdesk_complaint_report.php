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
$page_security = 'SA_HELPDESKVIEW';
$path_to_root="../..";
include_once($path_to_root . "/includes/session.inc");

include_once($path_to_root . "/includes/date_functions.inc");
include_once($path_to_root . "/includes/ui.inc");
include_once($path_to_root . "/includes/data_checks.inc");
include_once($path_to_root . "/reporting/includes/reporting.inc");
include_once($path_to_root . "/reporting/includes/excel_report.inc");
include($path_to_root . "/maintenance/includes/db/helpdesk_db.inc");
//include_once($path_to_root . "/maintenance/manage/add_preventive_maintenance.php");

$js = "";
if ($use_popup_windows)
	$js .= get_js_open_window(800, 500);
if ($use_date_picker)
	$js .= get_js_date_picker();
page(_($help_context = "Help Desk Complaint Report"), isset($_GET['bank_account']), false, "", $js); ?>
<html lang="en">
<head>
    <link rel="stylesheet" href="<?php echo $path_to_root . "/modules/ExtendedHRM/js/jquery-ui.css" ?>">
    <script src="<?php echo $path_to_root . "/modules/ExtendedHRM/js/jquery-1.10.2.js" ?>"></script>
   <script src="<?php echo $path_to_root . "/modules/ExtendedHRM/js/jquery-ui.js"?>"></script>
  <script>
  $( function() {
    $( "#datepicker" ).datepicker();
  } );
  </script>
</head>
<body>
<?php 
//check_db_has_bank_accounts(_("There are no bank accounts defined in the system."));

//-----------------------------------------------------------------------------------
// Ajax updates
//
if (get_post('Show'))
{
	$Ajax->activate('trans_tbl');
}

//------------------------------------------------------------------------------------------------
$query = "SELECT desk.*, CASE WHEN(desk.emp_id !=0)  THEN  emp.empl_firstname  WHEN (desk.stu_name !='') THEN desk.stu_name END AS name,m_dept.name AS maintain_dept FROM ".TB_PREF."maintenance_help_desk AS desk LEFT JOIN ".TB_PREF."kv_empl_info AS emp ON  desk.emp_id=emp.id LEFT JOIN ".TB_PREF."maintenance_department AS m_dept ON m_dept.id=desk.maintain_dept_id";
$res=db_query($query);
//$result=db_fetch($res);

if(list_updated('status')){
//display_error($_POST['status']);
	$Ajax->activate('trans_tbl');
}
div_start('trans_tbl');

start_table(TABLESTYLE);

$th = array(_("Reference"), _("Date"),
	_("Category"), _("Name"), _("Maintenance Department"), _("Issues"), _("Status"),_(""));
table_header($th);


function get_trans($help_id,$type)
{
	$label = $help_id;
	$class ='';
	$id=$help_id;
	$icon = '';
	 $viewer = $path_to_root."maintenance/view/";
	if ($type == 'Help Desk')
		$viewer .= "helpdesk_view.php?id=".$help_id;
	
	return viewer_link($label, $viewer, $class, $id,  $icon);
	
}

$k = 0; 
$i=1; //row colour counter
while ($myrow = db_fetch($res))
{

	alt_table_row_color($k);

	//$trandate = sql2date($myrow["prevent_id"]);
	label_cell(get_trans($myrow["help_id"],'Help Desk'));
	//label_cell(get_trans_view_str($myrow["type"],$myrow["trans_no"],$myrow['ref']));
	label_cell(dates2sql($myrow["helpdesk_date"]));
	if ($myrow["category"] ==1)
	{
		label_cell('Employee');
	}
	else{
		label_cell('Student');
	}
	label_cell($myrow["name"]);
	label_cell($myrow["maintain_dept"]);
	label_cell($myrow["issues"]);
	//helpdeskstatus_list_cell(_(""), 'status'.$i.'', null, false, false); 
	
	//display_error($_POST['status']);
	// submit_cells('buton'.$i.'', _("Submit"),"style='padding-left:20px;'",_('Select Approved'), 'ICON_SUBMIT');
	
?>

<?php $sql ="SELECT id,status FROM ".TB_PREF."helpdesk_status";

      $res1= db_query($sql); 
	  
 ?>
<td>
<?php if($myrow['status'] !=2){ ?>
<select name="status<?php echo $i;?>" id="status<?php echo $i;?>">
    <option value="">Select</option>
	<?php while($result = db_fetch($res1)){
	  ?>  
   <option value="<?php echo $result['id']; ?>"><?php echo $result['status']; ?></option>
   <?php } ?>
</select>
<td><input type="button" name="approved" value="Submit" id="approved" style="padding:4px;margin-right:8px;" onclick="select_approved(<?php echo $myrow['help_id'];?>,<?php echo $i;?>)" /></td>
<?php } else{ 
   echo 'Completed';
?>
<td> <?php echo 'Submitted';?></td>
<?php } ?>
</td>

<?php 

//	label_cell("<a href=".$path_to_root."/reporting/prn_redirect.php?xls=1&&PARAM_0=4&PARAM_1=4&PARAM_2=0&PARAM_3=&PARAM_4=0&REP_ID=603>EXCEl</a>");
	$rep = new FrontReport(_('Year of Holidays: '.$year_of_selection[0]), "Holidays", user_pagesize(), 9, $orientation);
	    
	    if ($orientation == 'L')
	    	recalculate_cols($cols);
			
	    $rep->Font();
	    $rep->Info(null, $cols, $headers, $aligns);
	    $rep->NewPage();

	end_row();

	$i++;
}

end_table(2);
div_end();

end_page();

?>
</body>
</html>
<script type="text/javascript">
function select_approved(help_id,num){
	var help_id = help_id;
	//alert(help_id);
	var status = $('#status'+num+'').val();
	//alert(status);
	 $.ajax({ 
			type: "POST",
			url:'<?php echo $path_to_root . "/maintenance/manage/ajax_update_status.php";?>',
			data: {help_id:help_id,status:status}
		}).done(function( data ) { 
			window.location.reload();
		});
	
}
</script>