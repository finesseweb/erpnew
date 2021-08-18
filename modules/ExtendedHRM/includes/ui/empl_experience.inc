<?php
/**********************************************************************
 AuthoR : Kvvaradha
 
 Module : HRM 
 
 Developed to 1Stop WebSolution.com
 
***********************************************************************/
include_once($path_to_root. '/includes/ui/simple_crud_class.inc');
include_once($path_to_root . "/modules/ExtendedHRM/includes/db/empl_experience_db.inc");
include_once($path_to_root . "/includes/date_functions.inc");
	
 	if(get_post('_name_of_org_changed')){
	
	    $regex = "/[^ ][ A-Za-z\.]$/";
		if(preg_match($regex, get_post('name_of_org')) ==0) {
			display_error( _("Accept only alphabets in organization name."));
			set_focus('name_of_org');	
		} 
	}  
		
	if(get_post('_job_role_changed')){
	
	    $regex = "/[^ ][ A-Za-z\.]$/";
		if(preg_match($regex, get_post('job_role')) ==0) {
			display_error( _("Accept only alphabets in job role."));
			set_focus('job_role');
			
		} 
	}
	
	if(get_post('_job_position_changed')){
	
	    $regex = "/[^ ][ A-Za-z\.]$/";
		if(preg_match($regex, get_post('job_position')) ==0) {
			display_error( _("Accept only alphabets in position."));
			set_focus('job_position');
		   //return false;
		} 
	}
	
	if(get_post('_monthly_sal_changed')){
	
	    $regex = "/[^ ][ a-zA-Z0-9\.]$/";	
		
		if(preg_match($regex, get_post('monthly_sal')) != 0) {
			
		} else{	
			display_error( _("Special Characters are not allowed in monthly salary."));
			set_focus('monthly_sal');
			return false;
		}	
	}
 
class experience extends simple_crud {

	var $selected_id;
	var $entity;
	var $sub_class;
	var $class;
	

	function __construct($name, $id, $class, $subclass=null) {
		$fields = array('name_of_org','job_role','job_position','nature_of_work','type_employment','monthly_sal','start_date', 'end_date','experience');

		//$this->simple_crud($name, $fields);
                 parent::__construct($name, $fields);
		$this->class = $class;
		$this->subclass = $subclass;
		$this->entity = $id;
			
	}

	function list_view() {
		br();

		$experience = get_person_experiences($this->entity);
			
		start_table(TABLESTYLE, "width=80%");

		$th = array( _("Organization"), _("Job Role"), _("Position"), _("Nature of Work"), _("Type of Employment"), _("Monthly Salary Drawn in INR (CTC)"), _("Start Date"), _("End Date"), _("Experience (In Months)"), "", "");	

		table_header($th);
		
		foreach($experience as $de) {
				
			label_cell($de[2]);					
			label_cell($de[3]);
			label_cell($de[4]);
			label_cell($de[5]);
			label_cell($de[6]);
			label_cell($de[7]);
			label_cell(sql2date($de[8]));
			label_cell(sql2date($de[9]));
			label_cell($de[10]);
					
			edit_button_cell("{$this->name}Edit[{$de[0]}]", _("Edit"));

			// delete button removes only category unless this is the last experience for this experience
			
			delete_button_cell("{$this->name}Delete[{$de[0]}]", _("Delete"));
			
			//submit_js_confirm({$this->name}."Delete[".{$de[0]}."]", sprintf(_("You are about to delete a Department Do you want to continue?"), $de[0]));
			//display_error($Mode);
			
			//display_error({$this->name}Delete[{$de[0]}]);
			end_row();
					
		}
		end_table(1);
	}

