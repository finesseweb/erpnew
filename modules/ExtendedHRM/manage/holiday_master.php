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

$page_security = 'SA_OPEN';
$path_to_root="../../..";
include_once($path_to_root . "/includes/date_functions.inc");
include_once($path_to_root . "/includes/ui.inc");
include($path_to_root . "/includes/session.inc"); 
$js = '';
///display_error($version_id['version_id']);
if ($version_id['version_id'] == '2.4.1') {
     
    if ($SysPrefs->use_popup_windows)
        $js .= get_js_open_window(900, 500);

   if (user_use_date_picker()) 
      
     $js .= get_js_date_picker();
}else {
     
    if ($SysPrefs->use_popup_windows)
        $js .= get_js_open_window(900, 500);

   if (user_use_date_picker()) 
      
     $js .= get_js_date_picker();
}



//page(_($help_context = "Holiday Master")); 

// include($path_to_root . "/sales/includes/db/credit_status_db.inc");
//include($path_to_root . "/modules/ExtendedHRM/includes/ui/kv_departments.inc" );
include_once($path_to_root . "/includes/date_functions.inc");
include_once($path_to_root . "/includes/ui.inc");
include_once($path_to_root . "/includes/data_checks.inc");
include($path_to_root . "/modules/ExtendedHRM/includes/Payroll.inc" );
include($path_to_root . "/includes/ui.inc"); 
page(_($help_context = "Holiday Master"), @$_REQUEST['popup'], false, "", $js); 
simple_page_mode(true);
if(get_post('_name_changed')){
	
	    $regex = "/[^ ][ A-Za-z\.]$/";
		if(preg_match($regex, get_post('name')) ==0) {	
			display_error( _("Accept only Alphabets."));	
			set_focus('name');
		} 
}
 ?>
<html lang="en">
 <head>
    <link rel="stylesheet" href="<?php echo $path_to_root . "/modules/ExtendedHRM/js/jquery-ui.css" ?>">
    <script src="<?php echo $path_to_root . "/modules/ExtendedHRM/js/jquery-1.10.2.js" ?>"></script>
   <script src="<?php echo $path_to_root . "/modules/ExtendedHRM/js/jquery-ui.js"?>"></script>
</head>
 
 </html>
<?php 

function can_process() 
{
		
	
	
	if (strlen($_POST['name']) == 0) 
	{
		
		display_error(_("Name cannot be empty."));
		set_focus('name');
		return false;
	}	
	if(!empty($_POST['name'])){
	    $regex = "/[^ ][ A-Za-z\.]$/";
		if(preg_match($regex, get_post('name')) ==0) {
			display_error( _("Accept Only Alphabets."));
			set_focus('name');
			return false;
		} 
	}
	
	$fiscal_year = get_fiscalyear($_POST['fisc_year']);
    $financial_year_start_month = $fiscal_year['1'];
    $financial_year_end_month = $fiscal_year['2'];
	
	if(!(date2sql($_POST['from_date']) >= $financial_year_start_month))
	{
				display_error("Invalid From Date");
				$_POST['from_date']=today();
				set_focus('to_date');
				return false;
	}
	
	if(!(date2sql($_POST['to_date']) <= $financial_year_end_month)){
	
			display_error("Invalid To Date");	
			$_POST['to_date']=today();
			set_focus('to_date');
	return false;
	}
	
	 if (date2sql($_POST['to_date']) < date2sql($_POST['from_date'])) {
			display_error(_("Invalid To date."));
			$_POST['to_date']=today();
			set_focus('to_date');
			return false;
	} 
		
	
		
	return true;
}

//-----------------------------------------------------------------------------------

if ($Mode=='ADD_ITEM'  && can_process()) 
{
	
	$sql="SELECT from_date,to_date FROM ".TB_PREF."kv_holiday_master WHERE  from_date >=".db_escape(date2sql($_POST['from_date']))." AND  to_date <=".db_escape(date2sql($_POST['to_date']));
	$result=db_query($sql);
	
	if(db_num_rows($result) !=0){
		display_error('Already record exists on selected dates');
		//return false;
	}
	else{
		
	
	
	add_holiday($_POST['fisc_year'],$_POST['name'],$_POST['descpt'],date2sql($_POST['from_date']),date2sql($_POST['to_date']));
	display_notification(_('Holiday has been added'));
	$Mode = 'RESET';
	}
	
} 

