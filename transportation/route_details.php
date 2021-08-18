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
//if ($use_date_picker)
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

submit_cells('Search', _("Search"), '', '', 'default');
end_row();

end_table();
end_form();



if(!isset($_POST['stop_page_next']) && !isset($_POST['stop_page_last']))
$_SESSION['fun']['id'] = 0;
start_form(true);
//$sql = get_routeResult($_POST['route']);
$sql = get_routeResult1($_POST['route']);
$cols = array(
            _("#") => array('align' => 'center','fun' => 'id'),
           // _("Config ID") => array('align' => 'center'),
            _("Route Name") => array('align' => 'center','fun' => 'route_name'),
            _("Stop Name") => array('align' => 'center', 'fun'=>'stop_name'),
            _("Sequence") => array('align' => 'center','fun'=>'sequence'),
            _("Pickup Time") => array('align' => 'center','fun'=>'exp_time'),
            _("Drop Time") => array('align' => 'center','fun'=>'drop_time'),
            _("Cost(Rs.)") => array('align' => 'center','fun'=>'cost'),
            _("Status") => array('align' => 'center','fun'=>'status'),
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
    
	$stopNameSql ="SELECT stop_name FROM ".TB_PREF."stop where id=".db_escape($row['s_name']);
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
	$link='route_configurationdetails.php?config='.$row["config_id"];
	$link2="<a href='$link'>Edit</a>";
    return  $link2;
}

end_table();
echo '<br>';


//-----------------------------------------------------------------------------------

start_table(TABLESTYLE2);


end_page();
?>