	function editor_view(){
	
	br();

		$experience = get_person_experiences($this->entity);
			
		start_table(TABLESTYLE, "width=80%");

		$th = array( _("Organization"), _("Job Role"), _("Position"), _("Nature of Work"), _("Type of Employment"), _("Monthly Salary Drawn in INR (CTC)"), _("Start Date"), _("End Date"), _("Experience (In Months)"), "", "");	

		table_header($th);
		
		foreach($experience as $de1) {
				
			label_cell($de1[2]);					
			label_cell($de1[3]);
			label_cell($de1[4]);
			label_cell($de1[5]);
			label_cell($de1[6]);
			label_cell($de1[7]);
			label_cell(sql2date($de1[8]));
			label_cell(sql2date($de1[9]));
			label_cell($de1[10]);
					
			edit_button_cell("{$this->name}Edit[{$de1[0]}]", _("Edit"));

			// delete button removes only category unless this is the last experience for this experience
			
			delete_button_cell("{$this->name}Delete[{$de1[0]}]", _("Delete"));
			
			//submit_js_confirm({$this->name}."Delete[".{$de[0]}."]", sprintf(_("You are about to delete a Department Do you want to continue?"), $de[0]));
			//display_error($Mode);
			
			//display_error({$this->name}Delete[{$de[0]}]);
			end_row();
					
		}
		end_table(1);
		
	         
		br();
		
		if($this->selected_id != null || $this->selected_id != "") {
			$degre = get_empl_experience_single($this->selected_id);
		
			foreach($degre as $de) {
			
				start_outer_table(TABLESTYLE);
				$dat = date('d-m-Y', strtotime($de[10])); 
				table_section(1);
				table_section_title(_("Experience Data"));
		text_row(_("Organization :*"), 'name_of_org', $de[2], 35, 40,null,'','',true);
				text_row(_("Job Role:*"), 'job_role', $de[3], 35, 40,null,'','',true);
			text_row(_("Position :*"), 'job_position', $de[4], 35, 40,null,'','',true);
				text_row(_("Nature of Work :"), 'nature_of_work', $de[5], 35, 40);
				text_row(_("Type of Employment :"), 'type_employment', $de[6], 35, 40);
				text_row(_("Monthly Salary Drawn in INR (CTC) :*"), 'monthly_sal', $de[7], 35, 40,null,'','',true);
				$_POST['start_date']=sql2date($de[8]);
				$_POST['end_date']=sql2date($de[9]);
				
date_row1(_("Start Date:"),'start_date',$_POST['start_date'],null,0,0,0,null,false,true);
 date_row1(_("End Date:"), 'end_date',$_POST['end_date'],null,0,0,0,null,false,true);
				
				text_row(_("Experience (In Months) :"), 'experience', $de[10], 35, 40,null,'','',false,true);	
				end_outer_table(1);	
			
			}
		}
		
		else {
		
			div_start('month_data');
			start_outer_table(TABLESTYLE);

			table_section(1);
			
			table_section_title(_("experience data"));

	text_row(_("Organization Name :*"), 'name_of_org', $de[2], 35, 40,null,'','',true);
	
			text_row(_("Job Role:*"), 'job_role', $de[3], 35, 40,null,'','',true);
			text_row(_("Position :*"), 'job_position', $de[4], 35, 40,null,'','',true);
			text_row(_("Nature of Work :"), 'nature_of_work', $de[5], 35, 40);
			text_row(_("Type of Employment :"), 'type_employment', $de[6], 35, 40);
			text_row(_("Monthly Salary Drawn in INR (CTC) :*"), 'monthly_sal', $de[7], 35, 40,null,'','',true);
			
		date_row(_("Start Date:"),'start_date',$de[8],null,0,0,0,null,false,true);		
		date_row(_("End Date:"), 'end_date', $de[9],null,0,0,0,null,false,true);
		
		
			//$diff_mon=date_diff2($_POST['start_date'],$_POST['end_date'],'m');
			//display_error($diff_mon);
		
			//$month  = floor(($diff – $years * (365*60*60*24)) / (30*60*60*24));
             $_POST['month']=5;
			text_row(_("Experience (In Months) :"), 'experience',$de[10], 35, 40,null,'','',false,true);
			
			end_outer_table(1);
          	div_end();
			
		}	
     end_form();
	}  
	  
