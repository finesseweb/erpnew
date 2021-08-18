<?php
$page_security = 'SA_ROUTEDETAIL';
if (!@$_GET['popup'])
	$path_to_root = "..";
else	
	$path_to_root = "../..";

include($path_to_root . "/includes/db_pager.inc");
include($path_to_root . "/includes/session.inc");
include($path_to_root . "/sales/includes/db/credit_status_db.inc");
include_once($path_to_root . "/includes/date_functions.inc");
include_once($path_to_root . "/includes/ui.inc");
include_once($path_to_root . "/includes/data_checks.inc");
include_once($path_to_root . "/transportation/function/function.php");

include($path_to_root . "/modules/ExtendedHRM/includes/Payroll.inc" );

$js='';
if ($use_date_picker)
	$js .= get_js_date_picker();

page(_($help_context = "Route Config Master"), @$_REQUEST['popup'], false, "", $js);


simple_page_mode(true);
?>
<?php

//===[Working]=====//
start_form();

start_table(TABLESTYLE_NOBORDER);
start_row();
route_list_cells(_("Select a Route: "), 'route', null,	true, true);
vehicle_list_detail(_("Select a Vehicle: "), 'vehicle', null,true, true,$_POST['route']);
stop_list_detail(_("Select a Stop: "), 'stop', null,true, true,$_POST['route']);
//stopList();

submit_cells('Search', _("Search"), '', '', 'default');
end_row();

end_table();
end_form();

if($_POST['route']){
    totalstudent($_POST['route'],$_POST['stop'],$_POST['vehicle']);
}

if(!isset($_POST['stop_page_next']) && !isset($_POST['stop_page_last']))
$_SESSION['fun']['id'] = 0;
start_form(true);

$sql = get_transportResult($_POST['route'],$_POST['vehicle'],$_POST['stop']);
$cols = array(
            _("#") => array('align' => 'center','fun' => 'id'),
            _("Transport ID") => array('align' => 'center'),
            _("Route") => array('align' => 'center'),
            _("Source") => array('align' => 'center'),
            _("Destination") => array('align' => 'center'),
            _("Vehicle No.") => array('align' => 'center'),
            _("Reg. No.") => array('align' => 'center'),
            _("Driver Name") => array('align' => 'center'),
            _("Stop No.") => array('align' => 'center','fun'=>'stop_name'),
            _("Sequence ") => array('align' => 'center'),
            _("Expected Pickup Time") => array('align' => 'center','fun'=>'exp_time'),
            _("Expected Drop Time") => array('align' => 'center','fun'=>'drop_time'),
            _("No. of Student") => array('align' => 'center','fun'=>'no_student'),
            _("Edit") => array('align' => 'center', 'fun' => 'edit'),
);
$table = &new_db_pager('stop', $sql, $cols);

$table->width = "80%";
display_db_pager($table);
start_table(TABLESTYLE, "width=40%");
inactive_control_column($th);
table_header($th);

$k = 0;

$nos = db_num_rows($result);

function id($row) {
    
    return ++$_SESSION['fun']['id'] ;
}
function route_name($row) {
	
	return $row['route_name'];
}
function stop_name($row) {
    
	$stopNameSql ="SELECT stop_name FROM ".TB_PREF."stop where id=".db_escape($row['stop']);
	$stopNameResult=db_query($stopNameSql);
	$stopName=db_fetch($stopNameResult);
	return $stopName[0];
}
function sequence($row) {

	return $row['sequence'];
}
function exp_time($row) {
	$Pickup=date('h:i A',strtotime($row['exp_time']));
	return $Pickup;
}
function drop_time($row) {
	$droptime=date('h:i A',strtotime($row['drop_time']));
	return $droptime;
}

function cost($row) {
	
	return $row['cost'];
}
function no_student($row) {
	
	return NoOfStudentCount($row['route_name'],$row['stop'],$row['vehicle_no']);
}
function status($row) {
	if($row['status']==1){
		$status='Active';
	}elseif($row['status']==2){
		$status='Deactive';
	}
    return $status;
}

function edit($row) {

   // return edit_button_cell("Edit" . $row['id'], _("Edit"));
	$link='transportation_edit.php?config='.$row["trans_id"];
	$link2="<a href='$link'>Edit</a>";
    return  $link2;
}

end_table();
echo '<br>';


//-----------------------------------------------------------------------------------

start_table(TABLESTYLE2);


end_page();
?>
<script type="text/javascript">
	$('#_route_sel').change(function(){
		var route = $('#_route_sel option:selected').val();
		$.ajax({
		type: "POST",
		url: "/finesse-erp/transportation/ajax.php", 
		data: { 
		route_name: route  
		},
		success : function(data){
			
			 helpers2.buildDropdown(

                    jQuery.parseJSON(data),

                    $('#stop'),

                    'Select an option'

                );

		}
		
		});
	});

	var helpers2 =

{

    buildDropdown: function(result, dropdown, emptyMessage)

    {

        // Remove current options

        dropdown.html('');

        // Add the empty option with the empty message

        dropdown.append('<option value="">' + emptyMessage + '</option>');

        // Check result isnt empty

        if(result != '')

        {

            // Loop through each of the results and append the option to the dropdown

            $.each(result, function(k, v) {

                dropdown.append('<option value="' + v.id + '">' + v.name + '</option>');

            });

        }

    }

}
</script>
<style>
	.total_student {
		float: right;
		font-size: 19px;
		padding: 18px;
	}
</style>

