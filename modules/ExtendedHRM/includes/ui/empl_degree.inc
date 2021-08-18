<?php
/**********************************************************************
 AuthoR : Kvvaradha
 
 Module : HRM 
 
 Developed to 1Stop WebSolution.com
 
***********************************************************************/
include_once($path_to_root. '/includes/ui/simple_crud_class.inc');
include_once($path_to_root . "/modules/ExtendedHRM/includes/db/empl_degree_db.inc");

if(get_post('_degree_changed')){
	
	    $regex = "/[^ ][ A-Za-z0-9\.]$/";
		if(preg_match($regex, get_post('degree')) ==0) {
			display_error( _("Special Characters are not allowed in degree."));
			set_focus('degree');
			return false;
		} 	
} 

if(get_post('_university_changed')){
	
	    $regex = "/[^ ][ A-Za-z0-9\.]$/";
		
		if(preg_match($regex, get_post('university')) ==0) {
			display_error( _("Special Characters are not allowed in university."));
			set_focus('university');
			return false;
		} 
} 

class degree extends simple_crud {
	var $selected_id;
	var $entity;
	var $sub_class;
	var $class;
	
	function __construct($name, $id, $class, $subclass=null) {
		$fields = array('empl_id','degree','major','university','grade', 'year_of_passing');

		//$this->simple_crud($name, $fields);
                parent::__construct($name, $fields);
		$this->class = $class;
		$this->subclass = $subclass;
		$this->entity = $id;
		
	}
	
	
   
	function list_view() {
		 br();

		$degree = get_person_degrees($this->entity);
			
		start_table(TABLESTYLE, "width=80%");

		$th = array(_('Degree'), _("Major Subject"), _("University"), _("Grade/ Percentage"), _("Year of Passing"), "", "");	

		table_header($th);
		
		foreach($degree as $de) {
				
			label_cell($de[2]);					
			label_cell($de[3]);
			label_cell($de[4]);
			label_cell($de[5]);
			label_cell(sql2date($de[6]));
					
			edit_button_cell("{$this->name}Edit[{$de[0]}]", _("Edit"));

			// delete button removes only category unless this is the last degree for this degree
		//	display_error($de[0]); die;
			
			delete_button_cell("{$this->name}Delete[{$de[0]}]", _("Delete"));
			
			
			
			end_row();
					
		}
		end_table(1); 
		
	}
	
	function editor_view(){
	
	
	br();

		$degree = get_person_degrees($this->entity);
			
		start_table(TABLESTYLE, "width=80%");

		$th = array(_('Degree'), _("Major Subject"), _("University"), _("Grade/ Percentage"), _("Year of Passing"), "", "");	

		table_header($th);
		
		foreach($degree as $de1) {
				
			label_cell($de1[2]);					
			label_cell($de1[3]);
			label_cell($de1[4]);
			label_cell($de1[5]);
			label_cell(sql2date($de1[6]));
					
			edit_button_cell("{$this->name}Edit[{$de1[0]}]", _("Edit"));

			// delete button removes only category unless this is the last degree for this degree
		//	display_error($de[0]); die;
			
			delete_button_cell("{$this->name}Delete[{$de1[0]}]", _("Delete"));
			
			
			
			end_row();
					
		}
		end_table(1);
	
		br();
		
		
		if($this->selected_id != null || $this->selected_id != "") {
			$degre = get_empl_degree_single($this->selected_id);
		
			//print_r($degre);
			foreach($degre as $de) {
				start_outer_table(TABLESTYLE);
				$dat = date('d-m-Y', strtotime($de[6])); 
				table_section(1);
				table_section_title(_("Degree data"));

				text_row(_("Degree :*"), 'degree', $de[2], 35, 40,null,'','',true);
				text_row(_("Major Subject :"), 'major', $de[3], 35, 40);
			   text_row(_("University:*"), 'university', $de[4], 35, 40,null,'','',true);
				text_row(_("Grade/ Percentage:"), 'grade', $de[5], 32, 30);
				date_row(_("Year of Passing:"), 'year_of_passing');
					
				end_outer_table(1);	
			
			}
		}
		else {
			start_outer_table(TABLESTYLE);

			table_section(1);
			table_section_title(_("Degree data"));

			text_row(_("Degree :*"), 'degree', $degre[2], 35, 40,null,'','',true);
			text_row(_("Major Subject :"), 'major', $degre[3], 35, 40);
			text_row(_("University:*"), 'university', @$this->data['university'], 35, 40,null,'','',true);
			text_row(_("Grade/ Percentage:"), 'grade', @$this->data['grade'], 32, 30);
			date_row(_("Year of Passing:"), 'year_of_passing', @$this->data['year_of_passing']);
			
			end_outer_table(1);
		}
	}

	function _bottom_controls()
	{
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
			
		return add_empl_degree($this->entity, $dat['degree'], $dat['major'], $dat['university'], $dat['grade'], $dat['year_of_passing']);
	}

	function db_update() {
	//echo $this->selected_id; 
		return update_empl_degree($this->entity, $_POST['degree'], $_POST['major'], $_POST['university'], $_POST['grade'], $_POST['year_of_passing'], $this->selected_id, $this->class);
	}
	
		
	function insert_check() {
		if (strlen($_POST['degree']) == 0) {
			display_error(_("The degree name cannot be empty."));
			set_focus('degree');
			return false;
		}
		
		if(!empty($_POST['degree'])){
			$regex = "/[^ ][ A-Za-z0-9\.]$/";
			
			if(preg_match($regex, $_POST['degree']) ==0) {
				display_error( _("Special Characters are not allowed in degree."));
				set_focus('degree');
				return false;
			} 	
		} 
	
		if (strlen($_POST['university']) == 0) {
			display_error(_("University cannot be empty."));
			set_focus('university');
			return false;
		}
		
		if(!empty($_POST['university'])){
			$regex = "/[^ ][ A-Za-z0-9\.]$/";
			
			if(preg_match($regex, $_POST['university']) == 0) {	
				display_error( _("Special Characters are not allowed in university."));
				set_focus('university');
				return false;
			}	
		}
		
		if (count(@$_POST['grade']) == 0) {
			display_error(_("You have to enter either Grade or Percentage here."));
			set_focus('grade');
			return false;
		}
		
		if ($_POST['grade'] > 100) {
			display_error(_("You have to enter Grade Less Than 100 Percentage here."));
			set_focus('grade');
			return false;
		}
		
		$today_date = date2sql(today());
		$yrp_pass = date2sql($_POST['year_of_passing']);
		
		if($yrp_pass > $today_date){
			display_error(_("Year of passing date should not be greater than current date"));
			$_POST['year_of_passing'] = today();
			set_focus('year_of_passing');
			return false;
		}	
		
	$yearr= $_POST['year_of_passing'];	
    $arr = explode("/", $yearr);
	list($month, $day, $year) = $arr;
		
	//	display_error($year);die;
	if (date('Y') < $year) {
			display_error(_("You have to enter Passing of Year Not Greater Than to current year here."));
			set_focus('year_of_passing');
			return false;
		} 
		
		return true; 
	}

		
	function db_read() {
		return get_empl_degree($this->entity);
	}
  function delete_check() {
	display_error(_("Are you sure to delete the record"));
		//return true;
	}
	
	//
	//	Delete all degree for degree in current class/entity
	//
	function db_delete() {
		if (delete_empl_degree($this->selected_id, $this->class, $this->entity)) {
			
		}
		return false;
	}
}

?>