//-----------------------------------------------------------------------------------

if ($Mode=='UPDATE_ITEM' && can_process() ) 
{
	//display_error("sdff");die;
	update_holiday($selected_id,$_POST['fisc_year'],$_POST['name'],$_POST['descpt'],date2sql($_POST['from_date']),date2sql($_POST['to_date']));
	$Mode = 'RESET';
	display_notification(_('Holiday has been updated'));
} 

//-----------------------------------------------------------------------------------

if ($Mode == 'Delete')
{		
		delete_holiday($selected_id);
		$Mode = 'RESET';
}

if ($Mode == 'RESET')
{
	$selected_id = -1;
	$sav = get_post('show_inactive');
	unset($_POST);
	$_POST['show_inactive'] = $sav;
} ?>

<?php 
//-----------------------------------------------------------------------------------

$result = get_holidays(check_value('show_inactive'));
//display_error($result);die;
start_form();
start_table(TABLESTYLE);
$th = array(_("Fiscal Year"),_("Name"),_("Description"),_("From Date"),_("To Date"),'','',);
inactive_control_column($th);
table_header($th);

$k = 0;

$nos=db_num_rows($result);
if($nos !=0){
	
while($myrow = db_fetch($result)) 
{
	
	alt_table_row_color($k);	
	label_cell(sql2date($myrow["f_styear"]).'-'.sql2date($myrow["f_endyear"]));
	label_cell($myrow["name"]);
	label_cell($myrow["descpt"]);
	label_cell(sql2date($myrow["from_date"]));
	label_cell(sql2date($myrow["to_date"]));
	
    inactive_control_cell($myrow["holiday_id"],$myrow["inactive"],'kv_holiday_master
    ', 'holiday_id');
 	edit_button_cell("Edit".$myrow['holiday_id'], _("Edit"));
 	delete_button_cell("Delete".$myrow['holiday_id'], _("Delete"));
	submit_js_confirm("Delete".$myrow["holiday_id"], sprintf(_("You are about to delete a Holiday Do you want to continue?"), $myrow['holiday_id']));
	end_row();
 }

}else{
	label_cell('No Records Found','colspan=7 align="center" size="15"');
}

inactive_control_row($th);
end_table();
echo '<br>';

start_table(TABLESTYLE2);
if ($selected_id != -1) 
{
	if ($Mode == 'Edit') {
	$myrow = get_holiday_edit($selected_id);
		$_POST['fisc_year']  = $myrow["fisc_year"];
		$_POST['name']  = $myrow["name"];
		$_POST['descpt']  = $myrow["descpt"];
		$_POST['from_date']  = sql2date($myrow["from_date"]);
		$_POST['to_date']  = sql2date($myrow["to_date"]);
	}
		
		hidden('selected_id', $selected_id);
		
}
kv_fiscalyears_list_cells(_("Fiscal Year:"), 'fisc_year', null, true);
text_row_ex(_("Name :*"), 'name', 30,null,null,null,null,null,true);
//text_row_ex(_("Name :*"), 'leave_type', 30,null,null,null,null,null,true);
textarea_row(_("Description:"), 'descpt',null, 27,4);
date_row(_("From Date") . ":", 'from_date', 20, null, '', '', '', null, true, "from_date");
date_row(_("To Date") . ":", 'to_date', 20, null, '', '', '', null, true, "to_date");
end_table(1);

submit_add_or_update_center($selected_id == -1, '', 'both');

 end_form(); 
 
 end_page();
  ?>
  <!-- <p style='padding: 3px;text-align:center;'> <a href='javascript:goBack();'>Back</a></p> --?
<script>
/*   
$(document).ready(function(){
	$('input[name=name]').keypress(function (e){
        var code =e.keyCode || e.which;
        if((code<65 || code>90)&&(code<97 || code>122)&&code!=32&&code!=46)  
        {
          alert("Only alphabets are allowed");
          return false;
        }
    }); 
}); */
</script>
<script>
/* $(document).ready(function(){
	
    $("body").delegate('input[name=from_date]', 'focusout', function(){
        var start_date = Date.parse($('input[name=from_date]').val());
		var end_date = Date.parse($('input[name=to_date]').val());
		if(start_date > end_date){
          alert("Invalid From Date");
          return false;
        }
    });
}); */
</script>

