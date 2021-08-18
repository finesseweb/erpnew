<?php

/****************************************
/*  Author 	: Kvvaradha
/*  Module 	: Extended HRM
/*  E-mail 	: admin@kvcodes.com
/*  Version : 1.0
/*  Http 	: www.kvcodes.com
*****************************************/


// Standard report classess
define('RC_EMPLOYEES', 8);
define('RC_ALLOWANCES', 9);
define('RC_KV_REPORTS', 10);

class BoxReports
{
	var $ar_classes; // report class names
	var $ar_reports;
	var $ctrl_handlers = array();

	function ReportClasses()
	{
		$this->ar_classes = array();
	}

	function addReportClass($class_name, $class_id=null)
	{
		if (!$class_id)
			$class_id = count($this->ar_classes);
		$this->ar_classes[$class_id] = $class_name;

		return $class_id;
	}

	function addReport($class, $id, $rep_name, $params=null)
	{
		unset($this->ar_reports[$class][$id]); // unset std report if any
		$this->ar_reports[$class][$id] = new Report($id, $rep_name, $params);
	}

	function getDisplay($class=null)
	{
		global $path_to_root, $pdf_debug, $Ajax;

		if (find_submit('Rep') != -1) {
			include($path_to_root.'/modules/ExtendedHRM/reports/prn_redirect.php');
			return;
		}

		$temp = array_values($this->ar_classes);
		$display_class = $class==null ? $temp[0] : $this->ar_classes[$class];
		$st_reports = "";
		$st_params = "";
		$st_classes = "<b>" . _("Report Classes:") . "</b><br>";

		if (isset($_GET['Class']))
			set_focus('class'.$_GET['Class']);

		$cur_class = $_REQUEST['Class'];

		foreach($this->ar_classes as $class_id => $name)
		{
			if (!isset($this->ar_reports[$class_id]))
				continue; // e.g. no dimensions

			$acc = access_string($name);
			$st_classes .= "<a href='"
				.$_SERVER['PHP_SELF']."?Class=$class_id'"
				." style='font-weight:". ($cur_class == $class_id ? 'bold' : 'normal')."'"
				." class='repclass_link' id='".'class'.$class_id."'"
				."$acc[1]>$acc[0]</a> <br>";

			$style = $class_id==$cur_class ? '' : "style='display:none'";
			$st_reports .= "<table class='repclass' id='TAB_" . $class_id ."' $style cellpadding='0' cellspacing='0' border='0' width='100%'><tr><td><b>" . _("Reports For Class: ") . "&nbsp;$name</b></td></tr>\n";
			foreach($this->ar_reports[$class_id] as $rep_id => $report)
			{
				$acc = access_string($report->name);
				$st_reports .= "<tr><td><a class='repopts_link'"
					." href='".$_SERVER['PHP_SELF']."?Class=$class_id&REP_ID=$report->id'"
					." style='font-weight:". (@$_REQUEST['REP_ID'] == $report->id ? 'bold' : 'normal')."'"
					." id='".$id = default_focus()."'"
					."$acc[1]>$acc[0]</a><tr><td>\n";

				if (@$_REQUEST['REP_ID'] == $report->id) {
					$Ajax->activate($id);
				}

				if (isset($_REQUEST['REP_ID']) && $_REQUEST['REP_ID']==$report->id) {
					// parameters form
					$action = $_SERVER['PHP_SELF'];
					$st_params = "<table><tr><td>\n"
						. "<form method='POST' action='$action' target='_blank'>\n";
					$st_params .= submit('Rep'.$report->id,  
						_("Display: ") . access_string($report->name, true),
						false, '', $pdf_debug ? false : 'default process') . hidden('REP_ID', $report->id, false).'<br><br>';
					$st_params .= $this->getOptions($report->get_controls(), $report->id);
					$st_params .= "\n<input type=hidden name='Class' value=".$cur_class.">"
						."\n</form></td></tr></table>\n";
					set_focus('Rep'.$report->id);
					$Ajax->addUpdate(true, 'rep_form', $st_params);
				}
			}
			$st_reports .= "</table>";
		}

		$st_params = "<div id='rep_form'>".
			"$st_params</div>";
		
		$st = "<table align='center' width='80%' style='border:1px solid #cccccc;'><tr valign='top'>";
		$st .= "<td width='30%'>$st_classes</td>";
		$st .= "<td width='35%' style='border-left:1px solid #cccccc;border-right:1px solid #cccccc;padding-left:3px;'>$st_reports</td>";
		$st .= "<td width='35%'>$st_params</td>";
		$st .= "</tr></table><br>";

		return $st;
	}

