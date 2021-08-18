<?php
/**********************************************************************
 AuthoR : Kvvaradha
 
 Module : HRM 
 
 Developed to 1Stop WebSolution.com
 
***********************************************************************/
include_once($path_to_root. '/includes/ui/simple_crud_class.inc');
include_once($path_to_root . "/modules/ExtendedHRM/includes/db/empl_attendance_db.inc");

/*
	View/Edit class for leave
*/
class leave extends simple_crud {
	var $id;
	var $entity;
	var $sub_class;
	var $class;
	
	function __construct($name, $id, $class, $subclass=null) {
		$fields = array('#','applied_date','leave_type','start_date', 'end_date', 'no_of_leave','reason',	'status', 'assgn' =>array('fld'=>'leave')
						);

		$this->simple_crud($name, $fields);
		$this->class = $class;
		$this->subclass = $subclass;
		$this->entity = $id;
	}

	function list_view() {
		br();

			$leave = get_crm_persons($this->class, $this->subclass, $this->entity);
			//				check_value('show_inactive'));
			start_table(TABLESTYLE, "width=80%");

			$th = array(_('#'), _("Applied Date"), _("Leave Type"), _("Start Date"), _("End Date"), _("Number of Days"),	_("Reason"), _("Status"), '',		"&nbsp;");
			//	inactive_control_column($th);

			table_header($th);
			$k = 0; 

			$last = $myrow = db_fetch($leave);
			while ($last)		{
					
				if ($myrow['id'] != $last['id']) {
					alt_table_row_color($k);
					label_cell(implode('<br>',$description));
					label_cell($last["#"]);					
					label_cell($last["name_of_org"]);
					label_cell($last["university"]);
					label_cell($last["year_of_passing"]);
					//email_cell($last["email"]);
		//	inactive_control_cell($myrow["id"], $myrow["inactive"], 'crm_leave', 'id');

				 	edit_button_cell("{$this->name}Edit[{$last['id']}]", _("Edit"));

			// delete button removes only category unless this is the last leave for this person
					delete_button_cell("{$this->name}Delete[{$last['id']}]", _("Delete"));
					end_row();
					$description = array();
					$last = $myrow;
				}

				if ($myrow) {
					$description[] = $myrow['description'];
					$myrow = db_fetch($leave);
				}
			}

//inactive_control_row($th);
			end_table(1);
	}
			
	
	//
	//	Delete all leave for person in current class/entity
	//
	
}

?>