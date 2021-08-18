<?php
/****************************************
/*  Author 	: Kvvaradha
/*  Module 	: Extended HRM
/*  E-mail 	: admin@kvcodes.com
/*  Version : 1.0
/*  Http 	: www.kvcodes.com
*****************************************/

function add_empl_degree($empl_id, $degree, $major, $university, $grade, $year) {
	
	$yr = date2sql($year);
	$sql = "INSERT INTO ".TB_PREF."kv_empl_degree (empl_id, degree, major, university, grade, year)
		VALUES ("
		  .db_escape($empl_id) . ", "
		  .db_escape($degree) . ", "
		  .db_escape($major) . ", "
		  .db_escape($university) . ", "
		  .db_escape($grade) . ", "
		  .db_escape($yr) 
		.")";

	begin_transaction();

	$ret = db_query($sql, "Can't insert empl degree");
	
	commit_transaction();
	return $ret;
}

function update_empl_degree($empl_id, $degree, $major, $university, $grade, $year, $id) {
	
	$yr = date2sql($year);
	
	$sql = "UPDATE ".TB_PREF."kv_empl_degree SET "
		  ."empl_id=".db_escape($empl_id) . ", "
		  ."degree=".db_escape($degree) . ", "
		  ."major=".db_escape($major) . ", "
		  ."university=".db_escape($university) . ", "
		  ."grade=".db_escape($grade) . ", "
		  ."year=".db_escape($yr)
		  ."WHERE id = ".db_escape($id);

	begin_transaction();

	$ret = db_query($sql, "Can't update empl degree");
	if ($ret) {
		//if(!update_person_degrees($id, $cat_ids, $entity, $type))
		//	return null;
	}
	commit_transaction();
	return $empl_id;
}

function delete_empl_degree($person, $with_degrees=false)
{
	begin_transaction();

	//if ($with_degrees) {
	//	$sql = "DELETE FROM ".TB_PREF."kv_empl_degree WHERE id=".db_escape($person);
	//	db_query($sql, "Can't delete empl degrees");
//	}
	$sql = "DELETE FROM ".TB_PREF."kv_empl_degree WHERE id=".db_escape($person);
	$ret = db_query($sql, "Can't delete empl degree");

	commit_transaction();
	return $ret;
}
/*
	Retrieve full degree data from database for selected type/action/entity or degree
*/
function get_empl_degree_single($id)
{
	$kv_id = (int)$id ;
	$sql = "SELECT * FROM "	.TB_PREF."kv_empl_degree WHERE id=".db_escape($kv_id);

	$degree = array();
	$ret = db_query($sql, "Can't get empl person degrees");
	
	while($cont = db_fetch_row($ret)) 
		$degree[] = $cont;
	//echo json_encode($cont);
	return $degree;
}

function get_empl_degree($id)
{
	$sql = "SELECT * FROM ".TB_PREF."kv_empl_degree WHERE id=".db_escape($id);

	$res = db_query($sql, "Can't get empl degrees");

	$person = db_fetch($res);
	$person['degrees'] = get_person_degrees($id);

	return $person;
}

/*
	Returns all degrees for given person id
*/
function get_person_degrees($id)
{
	//$kv_id = (int)$id ;
	$sql = "SELECT * FROM "	.TB_PREF."kv_empl_degree WHERE empl_id=".db_escape($id);


	$degrees = array();
	$ret = db_query($sql, "Can't get empl person degrees");
	
	while($cont = db_fetch_row($ret))
		$degrees[] = $cont;
	
	return $degrees;
}


function delete_entity_degrees($class, $entity){
	delete_empl_degrees(null, $class, $entity);
	// cleanup
	$res = get_empl_degree($class, null, $entity, null, true);
	while($person = db_fetch($res)) {
		$rels = get_person_degrees($person['id']);
		if (count($rels) == 0) {
			delete_empl_person($person['id']);
		}
	}
}



?>