	function getOptions($controls, $id = 0)
	{
		$st = '';
		if ($controls == null)
			return "";

		$cnt = 0;
		foreach($controls as $title => $type)
		{
			$ctrl = '';
			foreach($this->ctrl_handlers as $fun) { // first check for non-standard controls
				$ctrl = $fun('PARAM_'.$cnt, $type);
				if ($ctrl) break;
			}
			if ($ctrl == '') {
				if (($id == 102 || $id == 202) && $cnt == 3) // set default Show Also Allocated to Yes in aged reports 2014-09-19 Joe Hunt
					$_POST['PARAM_'.$cnt] = 1;
				
				if (isset($_COOKIE['select'][$id][$cnt])) // saved parameters 2010-10-06 Joe Hunt
					$_POST['PARAM_'.$cnt] = $_COOKIE['select'][$id][$cnt];
				$ctrl = $this->get_ctrl('PARAM_'.$cnt, $type);
			}
			if ($ctrl != '')  {
				$st .= $title . ':<br>';
				$st .= $ctrl;
				$st .= "<br><br>";
			} else {
				display_error(_('Unknown report parameter type:').$type);
			}
			$cnt++;
		}
		return $st;
	}

	//
	//	Register additional control handler
	// $handle - name of global function f($name, $type) returning html code for control
	function register_controls($handler) {
		$this->ctrl_handlers[] = $handler;
	}
	//
	//	Returns html code for input control $name of type $type 
	//
	function get_ctrl($name, $type)
	{
		global $path_to_root, $use_date_picker, $pdf_debug, $print_invoice_no,
			$def_print_destination, $def_print_orientation, $type_shortcuts;

		$st = '';
			switch ($type)
			{
				case 'CURRENCY':
					$sql = "SELECT curr_abrev, concat(curr_abrev,' - ', currency) AS name FROM ".TB_PREF."currencies";
					return combo_input($name, '', $sql, 'curr_abrev', 'name',array('spec_option'=>_("No Currency Filter"),'spec_id'=>ALL_TEXT,'order'=>false));

				case 'DATE':
				case 'DATEBEGIN':
				case 'DATEEND':
				case 'DATEBEGINM':
				case 'DATEENDM':
				case 'DATEBEGINTAX':
				case 'DATEENDTAX':
					if (!isset($_POST[$name]))
					{
						if ($type == 'DATEBEGIN')
							$date = begin_fiscalyear();
						elseif ($type == 'DATEEND')
							$date = end_fiscalyear();
						else
							$date = Today();
						if ($type == 'DATEBEGINM')
							$date = begin_month($date);
						elseif ($type == 'DATEENDM')
							$date = end_month($date);
						elseif ($type == 'DATEBEGINTAX' || $type == 'DATEENDTAX')
						{
							$row = get_company_prefs();
							$edate = add_months($date, -$row['tax_last']);
							$edate = end_month($edate);
							if ($type == 'DATEENDTAX')
								$date = $edate;
							else
							{
								$bdate = begin_month($edate);
								$bdate = add_months($bdate, -$row['tax_prd'] + 1);
								$date = $bdate;
							}
						}
					}
					else // saved parameters 2010-10-16 Joe Hunt
						$date = $_POST[$name];
					$st = "<input type='text' name='$name' value='$date'>";
					if ($use_date_picker)
					{
						$calc_image = (file_exists("$path_to_root/themes/".user_theme()."/images/cal.gif")) ? 
							"$path_to_root/themes/".user_theme()."/images/cal.gif" : "$path_to_root/themes/default/images/cal.gif";
						$st .= "<a href=\"javascript:date_picker(document.forms[0].$name);\">"
						. "	<img src='$calc_image' style='vertical-align:middle;padding-bottom:4px;width:16px;height:16px;border:0;' alt='"._('Click Here to Pick up the date')."'></a>\n";
					}	
					return $st;
					break;

				case 'YES_NO':
					return yesno_list($name);

				case 'PAYMENT_LINK':
					return payment_services($name, null);

				case 'DESTINATION':
					$sel = array(_("PDF/Printer"), "Excel");
					$def = 0;
					if (isset($def_print_destination) && $def_print_destination == 1)
						$def = 1;
					return array_selector($name, $def, $sel);

				case 'ORIENTATION':
					$sel = array(_("Portrait"), _("Landscape"));
					$def = 0;
					if (isset($def_print_orientation) && $def_print_orientation == 1)
						$def = 1;
					return array_selector($name, $def, $sel);

				case 'COMPARE':
					$sel = array(_("Accumulated"), _("Period Y-1"), _("Budget"));
					return array_selector($name, null, $sel);

				case 'GRAPHIC':
					$sel = array(_("No Graphics"), _("Vertical bars"), _("Horizontal bars"), 
								_("Dots"), _("Lines"), _("Pie"), _("Donut"));
					return array_selector($name, null, $sel);

				case 'SYS_TYPES':
					return gl_systypes_list($name, null, _("No Type Filter"));

				case 'SYS_TYPES_ALL':
					return systypes_list($name, null, _("No Type Filter"));

				case 'TEXT':
					return "<input type='text' name='$name'>";

				case 'KV_HIDDEN':
					$value = (isset($_POST['allowanc_id']) ? $_POST['allowanc_id'] : "");
					return "<input type='hidden' name='$name' value='$value'>";

				case 'TEXTBOX':
					$value = (isset($_POST[$name]) ? $_POST[$name] : "");
					return "<textarea rows=4 cols=30 name='$name'>$value</textarea>";

				case 'ACCOUNTS': // not used
					return gl_account_types_list($name, null, _("No Account Group Filter"), true);

				case 'ACCOUNTS_NO_FILTER': // not used
					return gl_account_types_list($name);

				case 'GL_ACCOUNTS':
					return gl_all_accounts_list($name);

				case 'BANK_ACCOUNTS':
					return bank_accounts_list($name);
				
				case 'KV_TRANS_YEARS':
					return kv_fiscalyears_list($name, null, true);
					
				case 'KV_EMPLOYEES':
					return employee_list($name);

				case 'MONTHS':
					return kv_current_fiscal_months($name);

				case 'DEPARTMENTS':
					return department_list($name);
	
				case 'USERS':
					$sql = "SELECT id, user_id FROM ".TB_PREF."users";
					return combo_input($name, '', $sql, 'id', 'user_id',array('spec_option'=>_("No Users Filter"),'spec_id'=>ALL_NUMERIC));

				case 'ACCOUNTTAGS':
				case 'DIMENSIONTAGS':
					if ($type == 'ACCOUNTTAGS')
						$tag_type = TAG_ACCOUNT;
					else
						$tag_type = TAG_DIMENSION;
					//return tag_list($name, 5, $tag_type, true, false, _("No tags"));
					return tag_list($name, 5, $tag_type, true);

			}
		return '';
	}
}

