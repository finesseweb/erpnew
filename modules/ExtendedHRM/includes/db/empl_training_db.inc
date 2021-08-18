<?php
/****************************************
/*  Author 	: Kvvaradha
/*  Module 	: Extended HRM
/*  E-mail 	: admin@kvcodes.com
/*  Version : 1.0
/*  Http 	: www.kvcodes.com
*****************************************/

function add_empl_training($empl_id, $training_desc, $course, $cost, $institute, $s_date, $e_date, $notes) {
	
	$sd = date2sql($s_date);
	$ed = date2sql($e_date);

	$sql = "INSERT INTO ".TB_PREF."kv_empl_training (empl_id, training_desc, course, cost, institute, s_date, e_date, notes)
		VALUES ("
		  .db_escape($empl_id) . ", "
		  .db_escape($training_desc) . ", "
		  .db_escape($course) . ", "
		  .db_escape($cost) . ", "
		  .db_escape($institute) . ", "
		  .db_escape($sd) . ", "
		  .db_escape($ed) . ", "
		  .db_escape($notes) 
		.")";

	begin_transaction();
	
	$ret = db_query($sql, "Can't insert employee training details");
	
	commit_transaction();
	return $ret;
}

function update_empl_training($empl_id, $training_desc, $course, $cost, $institute, $s_date, $e_date, $notes, $id) {
	
	$sd = date2sql($s_date);
	$ed = date2sql($e_date);
	
	$sql = "UPDATE ".TB_PREF."kv_empl_training SET "
		  ."empl_id=".db_escape($empl_id) . ", "
		  ."training_desc=".db_escape($training_desc) . ", "
		  ."course=".db_escape($course) . ", "
		  ."cost=".db_escape($cost) . ", "
		  ."institute=".db_escape($institute) . ", "
		  ."s_date=".db_escape($sd) . ", "
		  ."e_date=".db_escape($ed) . ", "
		  ."notes=".db_escape($notes) 
		  ."WHERE id = ".db_escape($id);

	begin_transaction();

	// display_error($sql);die;
	$ret = db_query($sql, "Can't update employee training Details");
	
	commit_transaction();
	return $empl_id;
}

function delete_empl_training($person, $with_trainings=false){
	begin_transaction();
	
	$sql = "DELETE FROM ".TB_PREF."kv_empl_training WHERE id=".db_escape($person);
	$ret = db_query($sql, "Can't delete employee training");

	commit_transaction();
	return $ret;
}

/*
	Retrieve full training data from database for selected type/action/entity or training
*/
function get_empl_training_single($id)
{
	$kv_id = (int)$id ;
	$sql = "SELECT * FROM "	.TB_PREF."kv_empl_training WHERE id=".db_escape($kv_id);

	$training = array();
	$ret = db_query($sql, "Can't get employee  trainings");
	
	while($cont = db_fetch_row($ret)) 
		$training[] = $cont;
	//echo json_encode($cont);
	return $training;
}

function get_empl_training($id)
{
	$sql = "SELECT * FROM ".TB_PREF."kv_empl_training WHERE id=".db_escape($id);

	$res = db_query($sql, "Can't get employee trainings");

	$person = db_fetch($res);
	$person['trainings'] = get_person_trainings($id);

	return $person;
}

/*
	Returns all trainings for given person id
*/
function get_person_trainings($id)
{
	//$kv_id = (int)$id ;
	$sql = "SELECT * FROM "	.TB_PREF."kv_empl_training WHERE empl_id=".db_escape($id);


	$trainings = array();
	$ret = db_query($sql, "Can't get employee trainings");
	
	while($cont = db_fetch_row($ret))
		$trainings[] = $cont;
	
	return $trainings;
}


function delete_entity_trainings($class, $entity)
{
	delete_empl_trainings(null, $class, $entity);
	// cleanup
	$res = get_empl_training($class, null, $entity, null, true);
	while($person = db_fetch($res)) {
		$rels = get_person_trainings($person['id']);
		if (count($rels) == 0) {
			delete_empl_person($person['id']);
		}
	}
}

?>