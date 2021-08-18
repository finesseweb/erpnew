<?php
/****************************************
/*  Author 	: Kvvaradha
/*  Module 	: Extended HRM
/*  E-mail 	: admin@kvcodes.com
/*  Version : 1.0
/*  Http 	: www.kvcodes.com
*****************************************/

function add_empl_experience($empl_id, $organization, $job_role,$job_position,$nature_of_work,$type_employment,$monthly_sal,$s_date, $e_date,$experience) {
	
	$sd = date2sql($s_date);
	$ed = date2sql($e_date);
//	echo $empl_id."_". $organization."_".$job_role."_".$s_date."_".$e_date ; 
	$sql = "INSERT INTO ".TB_PREF."kv_empl_experience (empl_id, organization, job_role,job_position,nature_of_work,type_employment,monthly_sal,s_date, e_date,experience)
		VALUES ("
		  .db_escape($empl_id) . ", "
		  .db_escape($organization) . ", "
		  .db_escape($job_role) . ", "
		  .db_escape($job_position) . ", "
		  .db_escape($nature_of_work) . ", "
		  .db_escape($type_employment) . ", "
		  .db_escape($monthly_sal) . ", "
		  .db_escape($sd) . ", "
		  .db_escape($ed) .", "
		  .db_escape($experience) . ")";

	begin_transaction();
	
	$ret = db_query($sql, "Can't insert employee experience");
	
	commit_transaction();
	return $ret;
}

function update_empl_experience($empl_id, $organization, $job_role,$job_position,$nature_of_work,$type_employment,$monthly_sal, $s_date, $e_date,$experience,$id) {
	
	$sd = date2sql($s_date);
	$ed = date2sql($e_date);
	
	$sql = "UPDATE ".TB_PREF."kv_empl_experience SET "
		  ."empl_id=".db_escape($empl_id) . ", "
		  ."organization=".db_escape($organization) . ", "
		  ."job_role=".db_escape($job_role) . ", "
		  ."job_position=".db_escape($job_position) . ", "
		  ."nature_of_work=".db_escape($nature_of_work) . ", "
		  ."type_employment=".db_escape($type_employment) . ", "
		  ."monthly_sal=".db_escape($monthly_sal) . ", "
		  ."s_date=".db_escape($sd) . ", "
		  ."e_date=".db_escape($ed).","."experience=".db_escape($experience)."  
		  WHERE id = ".db_escape($id);

	begin_transaction();

	$ret = db_query($sql, "Can't update employee experience");
	
	commit_transaction();
	return $empl_id;
}

function delete_empl_experience($person, $with_experiences=false){
	begin_transaction();
	
	$sql = "DELETE FROM ".TB_PREF."kv_empl_experience WHERE id=".db_escape($person);
	$ret = db_query($sql, "Can't delete employee experience");

	commit_transaction();
	return $ret;
}

/*
	Retrieve full experience data from database for selected type/action/entity or experience
*/
function get_empl_experience_single($id)
{
	$kv_id = (int)$id ;
	$sql = "SELECT * FROM "	.TB_PREF."kv_empl_experience WHERE id=".db_escape($kv_id);

	$experience = array();
	$ret = db_query($sql, "Can't get employee  experiences");
	
	while($cont = db_fetch_row($ret)) 
		$experience[] = $cont;
	//echo json_encode($cont);
	return $experience;
}

function get_empl_experience($id)
{
	$sql = "SELECT * FROM ".TB_PREF."kv_empl_experience WHERE id=".db_escape($id);

	$res = db_query($sql, "Can't get employee experiences");

	$person = db_fetch($res);
	$person['experiences'] = get_person_experiences($id);

	return $person;
}

/*
	Returns all experiences for given person id
*/
function get_person_experiences($id)
{
	//$kv_id = (int)$id ;
	$sql = "SELECT * FROM "	.TB_PREF."kv_empl_experience WHERE empl_id=".db_escape($id);

//display_error($sql);
	$experiences = array();
	$ret = db_query($sql, "Can't get employee experiences");
	
	while($cont = db_fetch_row($ret))
		$experiences[] = $cont;
	
	return $experiences;
}


function delete_entity_experiences($class, $entity)
{
	delete_empl_experiences(null, $class, $entity);
	// cleanup
	$res = get_empl_experience($class, null, $entity, null, true);
	while($person = db_fetch($res)) {
		$rels = get_person_experiences($person['id']);
		if (count($rels) == 0) {
			delete_empl_person($person['id']);
		}
	}
}

?>