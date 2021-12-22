<?php
/**********************************************************************
  
	Released underhe GNU General Public License, GPL, 
	as published by the Free Software Foundation, either version 3 
	of the License, or (at your option) any later version.
    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  
    See the License here <http://www.gnu.org/licenses/gpl-3.0.html>.
***********************************************************************/
function add_sub_category($selected, $sub_cat_name, $description, $category_id,$slab_id,$effective_date,$code)
{
    $date= date('Y-m-d',strtotime($effective_date));
    if($selected!='')
		$sql = "UPDATE ".TB_PREF."stock_sub_category SET sub_cat_name = ".db_escape($sub_cat_name).", description = ".db_escape($description).", category_id = ".db_escape($category_id).", slab_id = ".db_escape($slab_id).", effective_date = ".db_escape($date).", code = ".db_escape($code)."
    	WHERE sub_cat_name = ".db_escape($selected);
    else
		$sql = "INSERT INTO ".TB_PREF."stock_sub_category
			(sub_cat_name, description, category_id,slab_id,effective_date,code) VALUES( ".db_escape($sub_cat_name).",
	  		".db_escape($description).",".db_escape($category_id).",".db_escape($slab_id).",".db_escape($date).",".db_escape($code).")";

	db_query($sql,"sub category could not be updated");
}

function delete_sub_category($sub_cat_name)
{
	$sql="DELETE FROM ".TB_PREF."stock_sub_category WHERE sub_cat_name=".db_escape($sub_cat_name);

	db_query($sql,"sub category could not be deleted");
}

function get_sub_category($sub_cat_name)
{

	$sql="SELECT * FROM ".TB_PREF."stock_sub_category WHERE sub_cat_name=".db_escape($sub_cat_name);

	$result = db_query($sql,"sub category could not be retrieved");

	return db_fetch($result);
}
function get_category($cat_id)
{

	$sql = "SELECT category_id, description, inactive FROM ".TB_PREF."stock_category where category_id =".db_escape($cat_id);

	$result = db_query($sql,"category could not be retrieved");

	return db_fetch($result);
}


function get_sub_code($sub_cat_name)
{

	$sql="SELECT * FROM ".TB_PREF."stock_sub_category WHERE sub_cat_id=".db_escape($sub_cat_name);

	$result = db_query($sql,"sub category could not be retrieved");

	return db_fetch($result);
}
function get_sub_category_desc($sub_cat_name)
{
	$sql = "SELECT description FROM ".TB_PREF."stock_sub_category WHERE sub_cat_id=".db_escape($sub_cat_name);

	$result = db_query($sql, "could not unit description");

	$row = db_fetch_row($result);
	return $row[0];
}

function sub_cat_used($sub_cat_name) {
	$sql= "SELECT COUNT(*) FROM ".TB_PREF."stock_master WHERE sub_cat_name=".db_escape($sub_cat_name);
	$result = db_query($sql, "could not query stock master");
	$myrow = db_fetch_row($result);
	return ($myrow[0] > 0);
}

function get_all_sub_categories($all=false) {
    $sql = "SELECT *  FROM ".TB_PREF."stock_sub_category ";
	if (!$all) $sql .= " WHERE !inactive";
	$sql .= " ORDER BY sub_cat_name";
	
    return  db_query($sql, "could not get stock categories");
}
// 2008-06-15. Added Joe Hunt to get a measure of unit by given stock_id


?>