class Report
{
	var $id;
	var $name;
	var $ar_params;
	var $controls;
	
	function __construct($id, $name, $ar_params = null)
	{
		$this->id = $id;
		$this->name = $name;
		if ($ar_params) $this->set_controls($ar_params);
	}
	
	function set_controls($ar_params) {
		$this->controls = $ar_params;
	}
	
	function get_controls() {
		return $this->controls;
	}
	
}

//------------------------------------------------------------------------------------------------
function gl_systypes_list($name, $value=null, $spec_opt=false)
{
	global $systypes_array;
	
	$types = $systypes_array;
	
	foreach(array(ST_LOCTRANSFER, ST_PURCHORDER, ST_SUPPRECEIVE, ST_MANUISSUE,
				ST_MANURECEIVE, ST_SALESORDER, ST_SALESQUOTE, ST_DIMENSION) as $type)
		unset($types[$type]);

	return array_selector($name, $value, $types, 
		array( 
			'spec_option'=> $spec_opt,
			'spec_id' => ALL_NUMERIC,
			'async' => false,
			)
	);
}
/*
	Add extension/custom reports to report manager.
*/
function add_custom_reports(&$reports)
{
	global $installed_extensions, $path_to_root;
	
	// include reports installed inside extension modules
	if (count($installed_extensions) > 0)
	{
		$extensions = $installed_extensions;
		foreach ($extensions as $ext)
			if (($ext['active'] && $ext['type'] == 'extension')) {
				$file = $path_to_root.'/'.$ext['path']."/reporting/reports_custom.php";
				if (file_exists($file)) {
					set_ext_domain($ext['path']);
					include_once($file);
					set_ext_domain();
				}
			}
	}
	$file = company_path()."/reporting/reports_custom.php";
	if (file_exists($file))
		include_once($file);
}

?>