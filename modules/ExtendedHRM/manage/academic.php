<?php 

$page_security = 'SA_OPEN';
$path_to_root = "../../..";
include($path_to_root . "/includes/session.inc");
include($path_to_root . "/modules/ExtendedHRM/includes/Payroll.inc" );
//include($path_to_root . "/modules/ExtendedHRM/includes/Payroll.inc" );
include($path_to_root . "/tally/includes/ui/tally.inc" );
include($path_to_root . "/includes/ui.inc");

include_once($path_to_root . "/includes/date_functions.inc");
include_once($path_to_root . "/includes/data_checks.inc");


$version_id = get_company_prefs('version_id');

$js = '';
if ($version_id['version_id'] == '2.4.1') {
    if ($SysPrefs->use_popup_windows)
        $js .= get_js_open_window(900, 500);

    if (user_use_date_picker())
        $js .= get_js_date_picker();
}else {
    if ($use_popup_windows)
        $js .= get_js_open_window(900, 500);
    if ($use_date_picker)
        $js .= get_js_date_picker();
}


page(_($help_context = "Employees"), @$_REQUEST['popup'], false, "", $js);

$_POST['erp_sessions'] = $_SESSION['wa_current_user'];
$perfs = $_POST['erp_sessions']->prefs;
$statutory = $_POST['erp_sessions']->statutory;
$role_set = $_POST['erp_sessions']->role_set;
//echo "<pre>";print_r($_POST['erp_sessions']);exit;

global $acad;

echo "<form method='POST' name='myForm' target ='_blank' id='myForm' action = '$acad'>";
foreach($_POST['erp_sessions'] as $key => $value){
    if($key != 'statutory' && $key != 'prefs' && $key != 'role_set'){
          echo "<input type='hidden' name='".$key."' size= '255' value =". $value.">";
    }
}

foreach($role_set as $key => $value){
     echo "<input type='hidden' name='roles[]' size= '255' value =". $value.">";
}
  
echo "</form>";
///sdie();
?>
<!--<script type="text/javascript">
    window.onload=function(){
        var auto = setTimeout(function(){ submitform(); }, 1);

        function submitform(){
            clearTimeout(auto);
          document.forms["myForm"].submit();
          
        }

       
    }
</script>-->