	function _bottom_controls()	{
		$title=false;
		$async='both';
		$base=$this->name;

		$cancel = $async;

		if ($async === 'both') {
			$async = 'default'; $cancel = 'cancel';
		} 
		else if ($async === 'default')
			$cancel = true;
		else if ($async === 'cancel')
			$async = true;
		echo "<center>";

		if ($this->Mode == '' || $this->Mode == 'RESET')
			submit("{$base}NEW", _("Add new"), true, $title, $async);
		else {
		
			if ($this->Mode == 'NEW' || $this->selected_id==$this->_none)
				
				submit("{$base}ADD", _("Add"), true, $title, $async);
		else if($this->Mode == 'Delete' || $this->selected_id==$this->_none)
				submit("{$base}Proceed[{$this->selected_id}]", _("Proceed"), true, $title, $async);
			else {
				submit("{$base}UPDATE[{$this->selected_id}]", _("Update"), true, _('Submit changes'), $async);				
			}
			submit("{$base}RESET", _("Cancel"), true, _('Cancel edition'), $cancel);
		}
		echo "</center>";
	}
	
	function db_insert() {
		$dat = $this->data;
			
		return add_empl_experience($this->entity, $dat['name_of_org'], $dat['job_role'],$dat['job_position'], $dat['nature_of_work'], $dat['type_employment'], $dat['monthly_sal'] ,$dat['start_date'], $dat['end_date'],$dat['experience']);
	}

	function db_update() {	
		return update_empl_experience($this->entity, $_POST['name_of_org'], $_POST['job_role'], $_POST['job_position'], $_POST['nature_of_work'], $_POST['type_employment'], $_POST['monthly_sal'], $_POST['start_date'], $_POST['end_date'],$_POST['experience'] ,$this->selected_id, $this->class);
	}

	function insert_check() {
	
		if (strlen($_POST['name_of_org']) == 0) {
			display_error(_("The Organization cannot be empty."));
			set_focus('name_of_org');
			return false;
		}  
		
		 if(!empty($_POST['name_of_org'])){
	
			$regex = "/[^ ][ A-Za-z\.]$/";
			if(preg_match($regex, $_POST['name_of_org']) ==0) {
				display_error( _("Accept only alphabets in position.."));
				set_focus('name_of_org');
				return false;
			} 	
		}  
		
		if(!empty($_POST['job_role'])){
	
			$regex = "/[^ ][ A-Za-z\.]$/";
			if(preg_match($regex, $_POST['job_role']) ==0) {
				display_error( _("Accept only alphabets in position."));
				set_focus('job_role');
				return false;
			} 	
		}
		
		if (strlen($_POST['job_role']) == 0) {
			display_error(_("Employee Job role cannot be empty."));
			set_focus('job_role');
			return false;
		}
		
		if (strlen($_POST['job_position']) == 0) {
			display_error(_("Employee Position cannot be empty."));
			set_focus('job_position');
			return false;
		}
		
		if(!empty($_POST['job_position'])){
	
			$regex = "/[^ ][ A-Za-z\.]$/";
			if(preg_match($regex, $_POST['job_position']) ==0) {
				display_error( _("Accept only alphabets in position.."));
				set_focus('job_position');
				return false;
			} 
		} 
		
		if (strlen($_POST['monthly_sal']) == 0) {
			display_error(_("Employee monthly salary cannot be empty."));
			set_focus('monthly_sal');
			return false;
		}
		if(!empty($_POST['monthly_sal'])){
	
			$regex = "/[^ ][ a-zA-Z0-9\.]$/";	
			if(preg_match($regex, $_POST['monthly_sal']) != 0) {
			} else{	
				display_error( _("Special Characters are not allowed in monthly salary."));
				set_focus('monthly_sal');
				return false;
			}	
		} 
	
		if (count(@$_POST['start_date']) == 0) {
			display_error(_("Select Employee career start up date."));
			set_focus('start_date');
			return false;
		}
	
		if ($_POST['end_date'] < $_POST['start_date']) {
			display_error(_("End date should be greater than Start date."));
			$_POST['end_date']=today();
		//	display_error($_POST['end_date']); 
			set_focus('end_date');
			return false;
		}
		
		$today_date = date2sql(today());
		$end_date = date2sql($_POST['end_date']);
		
		if($end_date > $today_date){
			display_error(_("End date should not be greater than current date"));
			$_POST['end_date'] = today();
			set_focus('end_date');
			return false;
		}
		
		return true; 
	}

	function db_read() {
		return get_empl_experience($this->entity);
	}

	function delete_check() {
	display_error("Are you sure to delete the record");
		//return true;
	}
	
	function db_delete() {
		if (delete_empl_experience($this->selected_id, $this->class, $this->entity)) {
			
		}
		return false;
	}
	
}
?>


