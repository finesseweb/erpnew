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
	if ($type == 'Process')
		$viewer .= "maintain_process_view.php?id=".$help_id;
	
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
	label_cell($myrow["helpdesk_date"]);
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
	helpdeskstatus_list_cell(_(""), 'status'.$i.'', null, false, false); 
	
	//display_error($_POST['status']);
	 submit_cells('buton'.$i.'', _("Submit"),"style='padding-left:20px;'",_('Select Approved'), 'ICON_SUBMIT');
	
	/* else if($_POST['status'] ==2){
	display_error('two');
	if(list_updated('status')){
	
	  $Ajax->activate('status1');
	}
	submit_cells('status1', _("Submit"),"style='padding-left:20px;'",_('Select Approved'), 'ICON_SUBMIT');
	} */
?><input type="hidden" name="upd" id="upd" class="upd"/>



<?php 
   if(get_post('status1')){
     
	 display_error($_POST['status']);
	 
       
		
	    update_helpdesk_status($myrow["help_id"],$_POST['status']);
			
   }

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
?>

<input type="hidden" name="counts" id="counts" value="<?php echo $i; ?>" />
<?php 
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
</body>
</html>
<script type="text/javascript">
/* var count_val = $('#counts').val();
//alert(count_val);
for(var ct=1;ct < count_val;ct++){
$('body').on('click','#status1'+ct+'',function(){
//$('#status1'+ct+'').click(function(){
alert('dsfdsf');
var submit = $('#status1'+ct+'').val();
//alert(ct);
//alert(document.getElementsByClassName("combo"));
 var stat = document.getElementsByName("status").length;
 alert(stat);
//alert($('#status1'+ct+''));
//var obj=document.getElementsByName("status");
//var vals=$('select[name="' + status + '"] option:selected').val();
//alert(vals);
//var obj=$('select[name="' + status + '"] option:selected').val(); 
//alert(obj);
 for(var i=1;i<stat;i++){
 var obj=document.getElementsByName("status").item(i);
//if (status[i].checked) {
  //    alert ("The " + (i + 1) + ". radio button is checked");
//}
alert(obj.value);
} 
$('.upd').val(obj.value);
});
} */
var count_val = $('#counts').val();
//for(var ct=1;ct < count_val;ct++){
     $("select[name=status1]").change(function(){
	// alert(ct);
	   var st = $("select[name=status1] option:selected").val();
	   //alert(st);
    });
//}

</script>